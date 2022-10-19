<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Article;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DetectView
{
    private ServiceMail $articleMail;

    public function __construct(ServiceMail $articleMail)
    {
        $this->articleMail = $articleMail;
    }

    public function PostUpdate(LifecycleEventArgs $args): void
    {
        // La méthode doit porter le nom de l'évènement déclaré dans services.yaml
        // Noter que le paramètre LifecycleEventArgs permet également d'accéder à l'EntityManager

        $entity = $args->getObject();

        if(!$entity instanceof Article) {
            // Ne rien faire s'il ne s'agit pas d'une entité Vehicle => ne pas oublier ce test !
            return;
        }

        if($entity->getNbViews() % 10 == 0)
        {
            // Faire appel au service Mailer qui enverra l'alerte
            return;
        }
        $this->articleMail->sendEmail($entity);
    }
}