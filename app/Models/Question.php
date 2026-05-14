<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    // ▼ これを追加（一括保存を許可するカラム）
    protected $fillable = [
        'survey_id', 
        'question_text', 
        'type', 
        'is_required', 
        'sort_order',
        'options' // ← チェックボックスの選択肢用に追加
    ];

    // ▼ これを追加（optionsをデータベース上ではJSONとして、プログラム上では配列として扱う設定）
    protected $casts = [
        'options' => 'array',
    ];
}