<?php

namespace App\DataFixtures;

use App\Entity\Publisher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class PublisherFixturesTest extends Fixture implements FixtureGroupInterface
{
    /**
     * @var array<string, string>[]
     */
    final public const DATA = [
        [
            'key' => 'test-1',
            'name' => 'test-1',
            'address' => '123308, г. Москва, Зорге ул., д.1, стр.1',
            'email' => 'info@eksmo.ru',
            'website' => 'https://eksmo.ru',
            'image' => 'eksmo.png',
        ],
        [
            'key' => 'test-2',
            'name' => 'test-2',
            'address' => 'г. Москва, Пресненская наб., д.6, стр.2, БЦ «Империя»',
            'email' => 'support@ast.ru',
            'website' => 'https://ast.ru',
            'image' => 'act.png',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::DATA as $publisher) {
            try {
                $entity = new Publisher();

                $entity->setName($publisher['name']);
                $entity->setAddress($publisher['address']);
                $entity->setEmail($publisher['email']);
                $entity->setWebsite($publisher['website']);

                $entity->setIsDeleted(false);

                $manager->persist($entity);

                if (array_key_exists('key', $publisher)) {
                    $this->setReference(sprintf('publisher: %s', $publisher['key']), $entity);
                }
            } catch (\Throwable $throwable) {
                throw new \Exception(sprintf('Failed for the publisher %s', $publisher['name'] ?? 'EMPTY'), $throwable->getCode(), previous: $throwable);
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['test'];
    }
}
