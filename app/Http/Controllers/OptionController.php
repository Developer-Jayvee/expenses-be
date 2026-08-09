<?php

namespace App\Http\Controllers;

use App\Enums\BillCategoryEnum;
use App\Enums\PaymentTypesEnum;
use Illuminate\Http\JsonResponse;

class OptionController extends Controller
{    
    /**
     * __invoke
     *
     * @param  string $type
     * @return JsonResponse
     */
    public function __invoke(string $type): JsonResponse
    {
        $options = array();
        switch ($type) {
            case 'category':
                $options = $this->optionArray(BillCategoryEnum::cases());
                break;
            case 'payments':
                $options = $this->optionArray(PaymentTypesEnum::cases());
                break;
        }

        return $this->successMessage("Option $type",$options); 
    }    
    /**
     * Option Array 
     *
     * @param  mixed $selection
     * @return array
     */
    private function optionArray(array $selection): array
    {
        $options = [];
        foreach($selection as $category) {
            $options[] = [
                'label' => $category->label(),
                'key' => $category->value
            ];
        }
        return $options;
    }
}
