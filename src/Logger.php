<?php

namespace Asciisd\Cashier;

use Psr\Log\LoggerInterface;
use Tap\Util\LoggerInterface as TapLogger;

class Logger implements TapLogger
{
    /**
     * The Logger instance.
     */
    protected LoggerInterface $logger;

    /**
     * Create a new Logger instance.
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function error($message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }
}
