<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyTemplateQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_template_id',
        'question_text',
        'type',
        'is_required',
        'sort_order',
        'options',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'options' => 'array',
    ];

    public function surveyTemplate()
    {
        return $this->belongsTo(SurveyTemplate::class);
    }
}