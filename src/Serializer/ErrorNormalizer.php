<?php

namespace App\Serializer;

use App\Utils\Filter\Exception\InvalidQueryExpressionException;
use App\Utils\Filter\Exception\InvalidQueryOrderException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;

class ErrorNormalizer implements NormalizerInterface
{
    public function __construct(private string $environment)
    {
    }

    public function normalize($exception, string $format = null, array $context = []): array
    {
        /** @var FlattenException $exception */
        $message = $exception->getMessage();
        $code = $exception->getStatusCode();

        // change message
        switch ($exception->getClass()) {
            case ForeignKeyConstraintViolationException::class:
                $message = 'This row is used and can not be deleted.';
                break;
            case NotEncodableValueException::class:
            case NotNormalizableValueException::class:
                $message = 'JSON is invalid.';
                break;
        }

        // change code
        switch ($exception->getClass()) {
            case ForeignKeyConstraintViolationException::class:
            case NotEncodableValueException::class:
            case NotNormalizableValueException::class:
            case ResetPasswordExceptionInterface::class:
            case InvalidQueryOrderException::class:
            case InvalidQueryExpressionException::class:
                $code = Response::HTTP_BAD_REQUEST;
                break;
        }

        $exception->setStatusCode($code);

        if ('dev' !== $this->environment) {
            return [
                'exception' => [
                    'message' => $message,
                    'code' => $code,
                ],
            ];
        }

        return [
            'exception' => array_merge(['message' => $message, 'code' => $code], $exception->toArray()),
        ];
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof FlattenException;
    }
}
