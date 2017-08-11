<?php

namespace Trazeo\BaseBundle\Listener;

use Symfony\Component\Routing\Router;

use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\RouteRedirectView;



class ExceptionListener
{

    public function __construct($container, $session) {
        $this->container = $container;
        $this->session = $session;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        /*if($event->getException() instanceof PreconditionFailedHttpException){

            if($event->getRequest()->getMethod()=='GET'){
                $url=$_SERVER['HTTP_REFERER'];
                $this->container->get('sopinet_flashMessages')->addFlashMessages("warning",$event->getException()->getMessage());
                $event->setResponse(new RedirectResponse($url));
            }
            else{
                $response=json_encode(array(
                    'msg' => $event->getException()->getMessage(),
                    'status'=> -1
                ));
                $event->setResponse(new Response($response, 200, array(
                    'Content-Type' => 'application/json'
                )));
            }
        }*/

    }
}

?>
