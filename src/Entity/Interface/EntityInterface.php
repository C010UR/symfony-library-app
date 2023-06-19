<?php

namespace App\Entity\Interface;

interface EntityInterface
{
    /**
     * Check if entity is deleted.
     */
    public function isDeleted(): bool;

    /**
     * Converts entity to string.
     */
    public function __toString(): string;

    /**
     * Formats entity into array type.
     */
    public function format(): array;
}
