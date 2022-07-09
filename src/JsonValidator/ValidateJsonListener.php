<?php

declare(strict_types=1);

namespace App\JsonValidator;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ValidateJsonListener implements EventSubscriberInterface
{
    public function __construct(private readonly JsonValidator $jsonValidator)
    {
    }

    /**
     * @return array<string,array<mixed>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => [
                // Sensio\Bundle\FrameworkExtraBundle\EventListener\ControllerListener is priority 0
                [ 'validateJson', -1 ],
            ],
            KernelEvents::EXCEPTION => [
                [ 'handleException', 0 ],
            ],
        ];
    }

    public function validateJson(ControllerEvent $event): void
    {
        $request = $event->getRequest();

        // added by ControllerListener based on the alias of the annotation if it's a ConfigurationAnnotation
        if (!$request->attributes->has('_validate_json')) {
            return;
        }

        $annotation = $request->attributes->get('_validate_json');
        if (!($annotation instanceof ValidateJson)) {
            return;
        }

        $validJson = $this->jsonValidator->validateJsonRequest($request, strval($annotation->getPath()));
        $request->attributes->set('validJson', $validJson);
    }

    public function handleException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!($exception instanceof JsonValidationException)) {
            return;
        }

        $event->setResponse(new JsonResponse([
            'status' => Response::HTTP_BAD_REQUEST,
            'title' => 'Unable to validate sent JSON against schema',
            'code' => $exception->getCode(),
            'detail' => $exception->getMessage(),
            'errors' => $exception->getErrors(),
        ], Response::HTTP_BAD_REQUEST, [
            'Content-Type' => 'application/problem+json',
        ]));
    }
}
