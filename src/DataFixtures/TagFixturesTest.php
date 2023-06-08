<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class TagFixturesTest extends Fixture implements FixtureGroupInterface
{
    /**
     * @var array<string, string>[]
     */
    final public const DATA = [
        [
            'key' => 'test-1',
            'name' => 'test-1',
        ],
        [
            'key' => 'test-2',
            'name' => 'test-2',
        ],
        [
            'key' => 'test-3',
            'name' => 'test-3',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::DATA as $tag) {
            try {
                $entity = new Tag();

                $entity->setName($tag['name']);

                $manager->persist($entity);

                if (array_key_exists('key', $tag)) {
                    $this->setReference(sprintf('tag: %s', $tag['key']), $entity);
                }
            } catch (\Throwable $throwable) {
                throw new \Exception(sprintf('Failed for the tag %s', $tag['name'] ?? 'EMPTY'), $throwable->getCode(), previous: $throwable);
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['test'];
    }
}
