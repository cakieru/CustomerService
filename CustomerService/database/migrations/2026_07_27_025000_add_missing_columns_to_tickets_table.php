<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('tickets', 'issue_description')) {
                $table->text('issue_description')->nullable()->after('description');
            }
            if (!Schema::hasColumn('tickets', 'priority_level')) {
                $table->string('priority_level')->nullable()->after('priority');
            }
            if (!Schema::hasColumn('tickets', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'issue_description', 'priority_level', 'admin_notes']);
        });
    }
};