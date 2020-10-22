<?php


namespace Asciisd\Cashier\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Tap\Exception\SignatureVerificationException;
use Tap\WebhookSignature;

class VerifyWebhookSignature
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     *
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            WebhookSignature::verifyHeader(
                $request->all(),
                $request->header(),
                config('cashier.webhook.secret'),
                config('cashier.webhook.tolerance')
            );
        } catch (SignatureVerificationException $exception) {
            throw new AccessDeniedHttpException($exception->getMessage(), $exception);
        }

        return $next($request);
    }
}
