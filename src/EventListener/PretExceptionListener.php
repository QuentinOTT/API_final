<?php

namespace App\EventListener;

use App\Exception\PretAccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PretExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        
        // Vu00e9rifier si c'est notre exception personnalisu00e9e
        if ($exception instanceof PretAccessDeniedException) {
            $data = [
                '@context' => '/apiplatform/contexts/Error',
                '@type' => 'hydra:Error',
                'hydra:title' => 'Accu00e8s refusu00e9',
                'hydra:description' => $exception->getMessage(),
            ];
            
            $response = new JsonResponse($data, 403);
            $response->headers->set('Content-Type', 'application/ld+json');
            
            $event->setResponse($response);
        }
    }
}
