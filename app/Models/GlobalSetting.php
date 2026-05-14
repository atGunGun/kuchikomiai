<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalSetting extends Model
{
    use HasFactory;

    // ▼ これを追加（keyとvalueの保存を許可）
    protected $fillable = ['key', 'value'];
}