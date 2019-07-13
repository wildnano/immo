<?php

namespace App\Listener;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;


class ImageCacheSubscriber implements EventSubscriber{

    /**
     * ImageCacheSubscriber constructor.
     * @param CacheManager $cacheManager
     * @param UploaderHelper $helper
     */
    public function __construct(CacheManager $cacheManager, UploaderHelper $uploadHelper)
    {
        $this->cacheManager = $cacheManager;
        $this->uploadHelper = $uploadHelper;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents()
    {
        return [
            'preRemove',
            'preUpdate'
        ];
    }

    public function preRemove(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if(!$entity instanceof Property) {
            return;
        }
        $this->cacheManager->remove($this->uploadHelper->asset($entity, 'imageFile'));

    }

    public function preUpdate(PreUpdateEventArgs $args) {
//        dump($args->getEntity());
//        dump($args->getObject());
        $entity = $args->getEntity();
        if(!$entity instanceof Property) {
             return;
        }
        if($entity->getImageFile() instanceof UploadedFile) {
           $this->cacheManager->remove($this->uploadHelper->asset($entity, 'imageFile'));
        }

    }
}