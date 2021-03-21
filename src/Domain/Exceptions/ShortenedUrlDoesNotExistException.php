<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

class ShortenedUrlDoesNotExistException extends \InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('The shortened url does not exist');
    }
}
