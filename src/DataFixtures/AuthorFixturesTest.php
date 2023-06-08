<?php

namespace App\DataFixtures;

use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class AuthorFixturesTest extends Fixture implements FixtureGroupInterface
{
    /**
     * @var array<string, string>[]
     */
    final public const DATA = [
        [
            'key' => 'test-1',
            'firstName' => 'Аарон',
            'lastName' => 'Курвилль',
            'email' => 'courvila@iro.umontreal.ca',
            'image' => 'aaron-courville.jpg',
            'website' => 'https://mila.quebec/en/person/aaron-courville/',
        ],
        [
            'key' => 'test-2',
            'firstName' => 'Адитья',
            'lastName' => 'Бхаргава',
            'image' => 'aditya-bhargava.jpg',
            'website' => 'https://www.linkedin.com/in/adityabhargava/',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::DATA as $author) {
            try {
                $entity = new Author();

                $entity->setFirstName($author['firstName']);
                $entity->setLastName($author['lastName']);

                if (array_key_exists('middleName', $author)) {
                    $entity->setMiddleName($author['middleName']);
                }

                if (array_key_exists('website', $author)) {
                    $entity->setWebsite($author['website']);
                }

                if (array_key_exists('email', $author)) {
                    $entity->setEmail($author['email']);
                }

                $entity->setIsDeleted(false);

                $manager->persist($entity);

                if (array_key_exists('key', $author)) {
                    $this->setReference(sprintf('author: %s', $author['key']), $entity);
                }
            } catch (\Throwable $throwable) {
                throw new \Exception(sprintf('Failed for the author %s %s %s', $author['firstName'] ?? 'EMPTY', $author['lastName'] ?? 'EMPTY', $author['middleName'] ?? ''), $throwable->getCode(), previous: $throwable);
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['test'];
    }
}
