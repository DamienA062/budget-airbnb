<?php

namespace App\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ImageUrlCatch implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            'prePersist'
        ];
    }

    public function PrePersist(LifecycleEventArgs $args)
    {
        dump($args->getEntity());
    }
}