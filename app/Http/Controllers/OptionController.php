<?php

namespace App\Http\Controllers;

use App\Enums\BillCategoryEnum;
use App\Enums\DailyExpenseTypeEnum;
use App\Enums\PaymentTypesEnum;
use Illuminate\Http\JsonResponse;

class OptionController extends Controller
{
    /**
     * __invoke
     */
    public function __invoke(string $type): JsonResponse
    {
        $options = [];
        switch ($type) {
            case 'category':
                $options = $this->optionArray(BillCategoryEnum::cases());
                break;
            case 'payments':
                $options = $this->optionArray(PaymentTypesEnum::cases());
                break;
            case 'daily_expense_type':
                $options = $this->optionArray(DailyExpenseTypeEnum::cases());
                break;
        }

        return $this->successMessage("Option $type", $options);
    }

    /**
     * Option Array
     *
     * @param  mixed  $selection
     */
    private function optionArray(array $selection): array
    {
        $options = [];
        foreach ($selection as $category) {
            $options[] = [
                'label' => $category->label(),
                'key' => $category->value,
            ];
        }

        return $options;
    }
}
