<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    // データベースへの保存を許可する項目を設定します
    protected $fillable = [
        'company_id',
        'prompt_details',
        'generated_text',
        'is_copied',         // ★ コピー数のカウント用
        'is_redirected',     // ★ Google遷移のカウント用
        'is_direct_post',    // ★ そのまま投稿のカウント用
        'duration_seconds',   // ★ 平均所要時間の計測用
    ];
}