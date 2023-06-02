<?php

namespace App\DataFixtures;

use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class AuthorFixturesTest extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $authors = [
            'test 1',
            'test 2',
        ];

        foreach ($authors as $data) {
            $author = new Author();
            $author->setFirstName($data);
            $author->setLastName($data);
            $author->setEmail('example@email.com');
            $author->setWebsite('https://example.com');

            $manager->persist($author);

            $this->setReference(sprintf('author: %s', $author->getFullName()), $author);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['test'];
    }
}
