<?php

namespace EscolaLms\Payments\Database\Seeders;

use EscolaLms\Payments\Enums\PaymentStatus;
use EscolaLms\Payments\Facades\Payments;
use EscolaLms\Payments\Models\Payment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;

class PaymentsSeeder extends Seeder
{
    use WithFaker;

    public function run()
    {
        /** @var Model|HasFactory $model */
        $model = Payments::getPaymentsConfig()->getFallbackBillableModel();
        $billable = $model::query()->where('email', 'student@escola-lms.com')->first();
        if (!$billable) {
            $billable = $model::factory()->create();
        }

        Payment::factory()
            ->count(5)
            ->create([
                'billable_id' => $billable->getKey(),
                'billable_type' => $model,
                'status' => PaymentStatus::NEW,
            ]);
        Payment::factory()
            ->count(5)
            ->create([
                'billable_id' => $billable->getKey(),
                'billable_type' => $model,
                'status' => PaymentStatus::PAID,
            ]);
    }
}
