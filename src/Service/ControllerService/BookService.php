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
        private FormFactoryInterface $formFactory,
        private string $dirPublic,
        private string $dirBookCoverUploads,
        private BookRepository $repository
    ) {
        $queryParser = new QueryParser();
        $queryParser->setAllowedColumns([
            new Column('id', 'ID', Column::NOT_FILTERABLE_TYPE, true),
            new Column('name', 'Название', Column::STRING_TYPE, true),
            new Column('publisher', 'Издательство', Column::ENTITY_TYPE, true, data: [
                'entity' => 'publisher'
            ]),
            new Column('pageCount', 'Количество страниц', Column::INTEGER_TYPE, true),
            new Column('tags', 'Жанры', Column::ENTITIES_TYPE, true, data: [
                'entity' => 'tag'
            ]),
            new Column('authors', 'Авторы', Column::ENTITIES_TYPE, true, data: [
                'entity' => 'author'
            ])
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
            'method' => 'PATCH'
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
