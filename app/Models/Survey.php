<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $fillable = ['company_id', 'title', 'description'];

    // 設問との紐付け（順序通りに取得）
    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('sort_order');
    }
}
