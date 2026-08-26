<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'industry_id',
        'title',
        'description',
    ];

    /**
     * 業種との紐付け
     */
    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }

    /**
     * テンプレートの設問
     * 表示順に取得
     */
    public function questions()
    {
        return $this->hasMany(SurveyTemplateQuestion::class)
            ->orderBy('sort_order');
    }
}