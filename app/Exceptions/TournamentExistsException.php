<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response as Response;

class TournamentExistsException extends Exception
{
    protected $message;
    protected int $statusCode;

    public function __construct($message = "A tournament games already exists, so this action is not allowed.", $statusCode = Response::HTTP_FORBIDDEN)
    {
        $this->message = $message;
        $this->statusCode = $statusCode;

        parent::__construct($message);
    }
}
