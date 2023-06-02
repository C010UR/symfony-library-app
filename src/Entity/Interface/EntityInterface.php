<?php

namespace App\Entity\Interface;

use Symfony\Component\String\Slugger\SluggerInterface;

interface EntityInterface
{
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
