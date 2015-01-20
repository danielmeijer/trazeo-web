<?php

namespace Trazeo\MyPageBundle\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\Controller\FOSRestController;
use Trazeo\BaseBundle\Entity\EChild;
use Trazeo\BaseBundle\Form\ChildType;
use Trazeo\BaseBundle\Form\UserType;
use Trazeo\MyPageBundle\Classes\Module\TrazeoGroups;
use Application\Sonata\UserBundle\Entity\User;
use Trazeo\MyPageBundle\Entity\Menu;
use Trazeo\MyPageBundle\Entity\Module;
use Trazeo\MyPageBundle\Entity\Page;
use Trazeo\MyPageBundle\Form\UserDirectType;

/**
* @Route("/front")
*/
class FrontController extends Controller
{
    /**
    * @Route("/landing/{subdomain}", name="landingPage")
    * @Template()
    */
   public function landingPageAction($subdomain)
   {
	   	$em = $this->getDoctrine()->getEntityManager();

       $repositoryPage = $em->getRepository("TrazeoMyPageBundle:Page");
       /** @var Page $page */
       $page = $repositoryPage->findOneBySubdomain($subdomain);

       //$this->isGranted("edit", $page);

       /** @var Menu $menu */
       foreach($page->getMenus() as $menu) {
           /** @var Module $module */
           foreach($menu->getModules() as $module) {
               if ($module->getClass()->getClassName() == "TrazeoRoutes") {
                   $groups = $module->getClass()->prepareFront($this, $module);
               }
           }
       }

	   	return array(
            'container' => $this,
            'groups' => $groups,
            'page' => $page
	   	);
	}

    /**
     * @Route("/inGroup/{group_id}", name="loginInGroup")
     * @Template()
     */
    public function loginInGroupAction($group_id) {
        $em = $this->getDoctrine()->getEntityManager();

        $repositoryGroup = $em->getRepository("TrazeoBaseBundle:EGroup");
        $group = $repositoryGroup->findOneById($group_id);

        $user = new User();
        $form_user = $this->createForm(new UserDirectType(), $user);

        $child = new EChild();
        $form_children = $this->createForm(new ChildType(), $child, array(
            'action' => $this->generateUrl('panel_child_create'),
            'method' => 'POST',
            'attr' => array(
                'Children.help.nick' => $this->get('translator')->trans('Children.help.nick'),
                'Children.help.datebirth' => $this->get('translator')->trans('Children.help.datebirth'),
                'Children.help.visibility' => $this->get('translator')->trans('Children.help.visibility'),
                'Children.help.gender' => $this->get('translator')->trans('Children.help.gender'),
                'Children.help.scholl' => $this->get('translator')->trans('Children.help.scholl'),
            )
        ));

        return array(
            'group' => $group,
            'form_user' => $form_user->createView(),
            'form_children' => $form_children->createView()

        );
    }

    /**
     * @Route("saveInGroup/{group_id}", name="saveInGroup")
     */
    public function saveInGroup() {
        $em = $this->getDoctrine()->getEntityManager();
    }
}