<?php

namespace App\Serializer;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ErrorNormalizer implements NormalizerInterface
{
    public function __construct(private readonly string $environment)
    {
    }

    public function normalize($exception, string $format = null, array $context = []): array
    {
        /** @var FlattenException $exception */
        $message = $exception->getMessage();

        // change message
        switch ($exception->getClass()) {
            case ForeignKeyConstraintViolationException::class:
                $message = 'This row is used and can not be deleted.';
                break;
            case NotEncodableValueException::class:
            case NotNormalizableValueException::class:
                $message = 'Invalid JSON.';
                break;
        }

        if ('dev' !== $this->environment) {
            return [
                'exception' => [
                    'message' => $message,
                    'code' => $exception->getStatusCode(),
                ],
            ];
        }

        return [
            'exception' => array_merge(['message' => $message, 'code' => $exception->getStatusCode()], $exception->toArray()),
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof FlattenException;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            FlattenException::class => __CLASS__ === self::class,
        ];
    }
}
