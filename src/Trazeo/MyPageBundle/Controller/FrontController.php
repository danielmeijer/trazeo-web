<?php

namespace Trazeo\MyPageBundle\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\Controller\FOSRestController;
use Trazeo\BaseBundle\Entity\EChild;
use Trazeo\BaseBundle\Entity\EGroup;
use Trazeo\BaseBundle\Entity\EGroupRepository;
use Trazeo\BaseBundle\Entity\UserExtend;
use Trazeo\MyPageBundle\Form\ChildOptionalType;
use Trazeo\MyPageBundle\Form\UserExtendCustomType;
use Trazeo\BaseBundle\Form\UserExtendType;
use Trazeo\BaseBundle\Form\UserType;
use Trazeo\MyPageBundle\Classes\Module\TrazeoGroups;
use Application\Sonata\UserBundle\Entity\User;
use Trazeo\MyPageBundle\Entity\Menu;
use Trazeo\MyPageBundle\Entity\Module;
use Trazeo\MyPageBundle\Entity\Page;
use Trazeo\MyPageBundle\Form\UserDirectType;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
* @Route("/front")
*/
class FrontController extends Controller
{
    /**
     * Hace Login al usuario pasado por parámetro
     *
     * @param User $user
     */
    private function doLogin(User $user) {
        // Hacemos LOGIN
        $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
        $this->get("security.context")->setToken($token); //now the user is logged in

        //now dispatch the login event
        $request = $this->get("request");
        $event = new InteractiveLoginEvent($request, $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
    }

    /**
     * Accede a un Grupo
     *
     * @param Group $group
     */
    private function doJoin(EGroup $group) {
        if ($group->getVisibility() == EGroup::VISIBILITY_PUBLIC) {
            return $this->forward("TrazeoFrontBundle:PanelGroups:joinGroup", array('id' => $group->getId()));
        } else if ($group->getVisibility() == EGroup::VISIBILITY_PRIVATE) {
            return $this->forward("TrazeoFrontBundle:PanelGroups:requestJoinGroup", array('id' => $group->getId()));
        } else {
            die("Error");
        }
    }

    /**
     * @Route("/execute/{module}", name="executeAction")
     * @ParamConverter("module", class="TrazeoMyPageBundle:Module")
     */
    public function executeAction($module, Request $request) {
        return $module->getClass()->executeAction($this, $module, $request);
    }

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
     * @Route("/loginInGroup/{group_id}", name="loginInGroup")
     * @Template()
     */
    public function loginInGroupAction($group_id) {
        $em = $this->getDoctrine()->getEntityManager();

        $repositoryGroup = $em->getRepository("TrazeoBaseBundle:EGroup");
        $group = $repositoryGroup->findOneById($group_id);

        return array(
            'group' => $group
        );
    }

    /**
     * @Route("/loginSaveInGroup/{group_id}", name="loginSaveInGroup")
     * @Template()
     */
    public function loginSaveInGroupAction($group_id, Request $request) {
        $em = $this->getDoctrine()->getEntityManager();

        $repositoryGroup = $em->getRepository("TrazeoBaseBundle:EGroup");
        $group = $repositoryGroup->findOneById($group_id);

        $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($request->get('email'));
        $encoder = $this->get('security.encoder_factory')->getEncoder($user);
        $encodedPass = $encoder->encodePassword($request->get('password'), $user->getSalt());

        if ($user->getPassword() === $encodedPass) {
            $this->doLogin($user);
            return $this->doJoin($group);
        }
    }

    /**
     * @Route("/registerInGroup/{group_id}", name="registerInGroup")
     * @Template()
     */
    public function registerInGroupAction($group_id) {
        $em = $this->getDoctrine()->getEntityManager();

        $repositoryGroup = $em->getRepository("TrazeoBaseBundle:EGroup");
        /** @var EGroup $group */
        $group = $repositoryGroup->findOneById($group_id);

        $user = new User();
        $form_user = $this->createForm(new UserDirectType(), $user);


        for($i = 0; $i < 5; $i++) {
            $child = new EChild();
            $f = new ChildOptionalType();
            $f->setName("child_".$i);
            $form_children[] = $this->createForm($f, $child, array(
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
        }

        $userExtendCustom = new UserExtend();
        $form_userExtendCustom = $this->createForm(new UserExtendCustomType(), $userExtendCustom);

        return array(
            'group' => $group,
            'form_user' => $form_user->createView(),
            'form_userExtend' => $form_userExtendCustom->createView(),
            'form_children_0' => $form_children[0]->createView(),
            'form_children_1' => $form_children[1]->createView(),
            'form_children_2' => $form_children[2]->createView(),
            'form_children_3' => $form_children[3]->createView(),
            'form_children_4' => $form_children[4]->createView()
        );
    }

    /**
     * @Route("/welcome/{group}", name="welcome_login")
     * @ParamConverter("group", class="TrazeoBaseBundle:EGroup")
     * @Template()
     */
    public function welcomeLoginAction($group) {
        return array(
            'group' => $group
        );
    }

    /**
     * @Route("/saveInGroup/{group_id}", name="saveInGroup")
     */
    public function saveInGroup($group_id, Request $request) {
        $em = $this->getDoctrine()->getEntityManager();

        /** @var EGroupRepository $repositoryGroup */
        $repositoryGroup = $em->getRepository("TrazeoBaseBundle:EGroup");
        /** @var EGroup $group */
        $group = $repositoryGroup->findOneById($group_id);

        $container = $this->get('sopinet_flashMessages');
        if ($request->get('condiciones') != "on") {
            $container->addFlashMessages("warning","Debe aceptar las condiciones de uso.");
            return $this->redirect($this->generateUrl('registerInGroup', array('group_id' => $group->getId())));
        }

        $form_user = $this->createForm(new UserDirectType(), new User());
        $form_user->handleRequest($request);

        $form_children = array();
        for ($i = 0; $i < 5; $i++) {
            $child = new EChild();
            $f = new ChildOptionalType();
            $f->setName("child_".$i);
            $f_children = $this->createForm($f, $child, array(
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
            $f_children->handleRequest($request);
            $form_children[] = $f_children;
        }

        $spainCode = $em->getRepository('JJs\Bundle\GeonamesBundle\Entity\Country')->findOneByCode("ES");

        $userExtendCustom = new UserExtend();
        $form_userExtendCustom = $this->createForm(new UserExtendCustomType(), $userExtendCustom);
        $form_userExtendCustom->handleRequest($request);


        $user_check = $request->request->get('trazeo_mypagebundle_userdirecttype');
        $user_exists = $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($user_check['email']);
        if ($user_exists != null) {
            $container = $this->get('sopinet_flashMessages');
            $container->addFlashMessages("warning","Este usuario ya existe, si es el suyo, acceda desde esta pantalla.");
            return $this->redirect($this->generateUrl('fos_user_security_login'));
            //return $this->redirect($this->generateUrl('loginInGroup', array('group_id' => $group->getId())));
        }

        if ($form_user->isValid() && $form_userExtendCustom->isValid()) {
            /** @var User $user */
            $user = $form_user->getData();
            $user->setPlainPassword($user->getPassword());
            $user->setPassword(null);
            $user->setUsername($user->getEmail());
            $user->setEnabled(true);
            $em->persist($user);
            $em->flush();

            $children = array();
            foreach($form_children as $f) {
                if ($f->getData()->getNick() != null && $f->getData()->getNick() != "") {
                    /** @var EChild $child */
                    $child = $f->getData();
                    $child->setVisibility(false);
                    $em->persist($child);
                    $em->flush();
                    $children[] = $child;
                }
            }

            // Grabamos UserExtend
            /** @var UserExtend $userExtend */
            $userExtend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
            $dataUE = $request->get('trazeo_basebundle_userextendcustom');

            $userExtend->setMobile($dataUE['mobile']);
            $userExtend->setName($dataUE['name']);
            if ($request->get('trazeo_basebundle_userextend[country]') == '') {
                $userExtend->setCountry($spainCode);
            }
            $helper = $this->get('trazeo_base_helper');
            $city_entity = $helper->getCities($request->get('city'), 10, true);
            if (count($city_entity) > 0) {
                $userExtend->setCity($city_entity[0]);
            }
            $em->persist($userExtend);
            $em->flush();

            foreach($children as $child) {
                $userExtend->addChild($child);
                $em->persist($userExtend);
                $em->flush();
            }

            // Grabamos Niño y Grupo
            foreach($children as $child) {
                $child->addUserextendchild($userExtend);
                $em->persist($child);
                $em->flush();
            }

            $this->doLogin($user);
            $this->doJoin($group);

            return $this->redirect($this->generateUrl('welcome_login', array(
                'group' => $group->getId()
            )));
        } else {
            /**
            if (!$form_user->isValid()) {
                ldd($form_user->getErrorsAsString());
            }
            if (!$form_userExtend->isValid()) {
                ldd($form_userExtend->getErrorsAsString());
            }
            if (!$form_children->isValid()) {
                ld($request);
                ldd($form_children->getErrorsAsString());
            }
             **/


            $container->addFlashMessages("warning","Ha ocurrido algún error, por favor, revise los datos.");
            return $this->redirect($this->generateUrl('registerInGroup', array('group_id' => $group->getId())));


        }
    }
}