<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Publisher;
use App\Entity\Tag;
use App\Utils\Utils;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class BookFixturesDev extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private string $dirPublic,
        private string $dirBookCoverUploads,
        private string $dirFixtures,
    ) {
    }

    private function createFromFile(ObjectManager $manager, array $file): Book
    {
        preg_match('/(.*)\. /U', $file['filename'], $preg);

        if (empty($preg[1])) {
            throw new \InvalidArgumentException(sprintf('File %s has wrong format.', $file['filename']));
        }

        $authors = explode(',', $preg[1]);

        $name = preg_replace('/(.*)\. /U', '', pathinfo($file['filename'], PATHINFO_FILENAME));
        $name = ucwords(strtolower($name));

        $filename = sprintf('%s/cover-%s.%s', $this->dirBookCoverUploads, bin2hex(random_bytes(3)), $file['extension']);

        copy($file['full_path'], sprintf('%s%s', $this->dirPublic, $filename));

        $book = new Book();
        $book->setName($name);
        $book->setDatePublished(new \DateTime());
        $book->setDescription(Utils::getLoremIpsum());
        $book->setImagePath($filename);
        $book->setPageCount(rand(100, 600));
        $book->setPublisher($manager->getRepository(Publisher::class)->findOneBy([]));

        foreach ($authors as $author) {
            $author = ucwords(strtolower($author));

            $book->addAuthor($manager
                ->getRepository(Author::class)
                ->matching(
                    Criteria::create()
                        ->andWhere(
                            Criteria::expr()->contains('firstName', trim($author))
                        )
                )
                ->first());
        }

        foreach ($manager->getRepository(Tag::class)->findBy([]) as $tag) {
            $book->addTag($tag);
        }

        return $book;
    }

    public function load(ObjectManager $manager): void
    {
        if (!file_exists(sprintf('%s%s', $this->dirPublic, $this->dirBookCoverUploads))) {
            mkdir(sprintf('%s%s', $this->dirPublic, $this->dirBookCoverUploads), 0777, true);
        }

        foreach (new \DirectoryIterator(sprintf('%s%s', $this->dirFixtures, $this->dirBookCoverUploads)) as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $book = $this->createFromFile(
                $manager,
                [
                    'full_path' => $file->getPathname(),
                    'filename' => $file->getFilename(),
                    'extension' => $file->getExtension(),
                ],
            );

            $manager->persist($book);

            $this->setReference(sprintf('book: %s', $book->getName()), $book);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['dev'];
    }

    public function getDependencies()
    {
        return [AuthorFixturesDev::class];

        return [PublisherFixturesDev::class];

        return [TagFixturesDev::class];
    }
}
