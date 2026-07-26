<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\SlaTarget;
use App\Models\SlaPriorityPerformance;
use App\Models\SlaWeeklyCompliance;
use App\Models\SlaMetric;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SlaCalculator
{
    const TARGET_TIMES = [
        'Critical' => 4,
        'High' => 8,
        'Medium' => 16,
        'Low' => 24,
        'critical' => 4,
        'high' => 8,
        'medium' => 16,
        'low' => 24,
    ];

    private static function safeDiffInHours($start, $end): ?float
    {
        if (!$start || !$end) return null;

        try {
            $startCarbon = Carbon::parse($start);
            $endCarbon = Carbon::parse($end);
            $minutes = abs($startCarbon->diffInMinutes($endCarbon));
            return round($minutes / 60, 2);
        } catch (\Exception $e) {
            Log::warning("SLA Calculator: Failed to parse dates", ['start' => $start, 'end' => $end]);
            return null;
        }
    }

    public static function calculateResponseTime(Ticket $ticket): ?float
    {
        return self::safeDiffInHours($ticket->created_at, $ticket->responded_at);
    }

    public static function calculateResolutionTime(Ticket $ticket): ?float
    {
        return self::safeDiffInHours($ticket->created_at, $ticket->resolved_at);
    }

    public static function isCompliant(Ticket $ticket, float $actualTime): bool
    {
        $priority = $ticket->priority_level ?? $ticket->priority ?? 'low';
        $targetTime = self::TARGET_TIMES[$priority] ?? 24;
        return $actualTime <= $targetTime;
    }

    /**
     * Format a time value for display
     * Shows minutes/seconds for sub-hour values
     */
    public static function formatTime(?float $hours): string
    {
        if ($hours === null || $hours <= 0) return '0 hours';

        if ($hours < 1) {
            $minutes = round($hours * 60);
            if ($minutes < 1) {
                $seconds = round($hours * 3600);
                return $seconds . ' sec';
            }
            return $minutes . ' min';
        }

        return round($hours, 1) . ' hours';
    }

    public static function getTicketQuery(?Carbon $startDate = null, ?Carbon $endDate = null)
    {
        $query = Ticket::query();
        if ($startDate) $query->where('created_at', '>=', $startDate);
        if ($endDate) $query->where('created_at', '<=', $endDate);
        return $query;
    }

    public static function calculateCompliancePercentage(string $priorityLevel, ?Carbon $startDate = null, ?Carbon $endDate = null): float
    {
        $query = self::getTicketQuery($startDate, $endDate)
            ->where(function($q) use ($priorityLevel) {
                $q->where('priority_level', $priorityLevel)
                  ->orWhere('priority', strtolower($priorityLevel));
            })
            ->whereNotNull('resolved_at');

        $tickets = $query->get();
        if ($tickets->isEmpty()) return 0;

        $compliantCount = 0;
        foreach ($tickets as $ticket) {
            $resolutionTime = self::calculateResolutionTime($ticket);
            if ($resolutionTime !== null && self::isCompliant($ticket, $resolutionTime)) {
                $compliantCount++;
            }
        }
        return round(($compliantCount / $tickets->count()) * 100, 2);
    }

    public static function calculateOverallCompliance(?Carbon $startDate = null, ?Carbon $endDate = null): float
    {
        $query = self::getTicketQuery($startDate, $endDate)->whereNotNull('resolved_at');
        $tickets = $query->get();
        if ($tickets->isEmpty()) return 0;

        $compliantCount = 0;
        foreach ($tickets as $ticket) {
            $resolutionTime = self::calculateResolutionTime($ticket);
            if ($resolutionTime !== null && self::isCompliant($ticket, $resolutionTime)) {
                $compliantCount++;
            }
        }
        return round(($compliantCount / $tickets->count()) * 100, 2);
    }

    /**
     * FIXED: Only show weeks that have actual ticket data
     * No more empty placeholder weeks
     */
    public static function calculateWeeklyCompliance(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $now = $endDate ? $endDate->copy() : Carbon::now();
        $currentWeekStart = $now->copy()->startOfWeek();

        // Find the oldest week that has tickets
        $oldestTicket = Ticket::orderBy('created_at', 'asc')->first();
        if (!$oldestTicket) {
            // No tickets at all - just show current week
            return [self::buildWeekData($currentWeekStart, 'Week 1 (Current)', 1, true)];
        }

        $oldestWeekStart = Carbon::parse($oldestTicket->created_at)->startOfWeek();

        // Only go back as far as the oldest ticket, but max 4 weeks back
        $earliestStart = $currentWeekStart->copy()->subWeeks(4);
        if ($oldestWeekStart->lt($earliestStart)) {
            $oldestWeekStart = $earliestStart;
        }

        $weeks = [];
        $currentWeek = $oldestWeekStart->copy();
        $weekIndex = 1;

        // Count total weeks we'll show
        $totalWeeks = $currentWeekStart->diffInWeeks($oldestWeekStart) + 1;

        while ($currentWeek->lte($now->copy()->endOfWeek())) {
            $weekStart = $currentWeek->copy();
            $weekEnd = $currentWeek->copy()->endOfWeek();
            $weeksFromNow = $currentWeekStart->diffInWeeks($weekStart);
            $isCurrent = $weeksFromNow == 0;

            // Only include weeks that have tickets OR the current week
            $weekTickets = Ticket::whereBetween('created_at', [$weekStart, $weekEnd])->get();
            $hasTickets = $weekTickets->count() > 0;

            if ($hasTickets || $isCurrent) {
                if ($isCurrent) {
                    $weekName = 'Week 1 (Current)';
                } else {
                    $weekName = 'Week ' . ($weeksFromNow + 1);
                }

                $resolvedWeekTickets = $weekTickets->whereNotNull('resolved_at');
                $totalResolved = $resolvedWeekTickets->count();

                $met = 0;
                foreach ($resolvedWeekTickets as $ticket) {
                    $resolutionTime = self::calculateResolutionTime($ticket);
                    if ($resolutionTime !== null && self::isCompliant($ticket, $resolutionTime)) {
                        $met++;
                    }
                }

                $compliance = $totalResolved > 0 ? round(($met / $totalResolved) * 100) : 0;

                $weeks[] = [
                    'week_name' => $weekName,
                    'compliance_percentage' => $compliance,
                    'ticket_count' => $weekTickets->count(),
                    'sort_order' => $weekIndex,
                    'week_start' => $weekStart->toDateString(),
                    'week_end' => $weekEnd->toDateString(),
                    'is_current_week' => $isCurrent,
                ];
                $weekIndex++;
            }

            $currentWeek->addWeek();
        }

        // If no weeks have tickets yet, just show current week
        if (empty($weeks)) {
            return [self::buildWeekData($currentWeekStart, 'Week 1 (Current)', 1, true)];
        }

        return $weeks;
    }

    private static function buildWeekData(Carbon $weekStart, string $name, int $sortOrder, bool $isCurrent): array
    {
        $weekEnd = $weekStart->copy()->endOfWeek();
        $weekTickets = Ticket::whereBetween('created_at', [$weekStart, $weekEnd])->get();
        $resolvedWeekTickets = $weekTickets->whereNotNull('resolved_at');
        $totalResolved = $resolvedWeekTickets->count();

        $met = 0;
        foreach ($resolvedWeekTickets as $ticket) {
            $resolutionTime = self::calculateResolutionTime($ticket);
            if ($resolutionTime !== null && self::isCompliant($ticket, $resolutionTime)) {
                $met++;
            }
        }

        return [
            'week_name' => $name,
            'compliance_percentage' => $totalResolved > 0 ? round(($met / $totalResolved) * 100) : 0,
            'ticket_count' => $weekTickets->count(),
            'sort_order' => $sortOrder,
            'week_start' => $weekStart->toDateString(),
            'week_end' => $weekEnd->toDateString(),
            'is_current_week' => $isCurrent,
        ];
    }

    public static function calculateMonthlyCompliance(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $now = $endDate ? $endDate->copy() : Carbon::now();
        $start = $startDate ? $startDate->copy() : $now->copy()->subMonths(5)->startOfMonth();

        $months = [];
        $currentMonth = $start->copy()->startOfMonth();
        $monthIndex = 1;

        while ($currentMonth->lte($now->copy()->endOfMonth())) {
            $monthStart = $currentMonth->copy();
            $monthEnd = $currentMonth->copy()->endOfMonth();
            $monthName = $monthStart->format('M Y');

            $monthTickets = Ticket::whereBetween('created_at', [$monthStart, $monthEnd])->get();
            $resolvedMonthTickets = $monthTickets->whereNotNull('resolved_at');
            $totalResolved = $resolvedMonthTickets->count();

            $met = 0;
            foreach ($resolvedMonthTickets as $ticket) {
                $resolutionTime = self::calculateResolutionTime($ticket);
                if ($resolutionTime !== null && self::isCompliant($ticket, $resolutionTime)) {
                    $met++;
                }
            }

            $compliance = $totalResolved > 0 ? round(($met / $totalResolved) * 100) : 0;

            $months[] = [
                'period_name' => $monthName,
                'compliance_percentage' => $compliance,
                'ticket_count' => $monthTickets->count(),
                'sort_order' => $monthIndex,
                'period_start' => $monthStart->toDateString(),
                'period_end' => $monthEnd->toDateString(),
            ];

            $currentMonth->addMonth();
            $monthIndex++;
        }

        return $months;
    }

    public static function calculateYearlyCompliance(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $now = $endDate ? $endDate->copy() : Carbon::now();
        $start = $startDate ? $startDate->copy() : $now->copy()->subYears(4)->startOfYear();

        $years = [];
        $currentYear = $start->copy()->startOfYear();
        $yearIndex = 1;

        while ($currentYear->lte($now->copy()->endOfYear())) {
            $yearStart = $currentYear->copy();
            $yearEnd = $currentYear->copy()->endOfYear();
            $yearName = $yearStart->format('Y');

            $yearTickets = Ticket::whereBetween('created_at', [$yearStart, $yearEnd])->get();
            $resolvedYearTickets = $yearTickets->whereNotNull('resolved_at');
            $totalResolved = $resolvedYearTickets->count();

            $met = 0;
            foreach ($resolvedYearTickets as $ticket) {
                $resolutionTime = self::calculateResolutionTime($ticket);
                if ($resolutionTime !== null && self::isCompliant($ticket, $resolutionTime)) {
                    $met++;
                }
            }

            $compliance = $totalResolved > 0 ? round(($met / $totalResolved) * 100) : 0;

            $years[] = [
                'period_name' => $yearName,
                'compliance_percentage' => $compliance,
                'ticket_count' => $yearTickets->count(),
                'sort_order' => $yearIndex,
                'period_start' => $yearStart->toDateString(),
                'period_end' => $yearEnd->toDateString(),
            ];

            $currentYear->addYear();
            $yearIndex++;
        }

        return $years;
    }

    public static function getDateRangeForFilter(string $filter): array
    {
        $now = Carbon::now();

        switch ($filter) {
            case 'week':
                return [
                    'start' => $now->copy()->subWeeks(4)->startOfWeek(),
                    'end' => $now->copy()->endOfWeek(),
                    'label' => 'Week 1 (Current)',
                ];
            case 'month':
                return [
                    'start' => $now->copy()->subMonths(5)->startOfMonth(),
                    'end' => $now->copy()->endOfMonth(),
                    'label' => 'Last 6 Months',
                ];
            case 'year':
                return [
                    'start' => $now->copy()->subYears(4)->startOfYear(),
                    'end' => $now->copy()->endOfYear(),
                    'label' => 'Last 5 Years',
                ];
            case 'all':
            default:
                $oldestTicket = Ticket::orderBy('created_at', 'asc')->first();
                return [
                    'start' => $oldestTicket ? Carbon::parse($oldestTicket->created_at)->startOfMonth() : $now->copy()->subYear(),
                    'end' => $now->copy()->endOfDay(),
                    'label' => 'All Time',
                ];
        }
    }

    public static function updateSlaData()
    {
        foreach (['Critical', 'High', 'Medium', 'Low'] as $priority) {
            $compliance = self::calculateCompliancePercentage($priority);
            SlaPriorityPerformance::where('priority_level', $priority)
                ->update(['compliance_percentage' => $compliance]);
        }

        foreach (['Critical', 'High', 'Medium', 'Low'] as $priority) {
            $tickets = Ticket::where(function($q) use ($priority) {
                    $q->where('priority_level', $priority)
                      ->orWhere('priority', strtolower($priority));
                })
                ->whereNotNull('resolved_at')
                ->get();

            if ($tickets->isNotEmpty()) {
                $avgResolutionTime = $tickets->avg(function ($ticket) {
                    return self::calculateResolutionTime($ticket);
                });
                $compliance = self::calculateCompliancePercentage($priority);
                $status = 'On Track';
                if ($compliance < 70) $status = 'Breached';
                elseif ($compliance < 85) $status = 'At Risk';

                SlaTarget::where('priority_level', $priority)
                    ->update([
                        'actual_time' => self::formatTime($avgResolutionTime),
                        'compliance_percentage' => $compliance,
                        'ticket_count' => $tickets->count(),
                        'status' => $status,
                    ]);
            } else {
                SlaTarget::where('priority_level', $priority)
                    ->update([
                        'actual_time' => '0h',
                        'compliance_percentage' => 0,
                        'ticket_count' => 0,
                        'status' => 'On Track',
                    ]);
            }
        }

        $weeklyData = self::calculateWeeklyCompliance();
        SlaWeeklyCompliance::truncate();
        foreach ($weeklyData as $week) {
            SlaWeeklyCompliance::create([
                'week_name' => $week['week_name'],
                'compliance_percentage' => $week['compliance_percentage'],
                'ticket_count' => $week['ticket_count'],
                'sort_order' => $week['sort_order'],
            ]);
        }

        $totalTickets = Ticket::count();
        $resolvedTickets = Ticket::whereNotNull('resolved_at')->count();

        if ($resolvedTickets > 0) {
            $overallPercentage = self::calculateOverallCompliance();
            SlaMetric::where('metric_name', 'overall_compliance')
                ->update(['metric_value' => (string) $overallPercentage]);

            $avgResponse = Ticket::whereNotNull('responded_at')
                ->get()
                ->avg(function ($ticket) {
                    return self::calculateResponseTime($ticket);
                });
            SlaMetric::where('metric_name', 'avg_response_time')
                ->update(['metric_value' => self::formatTime($avgResponse)]);

            $avgResolution = Ticket::whereNotNull('resolved_at')
                ->get()
                ->avg(function ($ticket) {
                    return self::calculateResolutionTime($ticket);
                });
            SlaMetric::where('metric_name', 'avg_resolution_time')
                ->update(['metric_value' => self::formatTime($avgResolution)]);
        } else {
            SlaMetric::where('metric_name', 'overall_compliance')->update(['metric_value' => '0']);
            SlaMetric::where('metric_name', 'avg_response_time')->update(['metric_value' => '0 hours']);
            SlaMetric::where('metric_name', 'avg_resolution_time')->update(['metric_value' => '0 hours']);
        }
    }
}