<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorVisit extends Model
{
    protected $fillable = [
        'visitor_hash',
        'product_slug',
        'product_name',
    ];
}
