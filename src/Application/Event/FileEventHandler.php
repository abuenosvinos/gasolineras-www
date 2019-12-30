<?php

namespace App\Application\Event;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Symfony\Component\Mime\Email;

class FileEventHandler implements MessageSubscriberInterface
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function fileDownloaded(FileWasDownloaded $event)
    {
        $email = (new Email())
            ->from('hola@antoniobuenosvinos.com')
            ->to('antonio.buenosvinos@gmail.com')
            ->subject('fileDownloaded')
            ->html('<p>fileDownloaded: '.$event->name().'</p>');

        $this->mailer->send($email);
    }

    public function fileProcessed(FileWasProcessed $event)
    {
        $email = (new Email())
            ->from('hola@antoniobuenosvinos.com')
            ->to('antonio.buenosvinos@gmail.com')
            ->subject('fileProcessed')
            ->html('<p>fileProcessed: '.$event->name().'</p>');

        $this->mailer->send($email);
    }

    public static function getHandledMessages(): iterable
    {
        yield FileWasDownloaded::class => [
            'method' => 'fileDownloaded',
        ];
        yield FileWasProcessed::class => [
            'method' => 'fileProcessed',
        ];
    }

}
