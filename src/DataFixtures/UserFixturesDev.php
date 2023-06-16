<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Utils\FileUtils;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixturesDev extends Fixture implements FixtureGroupInterface
{
    /**
     * @var string
     */
    final public const PASSWORD = 'dev';

    final public const DATA = [
        [
            'firstName' => 'Адам',
            'lastName' => 'Пенкин',
            'middleName' => 'Геннадиевич',
            'image' => 'adam-penkin.jpg',
            'email' => 'adam.penkin@mtec.by',
            'roles' => [User::ROLE_USER],
        ],
        [
            'firstName' => 'Агафья',
            'lastName' => 'Бетрозова',
            'middleName' => 'Иосифовна',
            'image' => 'agafya-betrozova.jpg',
            'email' => 'agafya.betrozova@mtec.by',
            'roles' => [User::ROLE_USER],
        ],
        [
            'firstName' => 'Эмма',
            'lastName' => 'Софийская',
            'middleName' => 'Викторовна',
            'image' => 'emma-sofiyskaya.jpg',
            'email' => 'emma.sofiyskaya@mtec.by',
            'roles' => [User::ROLE_USER],
        ],
        [
            'firstName' => 'Антон',
            'lastName' => 'Лучной',
            'middleName' => 'Геннадиевич',
            'image' => 'anton-luchnoy.jpg',
            'email' => 'anton.luchnoy@mtec.by',
            'roles' => [User::ROLE_USER],
        ],
        [
            'firstName' => 'Васильев',
            'lastName' => 'Парфен',
            'middleName' => 'Закирович',
            'image' => 'parfen-vasilev.jpg',
            'email' => 'parfen.vasilev@mtec.by',
            'roles' => [User::ROLE_USER],
        ],
        [
            'firstName' => 'Яковенко',
            'lastName' => 'Яковенко',
            'middleName' => 'Ефимовна',
            'image' => 'pelageya-yakovenko.jpg',
            'email' => 'pelageya.yakovenko@mtec.by',
            'roles' => [User::ROLE_USER],
        ],
        [
            'firstName' => 'Виктория',
            'lastName' => 'Иванова',
            'middleName' => 'Александровна',
            'image' => 'viktoriya-ivanova.jpg',
            'email' => 'viktoriya.ivanova@mtec.by',
            'roles' => [User::ROLE_USER],
        ],
        [
            'firstName' => 'Фелицата',
            'lastName' => 'Кулагина',
            'middleName' => 'Антоновна',
            'image' => 'felitsata-kulagina.jpg',
            'email' => 'felitsata.kulagina@mtec.by',
            'roles' => [User::ROLE_USER, User::ROLE_ADMIN],
        ],
        [
            'key' => 'admin',
            'firstName' => 'Мрак',
            'lastName' => 'Лауренчикас',
            'middleName' => 'Павлович',
            'image' => 'mark-laurenchikas.jpg',
            'email' => 'lemur.laur@inbox.ru',
            'roles' => [User::ROLE_USER, User::ROLE_ADMIN],
        ],
        [
            'key' => 'user',
            'firstName' => 'Марк',
            'lastName' => 'Лауренчикас',
            'middleName' => 'Павлович',
            'image' => 'mark-laurenchikas.jpg',
            'email' => 'user.lemur.laur@inbox.ru',
            'roles' => [User::ROLE_USER],
        ],
    ];

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        private readonly string $dirPublic,
        private readonly string $dirUserUploads,
        private readonly string $dirFixtures,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        if (!file_exists(sprintf('%s%s', $this->dirPublic, $this->dirUserUploads))) {
            mkdir(sprintf('%s%s', $this->dirPublic, $this->dirUserUploads), 0777, true);
        }

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

                $entity->setIsActive(true);
                $entity->setIsDeleted(false);

                if (array_key_exists('image', $user)) {
                    $filename = FileUtils::joinPaths(
                        [
                            $this->dirUserUploads,
                            FileUtils::generateFilename('user', pathinfo($user['image'], \PATHINFO_EXTENSION)),
                        ]
                    );

                    copy(
                        FileUtils::joinPaths([$this->dirFixtures, $this->dirUserUploads, $user['image']]),
                        FileUtils::joinPaths([$this->dirPublic, $filename])
                    );

                    $entity->setImagePath($filename);
                }

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
        return ['dev'];
    }
}
