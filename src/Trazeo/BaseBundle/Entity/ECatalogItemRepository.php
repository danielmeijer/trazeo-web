<?php
namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

class ECatalogItemRepository extends EntityRepository
{

    /**
     * @param UserExtend $user
     * @param $ofert_id
     * @return \Swift_Message $message email to send
     * @throws NotFoundHttpException
     * @throws PreconditionFailedHttpException
     */
    public function exchangeCatalogItem(UserExtend $user, $ofert_id)
    {
        $ofert=$this->find($ofert_id);
        if($ofert==null)throw new NotFoundHttpException('Catalog item not found');
        if(($user->getPoints()-$user->getSpendedPoints())<$ofert->getPoints())throw new PreconditionFailedHttpException("Don't have enougth points");
        //Marcamos los puntos como gastados
        $user->setSpendedPoints($user->getSpendedPoints()+$ofert->getPoints());
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        //Creamos el email a enviar
        $message = \Swift_Message::newInstance()
            ->setFrom(array("hola@trazeo.es" => "Trazeo"))
            ->setTo("hola@trazeo.es")
            ->setSubject('Solicitud de canjeo de usuario')
            ->setBody('<p>Solicitud de canjeo del usuario '.$user->getNick().' para la oferta '.$ofert->getTitle().' de la empresa '.$ofert->getCompany(). '</p>', 'text/html');
        return $message;
    }
}