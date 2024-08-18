<?php

namespace Asciisd\Cashier\Http\Controllers;

use Asciisd\Cashier\Billable;
use Asciisd\Cashier\Cashier;
use Asciisd\Cashier\Events\TapChargeHandled;
use Asciisd\Cashier\Events\WebhookHandled;
use Asciisd\Cashier\Events\WebhookReceived;
use Asciisd\Cashier\Http\Middleware\VerifyWebhookSignature;
use Asciisd\Cashier\Http\Requests\TapWebhookRequest;
use Asciisd\Cashier\Payment;
use Illuminate\Notifications\Notifiable;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tap\Charge;

class WebhookController extends Controller
{
    /**
     * Create a new WebhookController instance.
     *
     * @return void
     */
    public function __construct()
    {
        logger()->info('WebhookController | __construct', request()->all());

        if (config('cashier.webhook.secret')) {
            $this->middleware(VerifyWebhookSignature::class);
        }
    }

    /**
     * Handle a Tap webhook call.
     *
     * @param TapWebhookRequest $request
     * @return Response
     */
    public function handleWebhook(TapWebhookRequest $request)
    {
        logger()->info('WebhookController | handleWebhook', request()->all());

        $method = 'handle' . Str::studly($request->input('object'));

        WebhookReceived::dispatch($request);

        if (method_exists($this, $method)) {
            $response = $this->{$method}($request);

            WebhookHandled::dispatch($request);

            return $response;
        }

        return $this->missingMethod();
    }

    protected function handleCharge(TapWebhookRequest $request)
    {
        TapChargeHandled::dispatch($request);
        return $this->successMethod();
    }

    /**
     * Handle payment action required for invoice.
     *
     * @param array $payload
     * @return Response
     */
    protected function handleInvoicePaymentActionRequired(array $payload)
    {
        if (is_null($notification = config('cashier.payment_notification'))) {
            return $this->successMethod();
        }

        if ($user = $this->getUserByTapId($payload['customer']['id'])) {
            if (in_array(Notifiable::class, class_uses_recursive($user))) {
                $payment = new Payment(
                    Charge::retrieve($payload['id'], $user->tapOptions())
                );

                $user->notify(new $notification($payment));
            }
        }

        return $this->successMethod();
    }

    /**
     * Get the billable entity instance by Tap ID.
     *
     * @param string|null $tapId
     * @return Billable|null
     */
    protected function getUserByTapId($tapId)
    {
        return Cashier::findBillable($tapId);
    }

    /**
     * Handle successful calls on the controller.
     *
     * @param array $parameters
     * @return Response
     */
    protected function successMethod($parameters = [])
    {
        return new Response('Webhook Handled', 200);
    }

    /**
     * Handle calls to missing methods on the controller.
     *
     * @param array $parameters
     * @return Response
     */
    protected function missingMethod($parameters = [])
    {
        logger('Missing Method', $parameters);
        return new Response;
    }
}
