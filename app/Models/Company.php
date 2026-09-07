<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        'demo_plan_id',
        'demo_expires_at',
        'applied_price',
        'selected_survey_id',
        'google_map_url',
        'stripe_customer_id',
        'stripe_subscription_id',
        'token'
    ];
    protected $casts = [
        'demo_expires_at' => 'datetime',
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
    public function demoPlan()
    {
        return $this->belongsTo(Plan::class, 'demo_plan_id');
    }

    public function effectivePlan()
    {
        if (
            $this->demo_plan_id &&
            (is_null($this->demo_expires_at) || $this->demo_expires_at->isFuture())
        ) {
            return $this->demoPlan;
        }

        return $this->plan;
    }
    public function effectivePlanCode(): ?string
    {
        return $this->effectivePlan()?->code;
    }

    public function hasEffectivePlan(string $code): bool
    {
        return $this->effectivePlanCode() === $code;
    }
    public function selectedSurvey()
    {
        // 企業は1つの「選ばれたアンケート」を持つ
        return $this->belongsTo(Survey::class, 'selected_survey_id');
    }
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($company) {
            if (empty($company->token)) {
                $company->token = Str::random(12);
            }
        });
    }

}