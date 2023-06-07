<?php

namespace App\Service\ControllerService;

use App\Entity\Book;
use App\Form\BookFormType;
use App\Repository\BookRepository;
use App\Service\Abstract\AbstractCrudService;
use App\Service\Interface\CrudServiceInterface;
use App\Utils\Filter\Column;
use App\Utils\Filter\QueryParser;
use App\Utils\ImageSaver;
use App\Utils\RequestUtils;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class BookService extends AbstractCrudService implements CrudServiceInterface
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly string $dirPublic,
        private readonly string $dirBookCoverUploads,
        BookRepository $repository
    ) {
        $queryParser = new QueryParser();
        $queryParser->setColumns([
            new Column([
                'name' => 'id',
                'label' => 'Дата добавления',
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
            new Column([
                'name' => 'authors',
                'label' => 'Авторы',
                'type' => Column::ENTITIES_TYPE,
                'isOrderable' => false,
                'isSearchable' => false,
                'entity' => 'author',
            ]),
        ]);

        $this->setQueryParser($queryParser)->setRepository($repository);
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

        $this->getRepository()->save($book, true);

        return $book->format();
    }
}
