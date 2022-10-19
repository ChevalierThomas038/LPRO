<?php

namespace App\Service;

use App\Entity\Article;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ServiceMail
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(Article $article)
    {
        $email = (new Email())
            ->subject("L'article : {$article->getTitle()}")
            ->text("Il a été vu {$article->getNbViews()}.")
            ->addTo('admin@monsite.com') // TODO : passer par une variable !
            ->addFrom('no-reply@monsite.com'); // TODO : passer par une variable !

        $this->mailer->send($email);
    }
}