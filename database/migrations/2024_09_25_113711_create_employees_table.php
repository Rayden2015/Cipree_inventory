<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('fname')->nullable();
            $table->string('oname')->nullable();
            $table->string('lname')->nullable();

            $table->date('date_of_birth')->nullable();
            $table->string('national_id_passport_number')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable(); // Gender dropdown options
            $table->string('phone')->nullable();
            $table->string('email')->unique()->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('nationality')->nullable(); // Add appropriate country options
            $table->enum('marital_status', ['Single', 'Married', 'Divorced', 'Widowed'])->nullable(); // Marital status dropdown options

            // Job Information
            $table->string('job_title')->nullable();
            $table->unsignedBigInteger('department_id')->nullable(); // Add appropriate department options
            $table->enum('employment_type', ['Full-time', 'Part-time', 'Contract'])->nullable();
            $table->enum('employee_status', ['Active', 'On Leave', 'Terminated', 'Inactive'])->nullable();
            $table->date('date_of_joining')->nullable();
            $table->string('reporting_manager')->nullable();
            $table->string('employee_grade_level')->nullable(); // Add appropriate grade/level options
            $table->string('work_location')->nullable(); // Add appropriate work location options

            // Employment Contracts
            $table->enum('contract_type', ['Permanent', 'Fixed-Term', 'Temporary', 'Other'])->nullable();
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->integer('probation_period')->nullable(); // Probation period in months
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('site_id');
            $table->unsignedBigInteger('edited_by')->nullable(); //
            $table->string('profile_picture')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('site_id')->references('id')->on('sites');
            $table->foreign('edited_by')->references('id')->on('users');
            $table->foreign('department_id')->references('id')->on('departments');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
