<?php

namespace App\Security\Exception;

use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Symfony\Component\HttpKernel\Attribute\WithLogLevel;

#[WithHttpStatus(Response::HTTP_UNAUTHORIZED, headers: ['WWW-Authenticate' => '-'])]
#[WithLogLevel(LogLevel::DEBUG)]
class AccountDeactivated extends \Exception
{
    /**
     * @var string
     */
    final public const MESSAGE = 'Too many attempts. Account was deactivated. Please reset the password.';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}
