<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Language;
use App\Models\OrderStatus;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orderStatusId = OrderStatus::query()->find(8)?->id ?? OrderStatus::query()->orderBy('id')->value('id');

        if (! $orderStatusId) {
            return;
        }

        $countryIds = Country::query()
            ->where('status', true)
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        $languages = Language::query()
            ->whereIn('code', ['en', 'uk'])
            ->orderBy('id')
            ->get();

        $bankTransferInstructionsEn = "Transfer the order total to:\nBank: JSC \"Example Bank\"\nRecipient: Example Store LLC\nIBAN: UA123456789012345678901234567\nPlease quote your order number in the payment reference.";
        $bankTransferInstructionsUk = "Перерахуйте суму замовлення на:\nБанк: АТ \"Приклад Банк\"\nОтримувач: ТОВ \"Приклад Магазин\"\nIBAN: UA123456789012345678901234567\nУ призначенні платежу вкажіть номер замовлення.";

        $instructionsByLang = [];
        foreach ($languages as $lang) {
            $instructionsByLang[$lang->id] = $lang->code === 'en' ? $bankTransferInstructionsEn : $bankTransferInstructionsUk;
        }

        $methods = [
            [
                'code' => 'free_checkout',
                'config' => ['order_status_id' => $orderStatusId],
                'countries' => null,
                'sort_order' => 1,
                'status' => true,
            ],
            [
                'code' => 'cod',
                'config' => ['order_status_id' => $orderStatusId],
                'countries' => $countryIds,
                'sort_order' => 2,
                'status' => true,
            ],
            [
                'code' => 'cheque',
                'config' => [
                    'payable_to' => 'My shop',
                    'order_status_id' => $orderStatusId,
                ],
                'countries' => $countryIds,
                'sort_order' => 3,
                'status' => true,
            ],
            [
                'code' => 'bank_transfer',
                'config' => [
                    'order_status_id' => $orderStatusId,
                    'instructions' => $instructionsByLang,
                ],
                'countries' => $countryIds,
                'sort_order' => 4,
                'status' => true,
            ],
        ];

        foreach ($methods as $data) {
            PaymentMethod::create($data);
        }
    }
}
