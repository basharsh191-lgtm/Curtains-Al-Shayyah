<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    public const STATUSES = ['pending', 'confirmed', 'contacted', 'completed', 'cancelled'];

    protected $fillable = [
        'client_name',
        'phone',
        'address',
        'address_details',
        'preferred_date',
        'preferred_time',
        'notes',
        'status',
        'manage_token_hash',
    ];

    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
        ];
    }

    public function scopeWithStatus(Builder $query, ?string $status): Builder
    {
        if ($status === 'confirmed') {
            return $query->whereIn('status', ['confirmed', 'contacted']);
        }

        return $status && in_array($status, self::STATUSES, true)
            ? $query->where('status', $status)
            : $query;
    }

    public function getClientNameAttribute(): ?string
    {
        return $this->attributes['customer_name'] ?? null;
    }

    public function setClientNameAttribute(string $value): void
    {
        $this->attributes['customer_name'] = $value;
    }

    public function getWhatsappPhoneAttribute(): string
    {
        $phone = preg_replace('/\D+/', '', $this->phone) ?? '';

        if (str_starts_with($phone, '00')) {
            return substr($phone, 2);
        }

        return str_starts_with($phone, '0') ? '963'.substr($phone, 1) : $phone;
    }
}
