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
        'base_price', 
        'max_surveys', 
        'max_generations', // ★追加
        'description'
    ];
}