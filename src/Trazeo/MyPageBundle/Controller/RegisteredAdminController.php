<?php

namespace Trazeo\MyPageBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sonata\AdminBundle\Admin\AdminInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Trazeo\BaseBundle\Entity\EChild;
use Trazeo\MyPageBundle\Form\RegisteredAdminType;

/**
 * @Route("/admin/registered")
 */
class RegisteredAdminController extends Controller
{
    /**
     * @Route("/evolution/", name="registered_evolution")
     * @Template()
     */
    public function evolutionAction()
    {
        // http://blog.eike.se/2014/03/custom-page-controller-in-sonata-admin.html
        $admin_pool = $this->get('sonata.admin.pool');

        $child = new EChild();
        $form_filters = $this->createForm(new RegisteredAdminType($this));

        return array(
            'admin_pool' => $admin_pool,
            'form_filters' => $form_filters->createView()
        );
    }
}