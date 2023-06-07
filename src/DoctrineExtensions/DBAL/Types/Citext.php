<?php

declare(strict_types=1);

namespace App\DoctrineExtensions\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\TextType;

final class Citext extends TextType
{
    /**
     * @var string
     */
    public const CITEXT = 'citext';

    public function getName(): string
    {
        return self::CITEXT;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $platform->getDoctrineTypeMapping(self::CITEXT);
    }
}
