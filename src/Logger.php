<?php

namespace Asciisd\Cashier;

use Psr\Log\LoggerInterface;
use Tap\Util\LoggerInterface as TapLogger;

class Logger implements TapLogger
{
    /**
     * The Logger instance.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Create a new Logger instance.
     *
     * @param LoggerInterface $logger
     * @return void
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function error($message, array $context = [])
    {
        $this->logger->error($message, $context);
    }
}
