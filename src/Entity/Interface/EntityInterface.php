<?php

namespace App\Entity\Interface;

use Symfony\Component\String\Slugger\SluggerInterface;

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
     * Computes slug for the entity.
     */
    public function computeSlug(SluggerInterface $slugger): void;

    /**
     * Formats entity into array type.
     */
    public function format(): array;
}
