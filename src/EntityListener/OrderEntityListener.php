<?php

namespace App\EntityListener;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsEntityListener(event: Events::prePersist, entity: Order::class)]
class OrderEntityListener
{
    public function __construct(
        private readonly SluggerInterface $slugger,
        private readonly string $dirPublic
    ) {
    }

    public function prePersist(Order $order, LifecycleEventArgs $event): void
    {
        $order->normalizeName();
        $order->setDateCreated();
    }
}
