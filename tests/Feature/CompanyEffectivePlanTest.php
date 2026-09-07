<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyEffectivePlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_plan_is_used_when_there_is_no_demo_plan(): void
    {
        $freePlan = Plan::where('code', 'free')->firstOrFail();

        $user = User::factory()->create();

        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Test Company',
            'plan_id' => $freePlan->id,
            'demo_plan_id' => null,
            'demo_expires_at' => null,
        ]);

        $this->assertSame('free', $company->effectivePlanCode());
        $this->assertTrue($company->hasEffectivePlan('free'));
        $this->assertFalse($company->hasEffectivePlan('standard'));
    }

    public function test_active_standard_demo_is_used_as_effective_plan(): void
    {
        $freePlan = Plan::where('code', 'free')->firstOrFail();
        $standardPlan = Plan::where('code', 'standard')->firstOrFail();

        $user = User::factory()->create();

        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Standard Demo Company',
            'plan_id' => $freePlan->id,
            'demo_plan_id' => $standardPlan->id,
            'demo_expires_at' => now()->addDay(),
        ]);

        $this->assertSame('standard', $company->effectivePlanCode());
        $this->assertTrue($company->hasEffectivePlan('standard'));
        $this->assertFalse($company->hasEffectivePlan('free'));
    }

    public function test_active_premium_demo_is_used_as_effective_plan(): void
    {
        $freePlan = Plan::where('code', 'free')->firstOrFail();
        $premiumPlan = Plan::where('code', 'premium')->firstOrFail();

        $user = User::factory()->create();

        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Premium Demo Company',
            'plan_id' => $freePlan->id,
            'demo_plan_id' => $premiumPlan->id,
            'demo_expires_at' => now()->addDay(),
        ]);

        $this->assertSame('premium', $company->effectivePlanCode());
        $this->assertTrue($company->hasEffectivePlan('premium'));
        $this->assertFalse($company->hasEffectivePlan('free'));
    }

    public function test_expired_demo_falls_back_to_actual_plan(): void
    {
        $freePlan = Plan::where('code', 'free')->firstOrFail();
        $standardPlan = Plan::where('code', 'standard')->firstOrFail();

        $user = User::factory()->create();

        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Expired Demo Company',
            'plan_id' => $freePlan->id,
            'demo_plan_id' => $standardPlan->id,
            'demo_expires_at' => now()->subMinute(),
        ]);

        $this->assertSame('free', $company->effectivePlanCode());
        $this->assertTrue($company->hasEffectivePlan('free'));
        $this->assertFalse($company->hasEffectivePlan('standard'));
    }
}