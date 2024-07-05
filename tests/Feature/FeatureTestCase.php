<?php

namespace Asciisd\Cashier\Tests\Feature;

use Asciisd\Cashier\Tests\Fixtures\User;
use Asciisd\Cashier\Tests\TestCase;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Config;
use Tap\ApiResource;
use Tap\Card;
use Tap\Exception\InvalidRequestException;
use Tap\Tap;
use Tap\TapObject;
use Tap\Token;

abstract class FeatureTestCase extends TestCase
{
    /**
     * @var string
     */
    protected static string $tapPrefix = 'cashier-test-';

    protected static array $test_card = [
            'card' => [
                'number' => '5111111111111118',
                'exp_month' => '05',
                'exp_year' => '21',
                'cvc' => '100',
                'name' => 'Amr Ahmed Asciisd',
                'address' => [
                    'country' => 'Kuwait',
                    'line1' => 'Salmiya, 21',
                    'city' => 'Kuwait city',
                    'street' => 'Salim',
                    'avenue' => 'Gulf',
                ],
            ],
            'client_ip' => '192.168.1.20'
        ];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        Tap::setApiKey(getenv('TAP_API_KEY'));
    }

    protected function setUp(): void
    {
        // Delay consecutive tests to prevent Tap rate limiting issues.
        sleep(2);

        parent::setUp();

        Eloquent::unguard();

        $this->loadLaravelMigrations();

        $this->artisan('migrate')->run();

    }

    protected static function deleteTapResource(ApiResource $resource)
    {
        try {
            $resource->delete();
        } catch (InvalidRequestException $e) {
            //
        }
    }

    protected function createCustomer($description = 'aemad'): User
    {
        return User::create([
            'email' => "{$description}@cashier-test.com",
            'first_name' => 'Amr',
            'last_name' => 'Ahmed',
            'name' => 'Amr Ahmed',
            'phone' => '010123456789',
            'phone_code' => '002',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ]);
    }

    protected function createCard($customer_id): TapObject {
        $token = $this->createToken();

        dump("token created : $token->id");
        dump("initiating card for customer : $customer_id");

        return Card::createFromSource($customer_id, $token->id);
//        return Card::verify([
//            'customer' => ['id' => $customer_id],
//            'source' => ['id' => $token->id],
//            'save_card' => true,
//            'threeDSecure' => false,
//            'currency' => 'USD',
//            'redirect' => ['url' => 'https://payment.test/tap/handle']
//        ]);
    }

    /**
     * create token from test card
     *
     * @return array|\Tap\Customer|TapObject
     */
    protected function createToken()
    {
        return Token::create(self::$test_card);
    }
}
