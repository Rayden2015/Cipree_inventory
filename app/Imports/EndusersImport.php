<?php

namespace App\Imports;

use App\Models\Enduser;
use App\Models\Department;
use App\Models\Section;
use App\Models\EndUsersCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class EndusersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
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
            $nameDescription = trim($row['name_description'] ?? $row['name'] ?? $row['description'] ?? '');
            $assetStaffId = trim($row['asset_staff_id'] ?? $row['asset_id'] ?? '');
            $type = trim($row['type'] ?? '');
            $departmentName = trim($row['department_name'] ?? $row['department'] ?? '');
            $sectionName = trim($row['section_name'] ?? $row['section'] ?? '');
            $model = trim($row['model'] ?? '');
            $serialNumber = trim($row['serial_number'] ?? '');
            $manufacturer = trim($row['manufacturer'] ?? '');
            $designation = trim($row['designation'] ?? '');
            $status = trim($row['status'] ?? 'Active');
            $categoryName = trim($row['category_name'] ?? $row['category'] ?? '');

            // Required fields validation
            if (empty($nameDescription) || empty($type)) {
                $this->failCount++;
                return null;
            }

            // Find department by name (scoped to current tenant via TenantScope)
            $department = null;
            if (!empty($departmentName)) {
                $department = Department::where('name', 'like', $departmentName)
                    ->where('site_id', $this->site_id)
                    ->first();
                
                if (!$department) {
                    // Try to find by partial match
                    $department = Department::where('name', 'like', '%' . $departmentName . '%')
                        ->where('site_id', $this->site_id)
                        ->first();
                }
            }

            if (!$department) {
                $this->failCount++;
                return null; // Department is required
            }

            // Find section by name (scoped to current tenant via TenantScope)
            $section = null;
            if (!empty($sectionName)) {
                $section = Section::where('name', 'like', $sectionName)
                    ->where('site_id', $this->site_id)
                    ->first();
                
                if (!$section) {
                    // Try to find by partial match
                    $section = Section::where('name', 'like', '%' . $sectionName . '%')
                        ->where('site_id', $this->site_id)
                        ->first();
                }
            }

            if (!$section) {
                $this->failCount++;
                return null; // Section is required
            }

            // Find category if provided (optional)
            $category = null;
            if (!empty($categoryName)) {
                $category = EndUsersCategory::where('name', 'like', $categoryName)
                    ->where('site_id', $this->site_id)
                    ->first();
            }

            // Use name_description as name (required by schema)
            $name = $nameDescription;

            $this->successCount++;

            return new Enduser([
                'name' => $name,
                'asset_staff_id' => $assetStaffId ?: null,
                'name_description' => $nameDescription,
                'department' => $departmentName, // Keep string fields for backward compatibility
                'section' => $sectionName,
                'model' => $model ?: null,
                'serial_number' => $serialNumber ?: null,
                'manufacturer' => $manufacturer ?: null,
                'type' => $type,
                'designation' => $designation ?: null,
                'status' => $status,
                'site_id' => $this->site_id,
                'department_id' => $department->id,
                'section_id' => $section->id,
                'tenant_id' => $this->tenant_id,
                'enduser_category_id' => $category->id ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('EndusersImport | Error importing row', [
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
            'name_description' => 'required|string',
            'type' => 'required|string|in:Equipment,Personnel,Organisation',
            'department_name' => 'required|string',
            'section_name' => 'required|string',
            'asset_staff_id' => 'nullable|string',
            'model' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'manufacturer' => 'nullable|string',
            'designation' => 'nullable|string',
            'status' => 'nullable|string',
            'category_name' => 'nullable|string',
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
