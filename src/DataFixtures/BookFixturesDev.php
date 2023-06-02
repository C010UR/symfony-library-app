<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Publisher;
use App\Entity\Tag;
use DateTime;
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
        $book->setDatePublished(new DateTime());
        $book->setDescription(<<<TEXT
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec hendrerit, purus at rutrum malesuada, massa risus sodales leo, vel efficitur nunc dui non tellus. Nam vulputate urna metus, ut maximus risus congue et. Suspendisse potenti. Vivamus tempor odio dui, sed aliquam diam hendrerit nec. Fusce vitae sem turpis. Quisque condimentum odio sapien, et blandit dui ullamcorper eget. Nullam interdum ullamcorper condimentum. Nam sagittis sem sed leo lobortis tempor. Etiam ultricies vel est vitae rhoncus. Aenean aliquam auctor libero, in vehicula tellus. Maecenas vestibulum ac augue nec ornare. Proin vitae enim id dolor finibus commodo sed non libero. Vestibulum id ex sit amet sapien consectetur consequat.
            Etiam sit amet euismod massa. Maecenas sit amet dictum nulla. In porta ligula eget suscipit pharetra. Donec eget leo feugiat, pellentesque elit in, molestie ante. Aenean consequat ullamcorper nunc, in lobortis dui malesuada sed. Aenean dapibus urna nunc, nec congue sapien luctus a. Suspendisse semper laoreet eros, semper cursus nisl viverra sit amet. In purus nisi, ullamcorper nec nisl id, ultricies mollis libero. In pretium id nulla sed mollis. Morbi non elit ipsum. Etiam nec massa eget nunc vehicula feugiat.
            Quisque vitae turpis non lacus bibendum auctor. Nam tempor finibus dolor in condimentum. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Vivamus venenatis lacus vel ante volutpat pulvinar quis quis nulla. Vivamus congue vestibulum quam, eget fermentum augue dapibus ut. Aenean non elit imperdiet, fringilla nunc eu, pulvinar massa. Integer justo leo, molestie vel vehicula at, fermentum quis purus. Mauris condimentum, erat eget blandit vestibulum, ipsum odio lobortis turpis, in suscipit felis turpis vel erat. In eros augue, gravida eget convallis sodales, porttitor vitae turpis. Phasellus ac est non dui dignissim venenatis id vitae risus. Integer egestas lorem quam, malesuada efficitur ligula mattis vitae. Sed vel accumsan arcu. Sed volutpat odio ut semper maximus.
            Suspendisse euismod lacinia neque, non bibendum est porttitor quis. Sed lobortis orci et vehicula varius. Mauris molestie cursus nibh sit amet vehicula. Quisque commodo quam neque, eget suscipit lacus bibendum id. Donec id nibh neque. Sed mollis dolor ligula, ac maximus sem hendrerit id. Curabitur vitae mauris suscipit, porta ipsum sed, ultrices risus. Sed auctor arcu et nisi fringilla faucibus. Nullam vehicula nulla a ante sollicitudin porta. Phasellus efficitur odio nec nulla iaculis, eu porta risus commodo. Mauris consequat, dui sit amet placerat scelerisque, ipsum est egestas nisi, at feugiat risus ex sit amet ante. Sed nec libero dolor. Pellentesque placerat efficitur neque, non sagittis nulla sagittis ac. Mauris id sapien a felis dignissim aliquam. Vestibulum viverra faucibus nibh fermentum auctor.
            Donec feugiat, erat vitae pellentesque varius, mauris nibh eleifend dolor, vitae tempor odio nibh porta arcu. Donec varius felis neque, vitae mattis sem iaculis eget. Phasellus at nunc quis sapien cursus porttitor. Duis sit amet euismod lorem. Aliquam condimentum arcu interdum sapien tristique cursus. Fusce condimentum diam ligula, eu luctus libero scelerisque eu. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus dui augue, tempus et suscipit eu, suscipit quis nisi. Nullam non magna vel urna luctus mattis. Nullam vel mollis mauris. Vivamus a lorem id velit tristique pulvinar sit amet a urna. Praesent nec efficitur enim.
            TEXT);
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
                            Criteria::expr()->contains('firstName', $author)
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
