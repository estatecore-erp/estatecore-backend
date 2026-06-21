<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'title',
        'description',
        'type',
        'status',
        'price',
        'location',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    // Relationships
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }

    public function lease()
    {
        return $this->hasOne(Lease::class);
    }

    public function sale()
    {
        return $this->hasOne(Sale::class);
    }
}
