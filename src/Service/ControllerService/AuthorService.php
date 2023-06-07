<?php

namespace App\Service\ControllerService;

use App\Entity\Author;
use App\Form\AuthorFormType;
use App\Repository\AuthorRepository;
use App\Service\Abstract\AbstractCrudService;
use App\Service\Interface\CrudServiceInterface;
use App\Utils\Filter\Column;
use App\Utils\Filter\QueryParser;
use App\Utils\ImageSaver;
use App\Utils\RequestUtils;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class AuthorService extends AbstractCrudService implements CrudServiceInterface
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private string $dirPublic,
        private string $dirBookAuthorUploads,
        private AuthorRepository $repository
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
                'name' => 'firstName',
                'label' => 'Имя',
                'type' => Column::STRING_TYPE,
                'isOrderable' => true,
                'isSearchable' => true,
            ]),
            new Column([
                'name' => 'lastName',
                'label' => 'Фамилия',
                'type' => Column::STRING_TYPE,
                'isOrderable' => true,
                'isSearchable' => true,
            ]),
            new Column([
                'name' => 'middleName',
                'label' => 'Отчество',
                'type' => Column::STRING_TYPE,
                'isNullable' => true,
                'isOrderable' => true,
                'isSearchable' => true,
            ]),
            new Column([
                'name' => 'books',
                'label' => 'Книги',
                'type' => Column::ENTITIES_TYPE,
                'isOrderable' => false,
                'isSearchable' => false,
                'entity' => 'book',
            ]),
        ]);

        $this->setQueryParser($queryParser)->setRepository($repository);
    }

    public function create(Request $request): array
    {
        $author = new Author();
        $form = $this->formFactory->create(AuthorFormType::class, $author);
        RequestUtils::submitForm($request, $form, true);

        if ($image = $form['image']->getData()) {
            $filename = (new ImageSaver())->save(
                $image,
                $this->dirPublic,
                $this->dirBookAuthorUploads,
                'author',
            );

            $author->setImagePath($filename);
        }

        $this->getRepository()->save($author, true);

        return $author->format();
    }

    public function update(Request $request, int $id): array
    {
        /** @var Author $author */
        $author = $this->find($id);
        $form = $this->formFactory->create(AuthorFormType::class, $author, [
            'method' => 'PATCH',
        ]);

        RequestUtils::submitForm($request, $form, false);

        if ($image = $form['image']->getData()) {
            $filename = (new ImageSaver())->save(
                $image,
                $this->dirPublic,
                $this->dirBookAuthorUploads,
                'author',
            );

            $author->setImagePath($filename);
        }

        $this->getRepository()->save($author, true);

        return $author->format();
    }
}
