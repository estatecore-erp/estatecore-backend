<?php

namespace App\Services;

use App\Models\Inquiry;
use App\Models\Lease;
use App\Models\Property;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardStatsService
{
    public function getStats(User $user): array
    {
        $isAdmin = $user->role === 'admin';

        return [
            'properties' => $this->propertyStats($user, $isAdmin),
            'leases_active' => $this->leasesActive($user, $isAdmin),
            'sales_this_month' => $this->salesThisMonth($user, $isAdmin),
            'inquiries_pending' => $this->inquiriesPending($user, $isAdmin),
            'clients_total' => $this->clientsTotal($user, $isAdmin),
            'monthly_sales' => $this->monthlySalesChart($user, $isAdmin),
            'property_status_breakdown' => $this->propertyStatusBreakdown($user, $isAdmin),
            'recent_activity' => $this->recentActivity($user, $isAdmin),
        ];
    }

    private function propertyQuery(User $user, bool $isAdmin)
    {
        return $isAdmin
            ? Property::query()
            : Property::query()->where('agent_id', $user->id);
    }

    private function leaseQuery(User $user, bool $isAdmin)
    {
        return $isAdmin
            ? Lease::query()
            : Lease::whereHas('property', fn($q) => $q->where('agent_id', $user->id));
    }

    private function saleQuery(User $user, bool $isAdmin)
    {
        return $isAdmin
            ? Sale::query()
            : Sale::whereHas('property', fn($q) => $q->where('agent_id', $user->id));
    }

    private function inquiryQuery(User $user, bool $isAdmin)
    {
        return $isAdmin
            ? Inquiry::query()
            : Inquiry::whereHas('property', fn($q) => $q->where('agent_id', $user->id));
    }

    private function propertyStats(User $user, bool $isAdmin): array
    {
        return (clone $this->propertyQuery($user, $isAdmin))
            ->selectRaw("count(*) as total")
            ->selectRaw("count(case when status = 'available' then 1 end) as available")
            ->selectRaw("count(case when status = 'rented' then 1 end) as rented")
            ->selectRaw("count(case when status = 'sold' then 1 end) as sold")
            ->first()
            ->toArray();
    }

    private function propertyStatusBreakdown(User $user, bool $isAdmin): array
    {
        $stats = $this->propertyStats($user, $isAdmin);

        return [
            'available' => $stats['available'],
            'rented' => $stats['rented'],
            'sold' => $stats['sold'],
        ];
    }

    private function leasesActive(User $user, bool $isAdmin): int
    {
        return $this->leaseQuery($user, $isAdmin)
            ->where('status', 'active')
            ->count();
    }

    private function inquiriesPending(User $user, bool $isAdmin): int
    {
        return $this->inquiryQuery($user, $isAdmin)
            ->where('status', 'pending')
            ->count();
    }

    private function salesThisMonth(User $user, bool $isAdmin): float
    {
        return (float) $this->saleQuery($user, $isAdmin)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('sale_price');
    }

    private function clientsTotal(User $user, bool $isAdmin): int
    {
        /** @disregard P1005 */
        return $isAdmin
            ? User::query()->where('role', 'client')->count()
            : $this->agentClientCount($user);
    }

    private function agentClientCount(User $user): int
    {
        return Inquiry::whereHas('property', fn($q) => $q->where('agent_id', $user->id))
            ->distinct('client_id')
            ->count('client_id');
    }

    private function monthlySalesChart(User $user, bool $isAdmin): array
    {
        $months = collect(range(5, 0))->map(fn($i) => now()->subMonths($i));

        return $months->map(function (Carbon $date) use ($user, $isAdmin) {
            $total = (float) $this->saleQuery($user, $isAdmin)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('sale_price');

            return [
                'month' => $date->format('M'),
                'total' => $total,
            ];
        })->values()->all();
    }

    private function recentActivity(User $user, bool $isAdmin): array
    {
        $sales = $this->saleQuery($user, $isAdmin)
            ->with('property')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($s) => [
                'type' => 'sale',
                'message' => 'Sale closed for property ' . ($s->property->title ?? "#{$s->property_id}"),
                'created_at' => $s->created_at,
            ]);

        $leases = $this->leaseQuery($user, $isAdmin)
            ->with('property')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($l) => [
                'type' => 'lease',
                'message' => 'Lease signed for property ' . ($l->property->title ?? "#{$l->property_id}"),
                'created_at' => $l->created_at,
            ]);

        $inquiries = $this->inquiryQuery($user, $isAdmin)
            ->with('property')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($i) => [
                'type' => 'inquiry',
                'message' => 'New inquiry on property ' . ($i->property->title ?? "#{$i->property_id}"),
                'created_at' => $i->created_at,
            ]);

        return $sales->concat($leases)->concat($inquiries)
            ->sortByDesc('created_at')
            ->take(10)
            ->values()
            ->all();
    }
}
