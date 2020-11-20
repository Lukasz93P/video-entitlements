<?php

declare(strict_types=1);

namespace Modules\SharedKernel\Application\Middlewares;

use Illuminate\Bus\Dispatcher;
use Illuminate\Contracts\Bus\QueueingDispatcher;

class MiddlewareFiringDispatcher implements QueueingDispatcher
{
    private const METHOD_TO_GET_MIDDLEWARE_FROM_JOB = 'middleware';

    private Dispatcher $decoratedDispatcher;

    public function __construct(Dispatcher $decoratedDispatcher)
    {
        $this->decoratedDispatcher = $decoratedDispatcher;
    }

    public function dispatch($command)
    {
        $this->prepareMiddlewares($command)->dispatch($command);
    }

    public function dispatchNow($command, $handler = null)
    {
        $this->prepareMiddlewares($command)->dispatchNow($command);
    }

    public function hasCommandHandler($command): bool
    {
        return $this->decoratedDispatcher->hasCommandHandler($command);
    }

    public function getCommandHandler($command)
    {
        return $this->decoratedDispatcher->getCommandHandler($command);
    }

    public function pipeThrough(array $pipes)
    {
        return $this->decoratedDispatcher->pipeThrough($pipes);
    }

    public function map(array $map)
    {
        return $this->decoratedDispatcher->map($map);
    }

    public function dispatchToQueue($command)
    {
        return $this->decoratedDispatcher->dispatchToQueue($command);
    }

    public function dispatchSync($command, $handler = null)
    {
        return $this->prepareMiddlewares($command)->dispatchSync($command, $handler);
    }

    public function findBatch(string $batchId)
    {
        return $this->decoratedDispatcher->findBatch($batchId);
    }

    public function batch($jobs)
    {
        return $this->decoratedDispatcher->batch($jobs);
    }

    private function prepareMiddlewares($command): Dispatcher
    {
        $middleware = is_callable([$command, self::METHOD_TO_GET_MIDDLEWARE_FROM_JOB])
            ? $command->{self::METHOD_TO_GET_MIDDLEWARE_FROM_JOB}()
            : [];

        $this->decoratedDispatcher->pipeThrough($middleware);

        return $this->decoratedDispatcher;
    }
}
