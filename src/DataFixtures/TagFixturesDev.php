<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class TagFixturesDev extends Fixture implements FixtureGroupInterface
{
    final public const DATA = [
        [
            'key' => 'fantasy',
            'name' => 'Фантастика',
        ],
        [
            'key' => 'science-fiction',
            'name' => 'Научная Фантастика',
        ],
        [
            'key' => 'dystopian',
            'name' => 'Дистопия',
        ],
        [
            'key' => 'action',
            'name' => 'Экшн',
        ],
        [
            'key' => 'adventure',
            'name' => 'Приключения',
        ],
        [
            'key' => 'mystery',
            'name' => 'Мистика',
        ],
        [
            'key' => 'horror',
            'name' => 'Хоррор',
        ],
        [
            'key' => 'thriller',
            'name' => 'Триллер',
        ],
        [
            'key' => 'historical-fiction',
            'name' => 'Историческая Фантастика',
        ],
        [
            'key' => 'romance',
            'name' => 'Роман',
        ],
        [
            'key' => 'science',
            'name' => 'Наука',
        ],
        [
            'key' => 'textbook',
            'name' => 'Учебник',
        ],
        [
            'key' => 'video-games',
            'name' => 'Видеоигры',
        ],
        [
            'key' => 'math',
            'name' => 'Математика',
        ],
        [
            'key' => 'physics',
            'name' => 'Физика',
        ],
        [
            'key' => 'economics',
            'name' => 'Экономика',
        ],
        [
            'key' => 'programming',
            'name' => 'Программирование',
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
        return ['dev'];
    }
}
