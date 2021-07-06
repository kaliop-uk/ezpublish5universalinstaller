<?php

namespace Kaliop\eZP5UI\Common;

use Psr\Log\LoggerInterface;

/**
 * Could be a Trait, but we strive for php 5.3 compat...
 */
class Logger
{
    protected $logger;

    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
        return $this; // fluent interfaces for setters
    }

    protected function emergency($message, array $context = array())
    {
        if ($this->logger) $this->logger->emergency($message, $context);
    }

    protected function alert($message, array $context = array())
    {
        if ($this->logger) $this->logger->alert($message, $context);
    }

    protected function critical($message, array $context = array())
    {
        if ($this->logger) $this->logger->critical($message, $context);
    }

    protected function error($message, array $context = array())
    {
        if ($this->logger) $this->logger->error($message, $context);
    }

    protected function warning($message, array $context = array())
    {
        if ($this->logger) $this->logger->warning($message, $context);
    }

    protected function notice($message, array $context = array())
    {
        if ($this->logger) $this->logger->notice($message, $context);
    }

    protected function info($message, array $context = array())
    {
        if ($this->logger) $this->logger->info($message, $context);
    }

    protected function debug($message, array $context = array())
    {
        if ($this->logger) $this->logger->debug($message, $context);
    }

    protected function log($level, $message, array $context = array())
    {
        if ($this->logger) $this->logger->log($level, $message, $context);
    }
}