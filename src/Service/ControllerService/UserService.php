<?php

namespace App\Service\ControllerService;

use App\Entity\User;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use App\Service\Abstract\AbstractCrudService;
use App\Service\Interface\CrudServiceInterface;
use App\Utils\Filter\Column;
use App\Utils\Filter\QueryParser;
use App\Utils\ImageSaver;
use App\Utils\RequestUtils;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class UserService extends AbstractCrudService implements CrudServiceInterface
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private string $dirPublic,
        private string $dirUserUploads,
        private UserRepository $repository
    ) {
        $queryParser = new QueryParser();
        $queryParser->setAllowedColumns([
            new Column('id', 'ID', Column::NOT_FILTERABLE_TYPE, true),
            new Column('firstName', 'Имя', Column::STRING_TYPE, true),
            new Column('lastName', 'Фамилия', Column::STRING_TYPE, true),
            new Column('middleName', 'Отчество', Column::STRING_TYPE, true),
            new Column('email', 'Email', Column::STRING_TYPE, true),
            new Column('roles', 'Роли', Column::ARRAY_TYPE, true),
            new Column('email', 'Email', Column::STRING_TYPE, true),
            new Column('isActive', 'Активен ли', Column::BOOLEAN_TYPE, true),
        ]);

        $this->setQueryParser($queryParser)->setRepository($repository);
    }

    public function create(Request $request): array
    {
        $user = new User();
        $form = $this->formFactory->create(UserFormType::class, $user);
        RequestUtils::submitForm($request, $form, true);

        if ($image = $form['image']->getData()) {
            $filename = (new ImageSaver())->save(
                $image,
                $this->dirPublic,
                $this->dirUserUploads,
                'user',
            );

            $user->setImagePath($filename);
        }

        $this->getRepository()->save($user, true);

        return $user->format();
    }

    public function update(Request $request, int $id): array
    {
        /** @var User $user */
        $user = $this->find($id);
        $form = $this->formFactory->create(UserFormType::class, $user, [
            'method' => 'PATCH'
        ]);

        RequestUtils::submitForm($request, $form, false);


        if ($image = $form['image']->getData()) {
            $filename = (new ImageSaver())->save(
                $image,
                $this->dirPublic,
                $this->dirUserUploads,
                'user',
            );

            $user->setImagePath($filename);
        }

        $user->setIsActive(false);
        $this->getRepository()->save($user, true);

        return $user->format();
    }
}
