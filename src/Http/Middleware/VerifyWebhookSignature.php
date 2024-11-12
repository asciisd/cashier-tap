<?php


namespace Asciisd\Cashier\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Tap\Exception\SignatureVerificationException;
use Tap\WebhookSignature;

class VerifyWebhookSignature
{
    /**
     * Handle the incoming request.
     *
     */
    public function handle(Request $request, Closure $next)
    {
        logger()->info('VerifyWebhookSignature@handle');
        logger()->info('Request headers: '.json_encode($request->header()));
        logger()->info('Request: '.json_encode($request->all()));

        try {
            WebhookSignature::verifyHeader(
                $request->all(),
                $request->header(),
                config('cashier.webhook.secret'),
                config('cashier.webhook.tolerance')
            );
        } catch (SignatureVerificationException $exception) {
            logger()->error($exception->getMessage());
            throw new AccessDeniedHttpException($exception->getMessage(), $exception);
        }

        return $next($request);
    }
}
