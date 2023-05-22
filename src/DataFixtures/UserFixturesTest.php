<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixturesTest extends Fixture implements FixtureGroupInterface
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    private function createEmail(string $name): string
    {
        return sprintf('test.email.%s@mtec.by', $name);
    }

    public function load(ObjectManager $manager): void
    {
        $roleAdmin = 'ROLE_ADMIN';
        $roleUser = 'ROLE_USER';
        $password = 'test.password';

        $users = [
            ['admin', $roleAdmin, true],
            ['user', $roleUser, true],
            ['disabled.admin', $roleAdmin, false],
            ['disabled.user', $roleUser, false],
        ];

        foreach ($users as $data) {
            $user = new User();
            $user->setEmail($this->createEmail($data[0]));
            $user->setRoles([$data[1]]);
            $hashed = $this->hasher->hashPassword($user, $password);
            $user->setPassword($hashed);
            $user->setIsActive($data[2]);

            $manager->persist($user);

            $this->setReference(sprintf('user: %s', $user->getEmail()), $user);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['test'];
    }
}
