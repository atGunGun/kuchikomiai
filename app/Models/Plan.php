<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    // 保存を許可するカラム
    protected $fillable = [
        'name',
        'code',
        'base_price',
        'stripe_price_id',
        'max_surveys',
        'max_generations',
        'max_reviews_monthly',
        'description',
    ];
}
