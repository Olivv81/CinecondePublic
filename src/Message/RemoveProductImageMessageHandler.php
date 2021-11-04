<?php

namespace App\MessageHandler;

use App\Message\RemoveProductImageMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RemoveProductImageMessageHandler implements MessageHandlerInterface
{
    public function __invoke(RemoveProductImageMessage $message): void
    {
        $filename = $message->getFilename();

        // delete your file according to your mapping configuration
    }
}
