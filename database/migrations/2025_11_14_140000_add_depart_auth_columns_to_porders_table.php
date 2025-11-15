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
        if (! Schema::hasTable('porders')) {
            return;
        }

        Schema::table('porders', function (Blueprint $table) {
            if (! Schema::hasColumn('porders', 'depart_auth_approval_status')) {
                $table->string('depart_auth_approval_status')->nullable()->after('site_id');
            }

            if (! Schema::hasColumn('porders', 'depart_auth_approved_by')) {
                $table->unsignedBigInteger('depart_auth_approved_by')->nullable()->after('depart_auth_approval_status');
                $table->foreign('depart_auth_approved_by')->references('id')->on('users');
            }

            if (! Schema::hasColumn('porders', 'depart_auth_denied_by')) {
                $table->unsignedBigInteger('depart_auth_denied_by')->nullable()->after('depart_auth_approved_by');
                $table->foreign('depart_auth_denied_by')->references('id')->on('users');
            }

            if (! Schema::hasColumn('porders', 'depart_auth_approval_status_time')) {
                $table->timestamp('depart_auth_approval_status_time')->nullable()->after('depart_auth_denied_by');
            }

            if (! Schema::hasColumn('porders', 'depart_auth_approved_on')) {
                $table->timestamp('depart_auth_approved_on')->nullable()->after('depart_auth_approval_status_time');
            }

            if (! Schema::hasColumn('porders', 'depart_auth_denied_on')) {
                $table->timestamp('depart_auth_denied_on')->nullable()->after('depart_auth_approved_on');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('porders')) {
            return;
        }

        Schema::table('porders', function (Blueprint $table) {
            if (Schema::hasColumn('porders', 'depart_auth_approved_by')) {
                $table->dropForeign(['depart_auth_approved_by']);
            }

            if (Schema::hasColumn('porders', 'depart_auth_denied_by')) {
                $table->dropForeign(['depart_auth_denied_by']);
            }

            foreach ([
                'depart_auth_approval_status',
                'depart_auth_approved_by',
                'depart_auth_denied_by',
                'depart_auth_approval_status_time',
                'depart_auth_approved_on',
                'depart_auth_denied_on',
            ] as $column) {
                if (Schema::hasColumn('porders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

