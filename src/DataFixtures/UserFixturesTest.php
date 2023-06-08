<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixturesTest extends Fixture implements FixtureGroupInterface
{
    /**
     * @var string
     */
    final public const PASSWORD = 'test';

    final public const DATA = [
        [
            'key' => 'admin',
            'firstName' => 'Адам',
            'lastName' => 'Пенкин',
            'middleName' => 'Геннадиевич',
            'email' => 'admin@mtec.by',
            'roles' => [User::ROLE_USER, User::ROLE_ADMIN],
            'isActive' => true,
        ],
        [
            'key' => 'user',
            'firstName' => 'Агафья',
            'lastName' => 'Бетрозова',
            'middleName' => 'Иосифовна',
            'email' => 'user@mtec.by',
            'roles' => [User::ROLE_USER],
            'isActive' => true,
        ],
        [
            'firstName' => 'Эмма',
            'lastName' => 'Софийская',
            'middleName' => 'Викторовна',
            'email' => 'disabled.admin@mtec.by',
            'roles' => [User::ROLE_USER, User::ROLE_ADMIN],
            'isActive' => false,
        ],
        [
            'firstName' => 'Антон',
            'lastName' => 'Лучной',
            'middleName' => 'Геннадиевич',
            'email' => 'disabled.user@mtec.by',
            'roles' => [User::ROLE_USER],
            'isActive' => false,
        ],
    ];

    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::DATA as $user) {
            try {
                $entity = new User();

                $entity->setFirstName($user['firstName']);
                $entity->setLastName($user['lastName']);

                if (array_key_exists('middleName', $user)) {
                    $entity->setMiddleName($user['middleName']);
                }

                $entity->setEmail($user['email']);
                $entity->setRoles($user['roles']);
                $entity->setPassword($this->hasher->hashPassword($entity, self::PASSWORD));

                $entity->setIsActive($user['isActive']);
                $entity->setIsDeleted(false);

                $manager->persist($entity);

                if (array_key_exists('key', $user)) {
                    $this->setReference(sprintf('user: %s', $user['key']), $entity);
                }
            } catch (\Throwable $throwable) {
                throw new \Exception(sprintf('Failed for the user %s %s %s', $user['firstName'] ?? 'EMPTY', $user['lastName'] ?? 'EMPTY', $user['middleName'] ?? ''), $throwable->getCode(), previous: $throwable);
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['test'];
    }
}
