<?php

namespace App\Http\Controllers;

use App\Models\SlaWeeklyCompliance;
use App\Models\SlaPriorityPerformance;
use App\Models\SlaTarget;
use App\Models\SlaMetric;
use App\Models\Ticket;
use App\Services\SlaCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SlaReportController extends Controller
{
    /**
     * Display SLA Reports Dashboard with optional filter
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'week'); // week, month, year, all

        // Always recalculate SLA data from actual tickets before displaying
        try {
            SlaCalculator::updateSlaData();
        } catch (\Exception $e) {
            Log::error('SLA Update failed: ' . $e->getMessage());
        }

        // Get date range for the selected filter
        $dateRange = SlaCalculator::getDateRangeForFilter($filter);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];
        $filterLabel = $dateRange['label'];

        // Get filtered data based on time period
        $weeklyCompliance = SlaWeeklyCompliance::orderBy('sort_order')->get();
        $priorityPerformance = SlaPriorityPerformance::orderBy('sort_order')->get();
        $slaTargets = SlaTarget::orderBy('sort_order')->get();

        // Get metrics (these are always current/all-time)
        $metrics = [
            'overall_compliance' => SlaMetric::where('metric_name', 'overall_compliance')->first(),
            'avg_response_time' => SlaMetric::where('metric_name', 'avg_response_time')->first(),
            'avg_resolution_time' => SlaMetric::where('metric_name', 'avg_resolution_time')->first(),
        ];

        // Calculate filtered stats for display
        $filteredTickets = Ticket::whereBetween('created_at', [$startDate, $endDate])->get();
        $filteredResolved = $filteredTickets->whereNotNull('resolved_at')->count();
        $filteredTotal = $filteredTickets->count();

        // Calculate filtered compliance for the trend chart
        $trendData = [];
        if ($filter === 'month') {
            $trendData = SlaCalculator::calculateMonthlyCompliance($startDate, $endDate);
        } elseif ($filter === 'year') {
            $trendData = SlaCalculator::calculateYearlyCompliance($startDate, $endDate);
        } else {
            // week or all - use weekly data
            $trendData = SlaCalculator::calculateWeeklyCompliance($startDate, $endDate);
        }

        return view('sla-reports.index', compact(
            'weeklyCompliance',
            'priorityPerformance',
            'slaTargets',
            'metrics',
            'filter',
            'filterLabel',
            'trendData',
            'filteredTotal',
            'filteredResolved',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export SLA data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');

        // Recalculate before export
        try {
            SlaCalculator::updateSlaData();
        } catch (\Exception $e) {
            Log::error('SLA Update failed during export: ' . $e->getMessage());
        }

        $data = [
            'generated_at' => now()->toDateTimeString(),
            'weekly_compliance' => SlaWeeklyCompliance::orderBy('sort_order')->get()->toArray(),
            'priority_performance' => SlaPriorityPerformance::orderBy('sort_order')->get()->toArray(),
            'sla_targets' => SlaTarget::orderBy('sort_order')->get()->toArray(),
            'metrics' => [
                'overall_compliance' => SlaMetric::where('metric_name', 'overall_compliance')->value('metric_value'),
                'avg_response_time' => SlaMetric::where('metric_name', 'avg_response_time')->value('metric_value'),
                'avg_resolution_time' => SlaMetric::where('metric_name', 'avg_resolution_time')->value('metric_value'),
            ],
            'ticket_summary' => [
                'total' => Ticket::count(),
                'open' => Ticket::where('status', 'open')->count(),
                'in_progress' => Ticket::where('status', 'in-progress')->count(),
                'resolved' => Ticket::where('status', 'resolved')->count(),
                'closed' => Ticket::where('status', 'closed')->count(),
                'overdue' => Ticket::where('status', '!=', 'closed')
                    ->where('status', '!=', 'resolved')
                    ->where('due_date', '<', now())
                    ->count(),
            ],
        ];

        if ($format === 'json') {
            return Response::json($data, 200, [
                'Content-Disposition' => 'attachment; filename="sla-report-' . now()->format('Y-m-d') . '.json"'
            ]);
        }

        $csv = $this->generateCsv($data);

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sla-report-' . now()->format('Y-m-d') . '.csv"'
        ]);
    }

    /**
     * Generate CSV from data array
     */
    private function generateCsv(array $data): string
    {
        $output = fopen('php://temp', 'r+');

        fputcsv($output, ['SLA Report Generated', $data['generated_at']]);
        fputcsv($output, []);

        fputcsv($output, ['Ticket Summary']);
        fputcsv($output, ['Metric', 'Value']);
        foreach ($data['ticket_summary'] as $key => $value) {
            fputcsv($output, [ucwords(str_replace('_', ' ', $key)), $value]);
        }
        fputcsv($output, []);

        fputcsv($output, ['Weekly Compliance']);
        fputcsv($output, ['Week', 'Compliance %', 'Ticket Count']);
        foreach ($data['weekly_compliance'] as $week) {
            fputcsv($output, [$week['week_name'], $week['compliance_percentage'], $week['ticket_count']]);
        }
        fputcsv($output, []);

        fputcsv($output, ['Priority Performance']);
        fputcsv($output, ['Priority', 'Compliance %']);
        foreach ($data['priority_performance'] as $perf) {
            fputcsv($output, [$perf['priority_level'], $perf['compliance_percentage']]);
        }
        fputcsv($output, []);

        fputcsv($output, ['SLA Targets']);
        fputcsv($output, ['Priority', 'Target Time', 'Actual Time', 'Compliance %', 'Ticket Count', 'Status']);
        foreach ($data['sla_targets'] as $target) {
            fputcsv($output, [
                $target['priority_level'],
                $target['target_time'],
                $target['actual_time'],
                $target['compliance_percentage'],
                $target['ticket_count'],
                $target['status']
            ]);
        }
        fputcsv($output, []);

        fputcsv($output, ['Key Metrics']);
        fputcsv($output, ['Metric', 'Value']);
        fputcsv($output, ['Overall Compliance', $data['metrics']['overall_compliance']]);
        fputcsv($output, ['Avg Response Time', $data['metrics']['avg_response_time']]);
        fputcsv($output, ['Avg Resolution Time', $data['metrics']['avg_resolution_time']]);

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Send SLA report data to Sales and Customer Support departments
     */
    public function sendToDepartments(Request $request)
    {
        // Gather current SLA snapshot
        $payload = [
            'generated_at' => now()->toDateTimeString(),
            'report_type' => 'sla_summary',
            'summary' => [
                'total_tickets' => Ticket::count(),
                'open' => Ticket::where('status', 'open')->count(),
                'in_progress' => Ticket::where('status', 'in-progress')->count(),
                'resolved' => Ticket::where('status', 'resolved')->count(),
                'overdue' => Ticket::where('status', '!=', 'closed')
                    ->where('status', '!=', 'resolved')
                    ->where('due_date', '<', now())
                    ->count(),
            ],
            'metrics' => [
                'overall_compliance' => SlaMetric::where('metric_name', 'overall_compliance')->value('metric_value'),
                'avg_response_time' => SlaMetric::where('metric_name', 'avg_response_time')->value('metric_value'),
                'avg_resolution_time' => SlaMetric::where('metric_name', 'avg_resolution_time')->value('metric_value'),
            ],
            'targets' => SlaTarget::orderBy('sort_order')->get()->toArray(),
        ];

        return response()->json([
            'success' => true,
            'status' => 'queued',
            'message' => 'SLA report data queued for Sales and Customer Support.',
            'sent_at' => now()->toDateTimeString(),
            'departments' => ['sales', 'customer_support'],
            'payload_summary' => $payload['summary'],
        ]);
    }
}