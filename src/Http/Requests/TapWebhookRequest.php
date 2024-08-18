<?php

namespace Asciisd\Cashier\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class TapWebhookRequest
 *
 * @property string $id
 * @property string $amount
 * @property string $currency
 * @property string $gateway_reference
 * @property string $payment_reference
 * @property string $status
 * @property string $created
 *
 * @package Asciisd\Cashier\Http\Requests
 */
class TapWebhookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        logger('TapWebhookRequest | authorize', request()->all());

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        logger('TapWebhookRequest | rules', request()->all());

        return [
            'id' => 'required',
            'amount' => 'required',
            'currency' => 'required',
            'reference.gateway' => 'required',
            'reference.payment' => 'required',
            'status' => 'required',
            'transaction.created' => 'required',
        ];
    }
}
