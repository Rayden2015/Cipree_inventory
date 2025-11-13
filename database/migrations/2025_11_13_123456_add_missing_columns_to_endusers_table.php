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
        Schema::table('endusers', function (Blueprint $table) {
            if (! Schema::hasColumn('endusers', 'asset_staff_id')) {
                $table->string('asset_staff_id')->nullable()->after('id');
            }

            if (! Schema::hasColumn('endusers', 'name_description')) {
                $table->string('name_description')->nullable()->after('asset_staff_id');
            }

            if (! Schema::hasColumn('endusers', 'designation')) {
                $table->string('designation')->nullable()->after('manufacturer');
            }

            if (! Schema::hasColumn('endusers', 'status')) {
                $table->string('status')->nullable()->after('designation');
            }

            if (! Schema::hasColumn('endusers', 'site_id')) {
                $table->unsignedBigInteger('site_id')->nullable()->after('status');
            }

            if (! Schema::hasColumn('endusers', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable()->after('site_id');
            }

            if (! Schema::hasColumn('endusers', 'section_id')) {
                $table->unsignedBigInteger('section_id')->nullable()->after('department_id');
            }

            if (! Schema::hasColumn('endusers', 'enduser_category_id')) {
                $table->unsignedBigInteger('enduser_category_id')->nullable()->after('section_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('endusers', function (Blueprint $table) {
            foreach ([
                'asset_staff_id',
                'name_description',
                'designation',
                'status',
                'site_id',
                'department_id',
                'section_id',
                'enduser_category_id',
            ] as $column) {
                if (Schema::hasColumn('endusers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
