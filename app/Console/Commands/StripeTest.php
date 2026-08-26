<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Stripe\Stripe;
use Stripe\Customer;

#[Signature('app:stripe-test')]
#[Description('Stripe API connection test')]
class StripeTest extends Command
{
    public function handle()
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $customers = Customer::all([
                'limit' => 1,
            ]);

            $this->info('Stripe APIへの接続に成功しました！');
            $this->info('Stripe上の顧客数を取得できました。');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Stripe APIへの接続に失敗しました。');
            $this->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}