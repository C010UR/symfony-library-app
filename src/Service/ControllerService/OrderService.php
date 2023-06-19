<?php

namespace App\Service\ControllerService;

use App\Entity\Order;
use App\Form\OrderFormType;
use App\Repository\OrderRepository;
use App\Service\Abstract\AbstractCrudService;
use App\Utils\Filter\Column;
use App\Utils\Filter\QueryParser;
use App\Utils\RequestUtils;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class OrderService extends AbstractCrudService
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        OrderRepository $repository,
        Security $security
    ) {
        $queryParser = new QueryParser();
        $queryParser->setColumns([
            new Column([
                'name' => 'id',
                'label' => '№',
                'type' => Column::NOT_FILTERABLE_TYPE,
                'isOrderable' => true,
                'isFilterable' => false,
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
                'isOrderable' => true,
                'isSearchable' => true,
            ]),
            new Column([
                'name' => 'phoneNumber',
                'label' => 'Номер телефона',
                'type' => Column::STRING_TYPE,
                'isOrderable' => false,
                'isSearchable' => true,
            ]),
            new Column([
                'name' => 'dateCreated',
                'label' => 'Дата создания',
                'type' => Column::DATE_TYPE,
                'isOrderable' => true,
                'isSearchable' => true,
            ]),
            new Column([
                'name' => 'userCompleted',
                'label' => 'Пользователь, завершенный заказ',
                'type' => Column::ENTITY_TYPE,
                'isOrderable' => true,
                'isSearchable' => true,
                'entity' => 'user',
            ]),
            new Column([
                'name' => 'dateCompleted',
                'label' => 'Дата завершения заказа',
                'type' => Column::DATE_TYPE,
                'isOrderable' => true,
                'isSearchable' => true,
            ]),
            new Column([
                'name' => 'book',
                'label' => 'Книга',
                'type' => Column::ENTITY_TYPE,
                'isOrderable' => true,
                'isSearchable' => true,
                'entity' => 'book',
            ]),
        ]);

        $this->setSecurity($security)->setQueryParser($queryParser)->setRepository($repository);
    }

    public function create(Request $request): array
    {
        $order = new Order();
        $form = $this->formFactory->create(OrderFormType::class, $order);
        RequestUtils::submitForm($request, $form, true);

        $this->getRepository()->save($order, true);

        return $order->format();
    }

    public function complete(int $id): array
    {
        $order = $this->find($id);

        if ($order->getUserCompleted() instanceof \App\Entity\User) {
            if ($order->getUserCompleted()->getUserIdentifier() !== $this->getSecurity()->getUser()->getUserIdentifier()) {
                throw new AccessDeniedException('Cannot unset completion of order by another user.');
            }

            $order->setUserCompleted(null);
        } else {
            $order->setUserCompleted($this->getSecurity()->getUser());
        }

        $this->getRepository()->save($order, true);

        return $order->format();
    }
}
