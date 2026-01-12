<?php

namespace App\Models;

use App\Models\Concerns\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory, TenantScope;
    
    protected $fillable = [
        'fname','lname','oname', 'date_of_birth', 'national_id_passport_number', 'gender', 'phone', 
        'email', 'address', 'emergency_contact_name', 'emergency_contact_phone', 
        'nationality', 'marital_status', 'job_title', 'department_id', 'employment_type', 
        'employee_status', 'date_of_joining', 'reporting_manager', 'employee_grade_level', 
        'work_location', 'contract_type', 'contract_start_date', 'contract_end_date', 'probation_period','user_id','site_id','tenant_id','edited_by','profile_picture','duration'
    ];
    
    public function department(){
        return $this->belongsTo(Department::class,'department_id');
    }

    /**
     * Get the tenant that owns the employee
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        static::bootTenantScope();
    }
}
