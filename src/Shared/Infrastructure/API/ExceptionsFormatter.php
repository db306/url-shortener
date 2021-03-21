<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\API;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionsFormatter implements EventSubscriberInterface
{
    private RequestStack $request;

    public function __construct(RequestStack $request)
    {
        $this->request = $request;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['outputException', 10],
            ],
        ];
    }

    public function outputException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        $statusCode = 500;
        if (method_exists($exception, 'getCode')) {
            $statusCode = $exception->getCode() ? $exception->getCode() : 500;
        }

        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode() ? $exception->getStatusCode() : 500;
        }

        $event->setResponse(
            new ApiResponse($exception->getMessage(), null, [$statusCode => $exception->getMessage()], $statusCode, [])
        );
    }
}
