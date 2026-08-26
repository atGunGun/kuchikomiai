<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * 業種に属するテンプレート
     */
    public function surveyTemplates()
    {
        return $this->hasMany(SurveyTemplate::class);
    }
}