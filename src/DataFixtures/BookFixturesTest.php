<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Publisher;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BookFixturesTest extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $books = [
            'test 1',
            'test 2',
        ];

        foreach ($books as $data) {
            $book = new Book();
            $book->setName($book);
            $book->setDatePublished(new \DateTime());
            $book->setDescription('test');
            $book->setPageCount(100);
            $book->setPublisher($manager->getRepository(Publisher::class)->first());
            $book->addAuthor($manager->getRepository(Author::class)->findOneBy([]));

            foreach ($manager->getRepository(Tag::class)->findBy([]) as $tag) {
                $book->addTag($tag);
            }

            $manager->persist($book);

            $this->setReference(sprintf('book: %s', $book->getName()), $book);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['test'];
    }

    public function getDependencies()
    {
        return [AuthorFixturesTest::class];

        return [PublisherFixturesTest::class];

        return [TagFixturesTest::class];
    }
}
