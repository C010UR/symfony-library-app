<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixturesDev extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        private readonly string $dirPublic,
        private readonly string $dirUserUploads,
        private readonly string $dirFixtures,
    ) {
    }

    private function createEmail(string $postfix): string
    {
        return sprintf('%s@mtec.by', $postfix);
    }

    private function createFromFile(array $file): User
    {
        preg_match('/(.*)\. /U', (string) $file['filename'], $preg);
        if (empty($preg[1])) {
            throw new \InvalidArgumentException(sprintf('File %s has wrong format.', $file['filename']));
        }

        $role = sprintf('ROLE_%s', strtoupper($preg[1]));

        $name = explode(' ', preg_replace('/(.*)\. /U', '', pathinfo((string) $file['filename'], PATHINFO_FILENAME)));

        $email = $this->createEmail(
            implode('.', $name),
        );

        $filename = sprintf('%s/user-%s.%s', $this->dirUserUploads, bin2hex(random_bytes(3)), $file['extension']);

        copy($file['full_path'], sprintf('%s%s', $this->dirPublic, $filename));

        $user = new User();
        $user->setEmail($email);
        $user->setRoles([$role]);

        $hashed = $this->hasher->hashPassword($user, 'dev');
        $user->setPassword($hashed);
        $user->setImagePath($filename);
        $user->setIsActive(true);

        if (array_key_exists(0, $name)) {
            $user->setFirstName($name[0]);
        }

        if (array_key_exists(1, $name)) {
            $user->setLastName($name[1]);
        }

        if (array_key_exists(2, $name)) {
            $user->setMiddleName($name[2]);
        }

        return $user;
    }

    public function load(ObjectManager $manager): void
    {
        if (!file_exists(sprintf('%s%s', $this->dirPublic, $this->dirUserUploads))) {
            mkdir(sprintf('%s%s', $this->dirPublic, $this->dirUserUploads), 0777, true);
        }

        foreach (new \DirectoryIterator(sprintf('%s%s', $this->dirFixtures, $this->dirUserUploads)) as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $user = $this->createFromFile(
                [
                    'full_path' => $file->getPathname(),
                    'filename' => $file->getFilename(),
                    'extension' => $file->getExtension(),
                ],
            );

            $manager->persist($user);

            $this->setReference(sprintf('user: %s', $user->getEmail()), $user);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['dev'];
    }
}
