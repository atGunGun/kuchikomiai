<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    // データベースへの保存を許可する項目を設定します
    // protected $fillable = ['prompt_details', 'generated_text'];
    protected $fillable = ['prompt_details', 'generated_text', 'company_id']; // company_idを追加
}