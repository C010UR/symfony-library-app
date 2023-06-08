<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Utils\FileUtils;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class AuthorFixturesDev extends Fixture implements FixtureGroupInterface
{
    final public const DATA = [
        [
            'key' => 'aaron-courville',
            'firstName' => 'Аарон',
            'lastName' => 'Курвилль',
            'email' => 'courvila@iro.umontreal.ca',
            'image' => 'aaron-courville.jpg',
            'website' => 'https://mila.quebec/en/person/aaron-courville/',
        ],
        [
            'key' => 'aditya-bhargava',
            'firstName' => 'Адитья',
            'lastName' => 'Бхаргава',
            'image' => 'aditya-bhargava.jpg',
            'website' => 'https://www.linkedin.com/in/adityabhargava/',
        ],
        [
            'key' => 'ben-straub',
            'firstName' => 'Бен',
            'lastName' => 'Страуб',
            'image' => 'ben-straub.jpg',
            'website' => 'https://ben.straub.cc',
        ],

        [
            'key' => 'galina-rasolko-alekseevna',
            'firstName' => 'Галина',
            'lastName' => 'Расолько',
            'middleName' => 'Алексеевна',
            'email' => ' rasolka@bsu.by',
            'image' => 'galina-rasolko-alekseevna.jpg',
        ],
        [
            'key' => 'ian-goodfellow',
            'firstName' => 'Иан',
            'lastName' => 'Гудфеллоу',
            'image' => 'ian-goodfellow.png',
            'website' => 'https://www.iangoodfellow.com',
        ],
        [
            'key' => 'jesse-shell',
            'firstName' => 'Джесси',
            'lastName' => 'Шелл',
            'image' => 'jesse-shell.jpg',
            'website' => 'https://www.jesseschell.com',
        ],
        [
            'key' => 'jon-duckett',
            'firstName' => 'Джон',
            'lastName' => 'Дакетт',
            'image' => 'jon-duckett.jpg',
        ],
        [
            'key' => 'bernard-perron',
            'firstName' => 'Бернар',
            'lastName' => 'Перрон',
            'image' => 'bernard-perron.jpg',
        ],
        [
            'key' => 'robert-martin-cecil',
            'firstName' => 'Роберт',
            'lastName' => 'Мартин',
            'middleName' => 'Сесил',
            'image' => 'robert-martin-cecil.jpg',
        ],
        [
            'key' => 'robert-lafore',
            'firstName' => 'Роберт',
            'lastName' => 'Лафор',
            'image' => 'robert-lafore.jpg',
        ],
        [
            'key' => 'scott-chacon',
            'firstName' => 'Скотт',
            'lastName' => 'Чакон',
            'image' => 'scott-chacon.jpg',
            'website' => 'http://scottchacon.com',
        ],
        [
            'key' => 'steve-mcconnell',
            'firstName' => 'Стив',
            'lastName' => 'МакКоннелл',
            'image' => 'steve-mcconnell.jpg',
            'website' => 'https://stevemcconnell.com',
        ],
        [
            'key' => 'william-vaughan',
            'firstName' => 'Уильям',
            'lastName' => 'Воган',
            'image' => 'william-vaughan.webp',
            'website' => 'http://pushingpoints.com',
        ],
        [
            'key' => 'yoshua-bengio',
            'firstName' => 'Йошуа',
            'lastName' => 'Бенджио',
            'image' => 'yoshua-bengio.jpg',
            'website' => 'https://yoshuabengio.org',
        ],
        [
            'key' => 'yurii-kremen-alexeevich',
            'firstName' => 'Юрий',
            'lastName' => 'Кремень',
            'middleName' => 'Алексеевич',
            'email' => 'kremenya@gmail.com',
            'image' => 'yurii-kremen-alexeevich.jpg',
        ],
        [
            'key' => 'william-king',
            'firstName' => 'Уильям',
            'lastName' => 'Кинг',
            'website' => 'http://www.williamking.me',
            'image' => 'william-king.jpg',
        ],
        [
            'key' => 'howard-phillips-lovecraft',
            'firstName' => 'Говард',
            'lastName' => 'Лавкрафт',
            'middleName' => 'Филлипс',
            'website' => 'https://www.hplovecraft.com',
            'image' => 'howard-phillips-lovecraft.jpg',
        ],
        [
            'key' => 'william-shakespeare',
            'firstName' => 'Уильям',
            'lastName' => 'Шекспир',
            'website' => 'https://www.shakespeare.org.uk',
            'image' => 'william-shakespeare.jpg',
        ],
        [
            'key' => 'lucewitch-alexander',
            'firstName' => 'Александр',
            'lastName' => 'Луцевич',
            'website' => 'https://www.iseu.bsu.by/ru/personalii/lucevich-aleksandr-aleksandrovich/',
            'image' => 'lucewitch-alexander.jpg',
        ],
        [
            'key' => 'pirutko-olga-nikolaevna',
            'firstName' => 'Ольга',
            'lastName' => 'Пирютко',
            'middleName' => 'Николаевна',
            'website' => 'http://mif.bspu.by/matherials/Mathem/Pirutko/',
            'image' => 'pirutko-olga-nikolaevna.jpg',
        ],
        [
            'key' => 'igor-simdyanov',
            'firstName' => 'Игорь',
            'lastName' => 'Симдянов',
            'website' => 'https://career.habr.com/igorsimdyanov',
            'image' => 'igor-simdyanov.jpg',
        ],
    ];

    public function __construct(
        private readonly string $dirPublic,
        private readonly string $dirBookAuthorUploads,
        private readonly string $dirFixtures,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        if (!file_exists(sprintf('%s%s', $this->dirPublic, $this->dirBookAuthorUploads))) {
            mkdir(sprintf('%s%s', $this->dirPublic, $this->dirBookAuthorUploads), 0777, true);
        }

        foreach (self::DATA as $author) {
            try {
                $entity = new Author();

                $entity->setFirstName($author['firstName']);
                $entity->setLastName($author['lastName']);

                if (array_key_exists('middleName', $author)) {
                    $entity->setMiddleName($author['middleName']);
                }

                if (array_key_exists('website', $author)) {
                    $entity->setWebsite($author['website']);
                }

                if (array_key_exists('email', $author)) {
                    $entity->setEmail($author['email']);
                }

                $entity->setIsDeleted(false);

                if (array_key_exists('image', $author)) {
                    $filename = FileUtils::joinPaths(
                        [
                            $this->dirBookAuthorUploads,
                            FileUtils::generateFilename('author', pathinfo($author['image'], \PATHINFO_EXTENSION))
                        ]
                    );

                    copy(
                        FileUtils::joinPaths([$this->dirFixtures, $this->dirBookAuthorUploads, $author['image']]),
                        FileUtils::joinPaths([$this->dirPublic, $filename])
                    );

                    $entity->setImagePath($filename);
                }

                $manager->persist($entity);

                if (array_key_exists('key', $author)) {
                    $this->setReference(sprintf('author: %s', $author['key']), $entity);
                }
            } catch (\Throwable $throwable) {
                throw new \Exception(sprintf('Failed for the author %s %s %s', $author['firstName'] ?? 'EMPTY', $author['lastName'] ?? 'EMPTY', $author['middleName'] ?? ''), $throwable->getCode(), previous: $throwable);
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['dev'];
    }
}
