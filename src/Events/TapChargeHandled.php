<?php

namespace Asciisd\Cashier\Events;

use Asciisd\Cashier\Http\Requests\TapWebhookRequest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TapChargeHandled
{
    use Dispatchable, SerializesModels;

    /**
     * The webhook payload.
     *
     * @var array
     */
    public $payload;

    /**
     * Create a new event instance.
     *
     * @param TapWebhookRequest $payload
     * @return void
     */
    public function __construct(TapWebhookRequest $payload)
    {
        $this->payload = $payload;
    }
}
