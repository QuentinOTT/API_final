<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PretAccessDeniedException extends AccessDeniedException
{
    public function __construct(string $message = "Ce prêt n'est pas le vôtre", \Throwable $previous = null)
    {
        parent::__construct($message, 403, $previous);
    }
}
