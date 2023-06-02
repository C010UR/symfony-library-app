<?php

namespace App\DataFixtures;

use App\Entity\Publisher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PublisherFixturesDev extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private string $dirPublic,
        private string $dirBookPublisherUploads,
        private string $dirFixtures,
    ) {
    }

    private function createFromFile(array $file): Publisher
    {
        $name = preg_replace('/(.*)\. /U', '', pathinfo($file['filename'], PATHINFO_FILENAME));
        $name = ucwords(strtolower($name));

        $filename = sprintf('%s/publisher-%s.%s', $this->dirBookPublisherUploads, bin2hex(random_bytes(3)), $file['extension']);

        copy($file['full_path'], sprintf('%s%s', $this->dirPublic, $filename));

        $publisher = new Publisher();
        $publisher->setAddress('Washington DC, 20090-6178');
        $publisher->setEmail('membership@aaas.org');
        $publisher->setName($name);
        $publisher->setImagePath($filename);
        $publisher->setWebsite('https://www.science.org/');

        return $publisher;
    }

    public function load(ObjectManager $manager): void
    {
        if (!file_exists(sprintf('%s%s', $this->dirPublic, $this->dirBookPublisherUploads))) {
            mkdir(sprintf('%s%s', $this->dirPublic, $this->dirBookPublisherUploads), 0777, true);
        }

        foreach (new \DirectoryIterator(sprintf('%s%s', $this->dirFixtures, $this->dirBookPublisherUploads)) as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $publisher = $this->createFromFile(
                [
                    'full_path' => $file->getPathname(),
                    'filename' => $file->getFilename(),
                    'extension' => $file->getExtension(),
                ],
            );

            $manager->persist($publisher);

            $this->setReference(sprintf('publisher: %s', $publisher->getName()), $publisher);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['dev'];
    }
}
