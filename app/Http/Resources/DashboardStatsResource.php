<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardStatsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'properties' => [
                'total' => $this->resource['properties']['total'] ?? 0,
                'available' => $this->resource['properties']['available'] ?? 0,
                'rented' => $this->resource['properties']['rented'] ?? 0,
                'sold' => $this->resource['properties']['sold'] ?? 0,
            ],

            'leases_active' => $this->resource['leases_active'] ?? 0,

            'sales_this_month' => (float) ($this->resource['sales_this_month'] ?? 0),

            'inquiries_pending' => $this->resource['inquiries_pending'] ?? 0,

            'clients_total' => $this->resource['clients_total'] ?? 0,

            'monthly_sales' => collect($this->resource['monthly_sales'] ?? [])
                ->map(fn($row) => [
                    'month' => $row['month'],
                    'total' => (float) $row['total'],
                ])->all(),

            'property_status_breakdown' => [
                'available' => $this->resource['property_status_breakdown']['available'] ?? 0,
                'rented' => $this->resource['property_status_breakdown']['rented'] ?? 0,
                'sold' => $this->resource['property_status_breakdown']['sold'] ?? 0,
            ],

            'recent_activity' => collect($this->resource['recent_activity'] ?? [])
                ->map(fn($item) => [
                    'type' => $item['type'],
                    'message' => $item['message'],
                    'created_at' => $item['created_at'] instanceof \Carbon\Carbon
                        ? $item['created_at']->toDateTimeString()
                        : (string) $item['created_at'],
                ])->all(),
        ];
    }
}
