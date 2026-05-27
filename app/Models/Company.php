<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    // ↓ この1行（許可リスト）を追加します
    protected $fillable = [
        'user_id',
        'name',
        'logo_path',
        'agency_id',
        'welcome_message',
        'theme_color',
        'completion_message',
        'plan_id',      
        'applied_price',
        'selected_survey_id',
        'google_map_url'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 以前追加したリレーションの設定
    public function agency()
    {
        return $this->belongsTo(User::class, 'agency_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
    public function selectedSurvey()
    {
        // 企業は1つの「選ばれたアンケート」を持つ
        return $this->belongsTo(Survey::class, 'selected_survey_id');
    }

}