<?php


namespace Asciisd\Cashier\Tests\Feature;


use Asciisd\Cashier\Logger;
use Asciisd\Cashier\Tests\TestCase;
use Mockery as m;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Tap\Tap;
use Tap\Util\DefaultLogger;
use Tap\Util\LoggerInterface;

class LoggerTest extends TestCase
{
    /** @var string|null */
    protected $channel;

    public function tearDown(): void
    {
        config(['cashier.logger' => null]);

        parent::tearDown();
    }

    public function test_the_logger_is_correctly_bound()
    {
        $logger = $this->app->make(LoggerInterface::class);

        $this->assertInstanceOf(
            Logger::class,
            $logger,
            'Failed asserting that the Tap logger interface is bound to the Cashier logger.'
        );

        $this->assertInstanceOf(
            LoggerInterface::class,
            $logger,
            'Failed asserting that the Cashier logger implements the Tap logger interface.'
        );
    }

    public function test_the_logger_uses_a_log_channel()
    {
        $channel = m::mock(PsrLoggerInterface::class);
        $channel->shouldReceive('error')->once()->with('foo', ['bar']);

        $this->mock('log', function ($logger) use ($channel) {
            $logger->shouldReceive('channel')->with('default')->once()->andReturn($channel);
        });

        config(['cashier.logger' => 'default']);

        $logger = $this->app->make(LoggerInterface::class);

        $logger->error('foo', ['bar']);
    }

    public function test_it_uses_the_default_tap_logger()
    {
        $logger = Tap::getLogger();

        $this->assertInstanceOf(
            DefaultLogger::class,
            $logger,
            'Failed asserting that Tap uses its own logger.'
        );
    }

    public function test_it_uses_a_configured_logger()
    {
        $this->channel = 'default';

        $this->refreshApplication();

        $logger = Tap::getLogger();

        $this->assertInstanceOf(
            Logger::class,
            $logger,
            'Failed asserting that Tap uses the Cashier logger.'
        );
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('cashier.logger', $this->channel);
    }
}
