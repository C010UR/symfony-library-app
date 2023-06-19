<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Publisher;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BookFixturesTest extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    /**
     * @var array<string, string[]|string|int>[]
     */
    final public const DATA = [
        [
            'name' => 'test-1',
            'datePublished' => '2016-05-01',
            'pageCount' => 288,
            'publisher' => 'test-1',
            'description' => <<<EOF
                Принято считать, что программирование – это очень сложно. Особенно если раз за разом наступать на одни и те же грабли, пытаться сделать по-своему то, что уже и так было придумано до нас. Ведь практически для любой задачи есть готовый алгоритм решения, осталось только найти его и правильно использовать.

                В книге «Грокаем алгоритмы» Адитья Бхаргава не просто показывает примеры таких решений с детальными иллюстрациями, но и учит читателя самостоятельно находить их в дальнейшем. Читатель знакомится с понятиями бинарного поиска, массивами, связанными списками, структурами данных, рекурсией.

                Книга рассчитана на тех, кто уже знаком с основными азами программирования и интересуется алгоритмическими решениями. Автор старается доносить информацию понятным даже новичку языком, иллюстрирует все основные моменты.
                EOF,
            'tags' => ['test-1', 'test-2'],
            'authors' => ['test-1'],
        ],
        [
            'name' => 'Глубокое обучение',
            'datePublished' => '2015-11-03',
            'pageCount' => 652,
            'publisher' => 'test-2',
            'description' => <<<EOF
                Глубокое обучение – это вид машинного обучения, наделяющий компьютеры способностью учиться на опыте и понимать мир в терминах иерархии концепций. Поскольку компьютер приобретает знания из опыта, отпадает нужда в человеке-операторе, который формально описывает необходимые компьютеру знания. Иерархическая организация позволяет компьютеру обучаться сложным концепциям, конструируя их из более простых; граф такой иерархии может содержать много уровней. В этой книге читатель найдет широкий обзор тем, изучаемых в глубоком обучении.

                Книга содержит математические и концептуальные основы линейной алгебры, теории вероятностей и теории информации, численных расчетов и машинного обучения в том объеме, который необходим для понимания материала. Описываются приемы глубокого обучения, применяемые на практике, в том числе глубокие сети прямого распространения, регуляризация, алгоритмы оптимизации, сверточные сети, моделирование последовательностей, и др. Рассматриваются такие приложения, как обработка естественных языков, распознавание речи, компьютерное зрение, онлайновые рекомендательные системы, биоинформатика и видеоигры. Наконец, описываются перспективные направления исследований: линейные факторные модели, автокодировщики, обучение представлений, структурные вероятностные модели, методы Монте-Карло, статистическая сумма, приближенный вывод и глубокие порождающие модели.

                Издание будет полезно студентами и аспирантам, а также опытным программистам, которые хотели бы применить глубокое обучение в составе своих продуктов или платформ.
                EOF,
            'tags' => ['test-1'],
            'authors' => ['test-1', 'test-2'],
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::DATA as $book) {
            try {
                $entity = new Book();

                $entity->setName($book['name']);

                $entity->setDatePublished(\DateTime::createFromFormat('Y-m-d', $book['datePublished']));
                $entity->setPageCount($book['pageCount']);

                $entity->setPublisher(
                    $this->getReference(sprintf('publisher: %s', $book['publisher']), Publisher::class)
                );

                if (array_key_exists('description', $book)) {
                    $entity->setDescription($book['description']);
                }

                $entity->setIsDeleted(false);

                foreach ($book['tags'] as $tag) {
                    $entity->addTag($this->getReference(sprintf('tag: %s', $tag), Tag::class));
                }

                foreach ($book['authors'] as $author) {
                    $entity->addAuthor($this->getReference(sprintf('author: %s', $author), Author::class));
                }

                $manager->persist($entity);

                if (array_key_exists('key', $book)) {
                    $this->setReference(sprintf('book: %s', $book['key']), $entity);
                }
            } catch (\Throwable $throwable) {
                throw new \Exception(sprintf('Failed for the book %s', $book['name'] ?? 'EMPTY'), $throwable->getCode(), previous: $throwable);
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['test'];
    }

    public function getDependencies()
    {
        return [
            AuthorFixturesTest::class,
            PublisherFixturesTest::class,
            TagFixturesTest::class,
        ];
    }
}
