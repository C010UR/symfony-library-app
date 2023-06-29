<?php

namespace App\Service\ControllerService;

use App\Entity\Book;
use App\Form\BookFormType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Service\Abstract\AbstractCrudService;
use App\Service\Interface\CrudServiceInterface;
use App\Utils\Filter\Column;
use App\Utils\Filter\QueryParser;
use App\Utils\ImageSaver;
use App\Utils\RequestUtils;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;

class BookService extends AbstractCrudService implements CrudServiceInterface
{
    private readonly QueryParser $authorBooksQueryParser;

    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly string $dirPublic,
        private readonly string $dirBookCoverUploads,
        private readonly AuthorRepository $authorRepository,
        BookRepository $repository,
        Security $security
    ) {
        $columns = [
            new Column([
                'name' => 'id',
                'label' => '№',
                'type' => Column::NOT_FILTERABLE_TYPE,
                'isOrderable' => true,
                'isSearchable' => false,
            ]),
            new Column([
                'name' => 'name',
                'label' => 'Название',
                'type' => Column::STRING_TYPE,
                'isOrderable' => true,
                'isSearchable' => true,
            ]),
            new Column([
                'name' => 'publisher',
                'label' => 'Издательство',
                'type' => Column::ENTITY_TYPE,
                'entity' => 'publisher',
                'isOrderable' => true,
                'isSearchable' => false,
            ]),
            new Column([
                'name' => 'datePublished',
                'label' => 'Дата издания',
                'type' => Column::DATE_TYPE,
                'isOrderable' => true,
                'isSearchable' => true,
            ]),
            new Column([
                'name' => 'pageCount',
                'label' => 'Количество страниц',
                'type' => Column::INTEGER_TYPE,
                'isOrderable' => true,
                'isSearchable' => true,
            ]),
            new Column([
                'name' => 'tags',
                'label' => 'Жанры',
                'type' => Column::ENTITIES_TYPE,
                'isOrderable' => false,
                'isSearchable' => false,
                'entity' => 'tag',
            ]),
        ];

        $this->authorBooksQueryParser = new QueryParser();
        $this->authorBooksQueryParser->setColumns($columns);

        $columns[] =
            new Column([
                'name' => 'authors',
                'label' => 'Авторы',
                'type' => Column::ENTITIES_TYPE,
                'isOrderable' => false,
                'isSearchable' => false,
                'entity' => 'author',
            ]);

        $queryParser = new QueryParser();
        $queryParser->setColumns($columns);

        $this->setSecurity($security)->setQueryParser($queryParser)->setRepository($repository);
    }

    public function create(Request $request): array
    {
        $book = new Book();
        $form = $this->formFactory->create(BookFormType::class, $book);
        RequestUtils::submitForm($request, $form, true);

        if ($image = $form['image']->getData()) {
            $filename = (new ImageSaver())->save(
                $image,
                $this->dirPublic,
                $this->dirBookCoverUploads,
                'cover',
            );

            $book->setImagePath($filename);
        }

        $this->getRepository()->save($book, true);

        return $book->format();
    }

    public function update(Request $request, int $id): array
    {
        /** @var Book $book */
        $book = $this->find($id);
        $form = $this->formFactory->create(BookFormType::class, $book, [
            'method' => 'PATCH',
        ]);

        RequestUtils::submitForm($request, $form, false);

        if ($image = $form['image']->getData()) {
            $filename = (new ImageSaver())->save(
                $image,
                $this->dirPublic,
                $this->dirBookCoverUploads,
                'cover',
            );

            $book->setImagePath($filename);
        }

        if ($form['removeImage']->getData()) {
            $book->setImagePath(null);
        }

        $this->getRepository()->save($book, true);

        return $book->format();
    }

    public function getAuthorBooks(Request $request, string $slug): array
    {
        /** @var Author $author */
        $author = $this->findWithRepository($this->authorRepository, ['slug' => $slug]);

        $query = HeaderUtils::parseQuery($request->getQueryString() ?? '');

        return $this->getRepository()->findMatchingByAuthorAndDeleted(
            $this->isAdmin(),
            $author,
            $this->authorBooksQueryParser->parseQuery($query, true, true),
        );
    }

    public function getAuthorBooksFilterMeta(): array
    {
        return $this->authorBooksQueryParser->getColumns();
    }
}
