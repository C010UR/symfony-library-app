<?php

namespace App\DataFixtures;

use App\Entity\Publisher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class PublisherFixturesTest extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $publishers = [
            'test 1',
            'test 2',
        ];

        foreach ($publishers as $data) {
            $publisher = new Publisher();
            $publisher->setName($data);
            $publisher->setAddress('-');
            $publisher->setEmail('example@email.com');
            $publisher->setWebsite('https://example.com');

            $manager->persist($publisher);

            $this->setReference(sprintf('publisher: %s', $publisher->getName()), $publisher);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['test'];
    }
}
