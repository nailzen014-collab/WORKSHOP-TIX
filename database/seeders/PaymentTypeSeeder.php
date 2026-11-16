<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\PaymentType;
use Illuminate\Database\Seeder;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Seed payment types and backfill existing payment records.
     */
    public function run(): void
    {
        $types = [
            ['code' => 'bank_transfer', 'name' => 'Bank Transfer', 'group' => 'bank_transfer'],
            ['code' => 'echannel', 'name' => 'Mandiri Bill', 'group' => 'bank_transfer'],
            ['code' => 'bca_va', 'name' => 'BCA Virtual Account', 'group' => 'bank_transfer'],
            ['code' => 'bni_va', 'name' => 'BNI Virtual Account', 'group' => 'bank_transfer'],
            ['code' => 'bri_va', 'name' => 'BRI Virtual Account', 'group' => 'bank_transfer'],
            ['code' => 'permata_va', 'name' => 'Permata Virtual Account', 'group' => 'bank_transfer'],
            ['code' => 'cimb_va', 'name' => 'CIMB Virtual Account', 'group' => 'bank_transfer'],
            ['code' => 'other_va', 'name' => 'Virtual Account Lainnya', 'group' => 'bank_transfer'],
            ['code' => 'gopay', 'name' => 'GoPay', 'group' => 'ewallet'],
            ['code' => 'shopeepay', 'name' => 'ShopeePay', 'group' => 'ewallet'],
            ['code' => 'qris', 'name' => 'QRIS', 'group' => 'qris'],
            ['code' => 'credit_card', 'name' => 'Kartu Kredit', 'group' => 'card'],
            ['code' => 'cstore', 'name' => 'Gerai Retail', 'group' => 'retail'],
            ['code' => 'akulaku', 'name' => 'Akulaku', 'group' => 'paylater'],
            ['code' => 'kredivo', 'name' => 'Kredivo', 'group' => 'paylater'],
            ['code' => 'other', 'name' => 'Lainnya', 'group' => 'other'],
        ];

        foreach ($types as $index => $type) {
            PaymentType::query()->updateOrCreate(
                ['code' => $type['code']],
                [
                    'name' => $type['name'],
                    'group' => $type['group'],
                    'is_active' => true,
                    'sort_order' => $index,
                ],
            );
        }

        Payment::query()
            ->whereNotNull('payment_type')
            ->whereNull('payment_type_id')
            ->cursor()
            ->each(function (Payment $payment): void {
                $type = PaymentType::query()
                    ->where('code', $payment->payment_type)
                    ->first();

                if ($type) {
                    $payment->update(['payment_type_id' => $type->id]);
                }
            });
    }
}
