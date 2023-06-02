<?php

namespace App\Service\ControllerService;

use App\Entity\Publisher;
use App\Form\PublisherFormType;
use App\Repository\PublisherRepository;
use App\Service\Abstract\AbstractCrudService;
use App\Service\Interface\CrudServiceInterface;
use App\Utils\Filter\Column;
use App\Utils\Filter\QueryParser;
use App\Utils\ImageSaver;
use App\Utils\RequestUtils;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class PublisherService extends AbstractCrudService implements CrudServiceInterface
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private string $dirPublic,
        private string $dirBookPublisherUploads,
        private PublisherRepository $repository
    ) {
        $queryParser = new QueryParser();
        $queryParser->setAllowedColumns([
            new Column('id', 'ID', Column::NOT_FILTERABLE_TYPE, true),
            new Column('name', 'Название', Column::STRING_TYPE, true),
        ]);

        $this->setQueryParser($queryParser)->setRepository($repository);
    }

    public function create(Request $request): array
    {
        $publisher = new Publisher();
        $form = $this->formFactory->create(PublisherFormType::class, $publisher);
        RequestUtils::submitForm($request, $form, true);

        if ($image = $form['image']->getData()) {
            $filename = (new ImageSaver())->save(
                $image,
                $this->dirPublic,
                $this->dirBookPublisherUploads,
                'publisher',
            );

            $publisher->setImagePath($filename);
        }

        $this->getRepository()->save($publisher, true);

        return $publisher->format();
    }

    public function update(Request $request, int $id): array
    {
        /** @var Publisher $publisher */
        $publisher = $this->find($id);
        $form = $this->formFactory->create(PublisherFormType::class, $publisher, [
            'method' => 'PATCH',
        ]);

        RequestUtils::submitForm($request, $form, false);

        if ($image = $form['image']->getData()) {
            $filename = (new ImageSaver())->save(
                $image,
                $this->dirPublic,
                $this->dirBookPublisherUploads,
                'publisher',
            );

            $publisher->setImagePath($filename);
        }

        $this->getRepository()->save($publisher, true);

        return $publisher->format();
    }
}
