<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->timestamp('responded_at')->nullable()->after('status');
            $table->timestamp('resolved_at')->nullable()->after('responded_at');
            $table->timestamp('first_response_at')->nullable()->after('responded_at');
            $table->integer('response_time_minutes')->nullable()->after('resolved_at');
            $table->integer('resolution_time_minutes')->nullable()->after('response_time_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'responded_at',
                'resolved_at',
                'first_response_at',
                'response_time_minutes',
                'resolution_time_minutes'
            ]);
        });
    }
};