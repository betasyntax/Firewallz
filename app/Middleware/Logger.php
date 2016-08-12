<?php namespace App\Middleware;

use League\Tactician\Middleware;

class Logger implements Middleware
{
    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function execute($command, callable $next)
    {
        $commandClass = get_class($command);

        $this->logger->log("Starting $commandClass");
        $returnValue = $next($command);
        $this->logger->log("$commandClass finished without errors");

        return $returnValue;
    }
}