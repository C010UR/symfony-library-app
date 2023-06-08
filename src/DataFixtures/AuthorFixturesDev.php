<?php

namespace App\DataFixtures;

use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class AuthorFixturesDev extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private readonly string $dirPublic,
        private readonly string $dirBookAuthorUploads,
        private readonly string $dirFixtures,
    ) {
    }

    private function createFromFile(array $file): Author
    {
        $name = preg_replace('/(.*)\. /U', '', pathinfo((string) $file['filename'], PATHINFO_FILENAME));
        $name = ucwords(strtolower($name));
        $name = explode(' ', $name);

        $filename = sprintf(
            '%s/author-%s.%s',
            $this->dirBookAuthorUploads,
            bin2hex(random_bytes(3)),
            $file['extension']
        );

        copy($file['full_path'], sprintf('%s%s', $this->dirPublic, $filename));

        $author = new Author();
        $author->setEmail('example@email.com');
        $author->setImagePath($filename);
        $author->setWebsite('https://example.com');

        if (array_key_exists(0, $name)) {
            $author->setFirstName($name[0]);
        }

        if (array_key_exists(1, $name)) {
            $author->setLastName($name[1]);
        }

        if (array_key_exists(2, $name)) {
            $author->setMiddleName($name[2]);
        }

        return $author;
    }

    public function load(ObjectManager $manager): void
    {
        if (!file_exists(sprintf('%s%s', $this->dirPublic, $this->dirBookAuthorUploads))) {
            mkdir(sprintf('%s%s', $this->dirPublic, $this->dirBookAuthorUploads), 0777, true);
        }

        foreach (new \DirectoryIterator(sprintf('%s%s', $this->dirFixtures, $this->dirBookAuthorUploads)) as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $author = $this->createFromFile(
                [
                    'full_path' => $file->getPathname(),
                    'filename' => $file->getFilename(),
                    'extension' => $file->getExtension(),
                ],
            );

            $manager->persist($author);

            $this->setReference(sprintf('author: %s', $author->getFullName()), $author);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['dev'];
    }
}
