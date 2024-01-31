<?php

declare(strict_types=1);

namespace App\Subscriber;

use App\Exception\ValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof ValidationException) {
            $responseData = [];
            $responseData['message'] = $exception->getMessage();
            $responseData['errors'] = $exception->getErrors();
            $responseData['codes'] = $exception->getMappedCodes();
            $response = new JsonResponse($responseData);
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
