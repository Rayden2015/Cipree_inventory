<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\Category;
use App\Models\Uom;
use App\Models\Site;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;

class ItemsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    protected $site_id;
    protected $added_by;
    protected $errors = [];
    protected $successCount = 0;
    protected $failCount = 0;

    public function __construct()
    {
        $this->site_id = Auth::user()->site->id ?? null;
        $this->added_by = Auth::id();
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            // Map CSV/Excel columns to database fields
            // Expected columns: item_description, item_part_number, category_name, uom_name, reorder_level, max_stock_level, lead_time_days, valuation_method
            $itemDescription = trim($row['item_description'] ?? $row['description'] ?? '');
            $itemPartNumber = trim($row['item_part_number'] ?? $row['part_number'] ?? '');
            $categoryName = trim($row['category_name'] ?? $row['category'] ?? '');
            $uomName = trim($row['uom_name'] ?? $row['uom'] ?? '');
            $reorderLevel = $row['reorder_level'] ?? $row['reorder_point'] ?? 0;
            $maxStockLevel = $row['max_stock_level'] ?? $row['maximum_stock_level'] ?? null;
            $leadTimeDays = $row['lead_time_days'] ?? $row['lead_time'] ?? null;
            $valuationMethod = trim($row['valuation_method'] ?? $row['valuation'] ?? '');

            if (empty($itemDescription)) {
                $this->failCount++;
                return null;
            }

            // Find or get category ID
            $category = Category::where('name', 'like', $categoryName)->first();
            if (!$category) {
                // Use first category as fallback, or skip
                $category = Category::first();
                if (!$category) {
                    $this->failCount++;
                    return null;
                }
            }

            // Find or get UOM ID
            $uom = Uom::where('name', 'like', $uomName)->orWhere('abbreviation', 'like', $uomName)->first();
            if (!$uom) {
                // Use first UOM as fallback
                $uom = Uom::first();
            }

            // Generate stock code
            $lastOrderId = Item::orderBy('id', 'desc')->value('id') ?? 0;
            $initials = substr($category->name, 0, 2);
            $stockCode = $initials . str_pad($lastOrderId + 1, 4, "0", STR_PAD_LEFT);

            // Check if item_part_number already exists (should be unique)
            if (!empty($itemPartNumber)) {
                $existingItem = Item::where('item_part_number', $itemPartNumber)->first();
                if ($existingItem) {
                    $this->failCount++;
                    return null; // Skip duplicate
                }
            }

            $this->successCount++;

            // Validate valuation_method if provided
            $validValuationMethods = ['FIFO', 'LIFO', 'Weighted Average'];
            $valuationMethodValue = null;
            if (!empty($valuationMethod) && in_array($valuationMethod, $validValuationMethods)) {
                $valuationMethodValue = $valuationMethod;
            }

            return new Item([
                'item_description' => $itemDescription,
                'item_part_number' => $itemPartNumber ?: null,
                'item_stock_code' => $stockCode,
                'item_category_id' => $category->id,
                'uom_id' => $uom->id ?? null,
                'item_uom' => $uomName,
                'reorder_level' => (int)$reorderLevel,
                'max_stock_level' => $maxStockLevel ? (int)$maxStockLevel : null,
                'lead_time_days' => $leadTimeDays ? (int)$leadTimeDays : null,
                'valuation_method' => $valuationMethodValue,
                'added_by' => $this->added_by,
                'site_id' => $this->site_id,
            ]);
        } catch (\Exception $e) {
            Log::error('ItemsImport | Error importing row', [
                'row' => $row,
                'error' => $e->getMessage(),
            ]);
            $this->failCount++;
            return null;
        }
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'item_description' => 'required|string',
            'category_name' => 'nullable|string',
            'uom_name' => 'nullable|string',
            'reorder_level' => 'nullable|integer|min:0',
            'max_stock_level' => 'nullable|integer|min:0',
            'lead_time_days' => 'nullable|integer|min:0',
            'valuation_method' => 'nullable|in:FIFO,LIFO,Weighted Average',
        ];
    }

    /**
     * Get import statistics
     */
    public function getStats()
    {
        return [
            'success' => $this->successCount,
            'failed' => $this->failCount,
            'errors' => $this->failures(),
        ];
    }
}
