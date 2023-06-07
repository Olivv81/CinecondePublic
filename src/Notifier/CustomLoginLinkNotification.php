<?php

namespace App\Notifier;

use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;


class CustomLoginLinkNotification extends LoginLinkNotification
{
    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null): ?EmailMessage
    {
        $emailMessage = parent::asEmailMessage($recipient, $transport);

        // get the NotificationEmail object and override the template
        $email = $emailMessage->getMessage();
        $email->htmlTemplate('login/custom_login_link_email.html.twig');

        return $emailMessage;
    }
}
