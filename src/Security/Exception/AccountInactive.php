<?php

namespace App\Security\Exception;

use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Symfony\Component\HttpKernel\Attribute\WithLogLevel;

#[WithHttpStatus(Response::HTTP_UNAUTHORIZED, headers: ['WWW-Authenticate' => '-'])]
#[WithLogLevel(LogLevel::DEBUG)]
class AccountInactive extends \Exception
{
    /**
     * @var string
     */
    final public const MESSAGE = 'Account is not active. Please reset the password to reactive account.';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}
