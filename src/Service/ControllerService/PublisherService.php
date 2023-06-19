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
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class PublisherService extends AbstractCrudService implements CrudServiceInterface
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly string $dirPublic,
        private readonly string $dirBookPublisherUploads,
        PublisherRepository $repository,
        Security $security
    ) {
        $queryParser = new QueryParser();
        $queryParser->setColumns([
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
                'name' => 'address',
                'label' => 'Адрес',
                'type' => Column::NOT_FILTERABLE_TYPE,
                'isOrderable' => false,
                'isSearchable' => true,
            ]),
            new Column([
                'name' => 'website',
                'label' => 'Веб-страница',
                'type' => Column::NOT_FILTERABLE_TYPE,
                'isOrderable' => false,
                'isSearchable' => true,
            ]),
            new Column([
                'name' => 'email',
                'label' => 'Email',
                'type' => Column::NOT_FILTERABLE_TYPE,
                'isOrderable' => false,
                'isSearchable' => true,
            ]),
        ]);

        $this->setSecurity($security)->setQueryParser($queryParser)->setRepository($repository);
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

        if ($form['removeImage']->getData()) {
            $publisher->setImagePath(null);
        }

        $this->getRepository()->save($publisher, true);

        return $publisher->format();
    }
}
