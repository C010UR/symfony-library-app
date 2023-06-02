<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class TagFixturesTest extends Fixture implements FixtureGroupInterface
{

    public function load(ObjectManager $manager): void
    {
        $genres = [
            'test 1',
            'test 2',
        ];

        foreach ($genres as $genre) {
            $tag = new Tag();
            $tag->setName($genre);

            $manager->persist($tag);

            $this->setReference(sprintf('tag: %s', $tag->getName()), $tag);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['test'];
    }
}
