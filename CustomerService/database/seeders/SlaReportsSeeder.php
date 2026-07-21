<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SlaReportsSeeder extends Seeder
{
    public function run(): void
    {
        // Create SLA tables if not exist
        $this->createTables();

        // Seed initial data
        $this->seedPriorityPerformance();
        $this->seedTargets();
        $this->seedWeeklyCompliance();
        $this->seedMetrics();
    }

    private function createTables(): void
    {
        // sla_priority_performance
        if (!DB::getSchemaBuilder()->hasTable('sla_priority_performance')) {
            DB::statement('
                CREATE TABLE sla_priority_performance (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    priority_level VARCHAR(255) NOT NULL,
                    compliance_percentage DECIMAL(5,2) DEFAULT 0,
                    color_class VARCHAR(255),
                    sort_order INT DEFAULT 0,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL
                )
            ');
        }

        // sla_targets
        if (!DB::getSchemaBuilder()->hasTable('sla_targets')) {
            DB::statement('
                CREATE TABLE sla_targets (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    priority_level VARCHAR(255) NOT NULL,
                    target_time VARCHAR(255),
                    actual_time VARCHAR(255),
                    compliance_percentage DECIMAL(5,2) DEFAULT 0,
                    ticket_count INT DEFAULT 0,
                    status VARCHAR(255),
                    badge_color VARCHAR(255),
                    badge_text_color VARCHAR(255),
                    progress_color VARCHAR(255),
                    sort_order INT DEFAULT 0,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL
                )
            ');
        }

        // sla_weekly_compliance
        if (!DB::getSchemaBuilder()->hasTable('sla_weekly_compliance')) {
            DB::statement('
                CREATE TABLE sla_weekly_compliance (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    week_name VARCHAR(255) NOT NULL,
                    compliance_percentage DECIMAL(5,2) DEFAULT 0,
                    ticket_count INT DEFAULT 0,
                    sort_order INT DEFAULT 0,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL
                )
            ');
        }

        // sla_metrics
        if (!DB::getSchemaBuilder()->hasTable('sla_metrics')) {
            DB::statement('
                CREATE TABLE sla_metrics (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    metric_name VARCHAR(255) NOT NULL,
                    metric_value VARCHAR(255),
                    metric_label VARCHAR(255),
                    icon_class VARCHAR(255),
                    icon_bg VARCHAR(255),
                    trend_text VARCHAR(255),
                    trend_direction VARCHAR(255),
                    target_value VARCHAR(255),
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL
                )
            ');
        }
    }

    private function seedPriorityPerformance(): void
    {
        DB::table('sla_priority_performance')->truncate();
        DB::table('sla_priority_performance')->insert([
            ['priority_level' => 'Critical', 'compliance_percentage' => 0, 'color_class' => 'bg-red-500', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['priority_level' => 'High', 'compliance_percentage' => 0, 'color_class' => 'bg-orange-500', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['priority_level' => 'Medium', 'compliance_percentage' => 0, 'color_class' => 'bg-yellow-500', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['priority_level' => 'Low', 'compliance_percentage' => 0, 'color_class' => 'bg-green-500', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    private function seedTargets(): void
    {
        DB::table('sla_targets')->truncate();
        DB::table('sla_targets')->insert([
            ['priority_level' => 'Critical', 'target_time' => '4h', 'actual_time' => '0h', 'compliance_percentage' => 0, 'ticket_count' => 0, 'status' => 'On Track', 'badge_color' => 'bg-red-100', 'badge_text_color' => 'text-red-800', 'progress_color' => 'bg-red-500', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['priority_level' => 'High', 'target_time' => '8h', 'actual_time' => '0h', 'compliance_percentage' => 0, 'ticket_count' => 0, 'status' => 'On Track', 'badge_color' => 'bg-orange-100', 'badge_text_color' => 'text-orange-800', 'progress_color' => 'bg-orange-500', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['priority_level' => 'Medium', 'target_time' => '16h', 'actual_time' => '0h', 'compliance_percentage' => 0, 'ticket_count' => 0, 'status' => 'On Track', 'badge_color' => 'bg-yellow-100', 'badge_text_color' => 'text-yellow-800', 'progress_color' => 'bg-yellow-500', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['priority_level' => 'Low', 'target_time' => '24h', 'actual_time' => '0h', 'compliance_percentage' => 0, 'ticket_count' => 0, 'status' => 'On Track', 'badge_color' => 'bg-green-100', 'badge_text_color' => 'text-green-800', 'progress_color' => 'bg-green-500', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    private function seedWeeklyCompliance(): void
    {
        DB::table('sla_weekly_compliance')->truncate();
        DB::table('sla_weekly_compliance')->insert([
            ['week_name' => 'Week 1', 'compliance_percentage' => 0, 'ticket_count' => 0, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['week_name' => 'Week 2', 'compliance_percentage' => 0, 'ticket_count' => 0, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['week_name' => 'Week 3', 'compliance_percentage' => 0, 'ticket_count' => 0, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['week_name' => 'Week 4', 'compliance_percentage' => 0, 'ticket_count' => 0, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['week_name' => 'Week 5', 'compliance_percentage' => 0, 'ticket_count' => 0, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    private function seedMetrics(): void
    {
        DB::table('sla_metrics')->truncate();
        DB::table('sla_metrics')->insert([
            ['metric_name' => 'overall_compliance', 'metric_value' => '0', 'metric_label' => 'Overall Compliance', 'icon_class' => 'fa-check', 'icon_bg' => 'bg-green-100', 'trend_text' => '+0%', 'trend_direction' => 'up', 'target_value' => '100%', 'created_at' => now(), 'updated_at' => now()],
            ['metric_name' => 'avg_response_time', 'metric_value' => '0 hours', 'metric_label' => 'Avg. Response Time', 'icon_class' => 'fa-clock', 'icon_bg' => 'bg-yellow-100', 'trend_text' => '-0%', 'trend_direction' => 'down', 'target_value' => '< 2h', 'created_at' => now(), 'updated_at' => now()],
            ['metric_name' => 'avg_resolution_time', 'metric_value' => '0 hours', 'metric_label' => 'Avg. Resolution Time', 'icon_class' => 'fa-chart-line', 'icon_bg' => 'bg-purple-100', 'trend_text' => '+0%', 'trend_direction' => 'up', 'target_value' => '< 8h', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}