<?php

namespace App\DataFixtures;

use App\Entity\Publisher;
use App\Utils\FileUtils;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class PublisherFixturesDev extends Fixture implements FixtureGroupInterface
{
    final public const DATA = [
        [
            'key' => 'eksmo',
            'name' => 'Издательская Группа «ЭКСМО»',
            'address' => 'Российская Федерация, 123308, г. Москва, Зорге ул., д.1, стр.1',
            'email' => 'info@eksmo.ru',
            'website' => 'https://eksmo.ru',
            'image' => 'eksmo.png',
        ],
        [
            'key' => 'ast',
            'name' => 'Издательство «АСТ»',
            'address' => 'Российская Федерация, г. Москва, Пресненская наб., д.6, стр.2, БЦ «Империя»',
            'email' => 'support@ast.ru',
            'website' => 'https://ast.ru',
            'image' => 'act.png',
        ],
        [
            'key' => 'azbooka',
            'name' => 'Издательская Группа «Азбука-Аттикус»',
            'address' => 'Российская Федерация, 123308, Москва, Зорге ул., 1 стр.1',
            'email' => 'info@atticus-group.ru',
            'website' => 'https://azbooka.ru',
            'image' => 'azbooka.png',
        ],
        [
            'key' => 'ripol',
            'name' => 'Издательство «РИПОЛ классик»',
            'address' => 'Российская Федерация, 109052, г. Москва, Нижегородская ул., 29-33',
            'email' => 'info@ripol.ru',
            'website' => 'https://ripol.ru',
            'image' => 'ripol.png',
        ],
        [
            'key' => 'prospect',
            'name' => 'Издательство «Проспект классик»',
            'address' => 'Российская Федерация, 119285, г. Москва, Мосфильмовская ул., д.1',
            'email' => 'mail@prospekt.org',
            'website' => 'https://prospekt.org',
            'image' => 'prospect.png',
        ],
        [
            'key' => 'piter',
            'name' => 'Издательский дом «Питер»',
            'address' => 'Российская Федерация, 194044, г. Санкт-Петербург, Большой Сампсониевский пр-кт, дом № 29, литера А',
            'email' => 'gorbach@piter.com',
            'website' => 'https://www.piter.com',
            'image' => 'piter.jpg',
        ],
        [
            'key' => 'dmk',
            'name' => 'Издательство «ДМК Пресс»',
            'address' => 'Российская Федерация, 107113, г. Москва, Старослободская ул., д. 14, кв. 17',
            'email' => 'dmkpress.help@gmail.com',
            'website' => 'https://dmkpress.com',
            'image' => 'dmk.png',
        ],
        [
            'key' => 'bombora',
            'name' => 'Издательство «Бомбора»',
            'address' => 'Российская Федерация, 123308, Москва, Зорге ул, д. 1 ст. 1',
            'email' => 'bombora@eksmo.ru',
            'website' => 'https://bombora.ru',
            'image' => 'bombora.jpg',
        ],
        [
            'key' => 'visheishaya-shkola',
            'name' => 'Издательство Вышэйшая школа',
            'address' => 'Республика Беларусь, г. Минск, 220004, Победителей пр-кт, д. 11',
            'email' => 'market@vshph.com',
            'website' => 'https://vshph.com',
            'image' => 'visheishaya-shkola.gif',
        ],
        [
            'key' => 'russian-editorial-board',
            'name' => 'Издательство «Русская Редакция»',
            'address' => 'Российская Федерация, г. Москва, Полесский проезд, д. 16 к. 1',
            'email' => 'info@rusedit.com',
            'website' => 'https://www.rusedit.com',
        ],
        [
            'key' => 'alpina',
            'name' => 'Издательская группа «Альпина»',
            'address' => 'Российская Федерация, 123007, Москва, 4-я Магистральная ул., д. 5, ст. 1',
            'email' => 'shop@alpinabook.ru ',
            'website' => 'https://alpinabook.ru',
            'image' => 'alpina.png',
        ],
        [
            'key' => 'black-library',
            'name' => 'Black library',
            'address' => 'United Kingdom, Black Library Customer Services, Willow Road, Lenton, Nottingham, NG7 2WS',
            'email' => 'contact@blacklibrary.com',
            'website' => 'https://www.blacklibrary.com',
            'image' => 'black-library.jpg',
        ],
        [
            'key' => 'aiv',
            'name' => 'Издательство «Адукацыя і выхаванне»',
            'address' => 'Республика Беларусь, 220070, г. Минск, Будённого ул., д. 21',
            'email' => 'sale@aiv.by',
            'website' => 'https://aiv.by',
            'image' => 'aiv.png',
        ],
        [
            'key' => 'narodnaya-asveta',
            'name' => 'Издательство «Народная асвета»',
            'address' => 'Республика Беларусь, г. Минск, Независимости пр-кт, д. 77, пом. 33',
            'email' => 'info@n-asveta.by',
            'website' => 'https://www.n-asveta.by/',
            'image' => 'narodnaya-asveta.png',
        ],
        [
            'key' => 'bhv-peterburg',
            'name' => 'Издательство «БХВ-Петербург»',
            'address' => 'Российская Федерация, 191036, г. Санкт-Петербург, Гончарная ул., д. 20, пом. 7Н',
            'email' => 'mail@bhvm.ru',
            'website' => 'https://bhv.ru/',
            'image' => 'bhv.jpg',
        ],
    ];

    public function __construct(
        private readonly string $dirPublic,
        private readonly string $dirBookPublisherUploads,
        private readonly string $dirFixtures,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        if (!file_exists(sprintf('%s%s', $this->dirPublic, $this->dirBookPublisherUploads))) {
            mkdir(sprintf('%s%s', $this->dirPublic, $this->dirBookPublisherUploads), 0777, true);
        }

        foreach (self::DATA as $publisher) {
            try {
                $entity = new Publisher();

                $entity->setName($publisher['name']);
                $entity->setAddress($publisher['address']);
                $entity->setEmail($publisher['email']);
                $entity->setWebsite($publisher['website']);

                $entity->setIsDeleted(false);

                if (array_key_exists('image', $publisher)) {
                    $filename = FileUtils::joinPaths(
                        [
                            $this->dirBookPublisherUploads,
                            FileUtils::generateFilename('publisher', pathinfo($publisher['image'], \PATHINFO_EXTENSION))
                        ]
                    );

                    copy(
                        FileUtils::joinPaths([$this->dirFixtures, $this->dirBookPublisherUploads, $publisher['image']]),
                        FileUtils::joinPaths([$this->dirPublic, $filename])
                    );

                    $entity->setImagePath($filename);
                }

                $manager->persist($entity);

                if (array_key_exists('key', $publisher)) {
                    $this->setReference(sprintf('publisher: %s', $publisher['key']), $entity);
                }
            } catch (\Throwable $throwable) {
                throw new \Exception(sprintf('Failed for the publisher %s', $publisher['name'] ?? 'EMPTY'), $throwable->getCode(), previous: $throwable);
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['dev'];
    }
}
