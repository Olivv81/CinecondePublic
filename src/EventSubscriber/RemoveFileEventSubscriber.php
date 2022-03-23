<?php

namespace App\EventSubscriber;

use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;
use App\Message\RemoveProductImageMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RemoveFileEventSubscriber implements EventSubscriberInterface


{

    private $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::PRE_REMOVE => ['onPreRemove'],
        ];
    }

    public function onPreRemove(Event $event): void
    {
        $mapping = $event->getMapping();
        $mappingName = $mapping->getMappingName();

        if ('product_image' === $mappingName) {
            $this->dispatch(RemoveProductImageMessage::class, $event);
        }
    }

    private function dispatch(string $messageClass, Event $event): void
    {
        $event->cancel();

        $object = $event->getObject();

        $mapping = $event->getMapping();
        $filename = $mapping->getFileName($object);

        $message = new $messageClass($filename);
        $this->messageBus->dispatch($message);
    }
}
