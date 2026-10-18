<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Curtain extends Model
{
    public const CATEGORIES = ['مودرن', 'كلاسيك', 'بلاك أوت'];

    public const MATERIALS = ['كتان', 'مخمل', 'بوليستر'];

    public const OPACITY_LEVELS = ['شفاف', 'نصف تعتيم', 'تعتيم كامل'];

    protected $fillable = [
        'title',
        'category',
        'material',
        'opacity_level',
        'description',
        'image',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getImageUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->image);
    }
}
