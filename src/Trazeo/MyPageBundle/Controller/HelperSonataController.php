<?php

namespace Trazeo\MyPageBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Swift_Message as Message;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\CoreBundle\FlashMessage\FlashManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin/helper")
 */
class HelperSonataController extends Controller
{
    /**
     * @Route("/sendEmail/", name="admin_helper_sendEmail")
     */
    public function sendEmailAction(Request $request)
    {
        $data = $request->get('SendEmail');
        $emails_string = $request->get('emails');
        $emails = explode(",", $emails_string);

        //$dispatcher = $this->get('hip_mandrill.dispatcher');
        $dispatcher = $this->get('swiftmailer.mailer');
        $message = new Message();
        $message
            ->setFrom('hola@trazeo.es', 'Trazeo');

        foreach($emails as $email) {
            $message->addTo($email);
        }

        $message
            ->setBcc('hola@trazeo.es')
            ->setSubject($data['subject'])
            ->setHtml($data['body']);

        $result = $dispatcher->send($message);

        $this->getRequest()->getSession()->getFlashBag()->add("success", "Su email se ha mandado con éxito");

        return new RedirectResponse($this->generateUrl('sonata_admin_dashboard'));
    }
}