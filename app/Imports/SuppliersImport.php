<?php

namespace App\Imports;

use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class SuppliersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    protected $site_id;
    protected $tenant_id;
    protected $successCount = 0;
    protected $failCount = 0;

    public function __construct()
    {
        $user = Auth::user();
        $this->site_id = $user->site->id ?? null;
        $this->tenant_id = $user->getCurrentTenant()?->id ?? $user->site->tenant_id ?? null;
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
            $name = trim($row['name'] ?? $row['supplier_name'] ?? '');
            $address = trim($row['address'] ?? '');
            $location = trim($row['location'] ?? '');
            $tel = trim($row['tel'] ?? $row['telephone'] ?? '');
            $phone = trim($row['phone'] ?? $row['mobile'] ?? '');
            $email = trim($row['email'] ?? '');
            $itemsSupplied = trim($row['items_supplied'] ?? '');
            $contactPerson = trim($row['contact_person'] ?? '');
            $primaryCurrency = trim($row['primary_currency'] ?? $row['currency'] ?? '');
            $compRegNo = trim($row['comp_reg_no'] ?? $row['company_registration_number'] ?? '');
            $vatRegNo = trim($row['vat_reg_no'] ?? $row['vat_registration_number'] ?? '');
            $itemCat1 = trim($row['item_cat1'] ?? $row['item_category_1'] ?? '');
            $itemCat2 = trim($row['item_cat2'] ?? $row['item_category_2'] ?? '');
            $itemCat3 = trim($row['item_cat3'] ?? $row['item_category_3'] ?? '');

            if (empty($name)) {
                $this->failCount++;
                return null;
            }

            // Check if supplier with same name already exists (for same site)
            $existingSupplier = Supplier::where('name', $name)
                ->where('site_id', $this->site_id)
                ->first();
            
            if ($existingSupplier) {
                $this->failCount++;
                return null; // Skip duplicate
            }

            $this->successCount++;

            return new Supplier([
                'name' => $name,
                'address' => $address ?: null,
                'location' => $location ?: null,
                'tel' => $tel ?: null,
                'phone' => $phone ?: null,
                'email' => $email ?: null,
                'items_supplied' => $itemsSupplied ?: null,
                'contact_person' => $contactPerson ?: null,
                'primary_currency' => $primaryCurrency ?: null,
                'comp_reg_no' => $compRegNo ?: null,
                'vat_reg_no' => $vatRegNo ?: null,
                'item_cat1' => $itemCat1 ?: null,
                'item_cat2' => $itemCat2 ?: null,
                'item_cat3' => $itemCat3 ?: null,
                'site_id' => $this->site_id,
                'tenant_id' => $this->tenant_id,
            ]);
        } catch (\Exception $e) {
            Log::error('SuppliersImport | Error importing row', [
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
            'name' => 'required|string',
            'address' => 'nullable|string',
            'location' => 'nullable|string',
            'tel' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'items_supplied' => 'nullable|string',
            'contact_person' => 'nullable|string',
            'primary_currency' => 'nullable|string',
            'comp_reg_no' => 'nullable|string',
            'vat_reg_no' => 'nullable|string',
            'item_cat1' => 'nullable|string',
            'item_cat2' => 'nullable|string',
            'item_cat3' => 'nullable|string',
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
