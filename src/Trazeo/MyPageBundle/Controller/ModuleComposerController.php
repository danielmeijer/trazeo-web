<?php

namespace Trazeo\MyPageBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sonata\AdminBundle\Admin\AdminInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Trazeo\BaseBundle\Entity\EChild;
use Trazeo\BaseBundle\Entity\EChildRepository;
use Trazeo\BaseBundle\Entity\EGroup;
use Trazeo\BaseBundle\Entity\EGroupRepository;
use Trazeo\BaseBundle\Entity\ERide;
use Trazeo\BaseBundle\Entity\ERideRepository;
use Trazeo\MyPageBundle\Entity\Menu;
use Trazeo\MyPageBundle\Entity\Module;
use Trazeo\MyPageBundle\Form\BarAdminType;
use Trazeo\MyPageBundle\Form\ModuleEditComposerType;
use Trazeo\MyPageBundle\Form\RegisteredAdminType;
use Trazeo\MyPageBundle\Form\RegisteredEvolutionAdminType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/admin/moduleComposer")
 */
class ModuleComposerController extends Controller
{
    /**
     * @Route("/view/", name="moduleComposer_view")
     * @Template()
     */
    public function viewAction(Request $request)
    {
        // http://blog.eike.se/2014/03/custom-page-controller-in-sonata-admin.html
        $admin_pool = $this->get('sonata.admin.pool');

        /** @var Helper $helper */
        $helper = $this->get('trazeo_base_helper');
        $page = $helper->getPageBySubdomain();

        return array(
            'admin_pool' => $admin_pool,
            'page' => $page
        );
    }

    /**
     * @Route("/editModule/{module}", name="moduleComposer_editModule")
     * @ParamConverter("module", class="TrazeoMyPageBundle:Module")
     * @Template()
     */
    public function editModuleAction(Module $module, Request $request) {
        // http://blog.eike.se/2014/03/custom-page-controller-in-sonata-admin.html
        $admin_pool = $this->get('sonata.admin.pool');

        $formModule = $this->createForm(new ModuleEditComposerType($this), $module);
        /** @var Form $formModule */
        $formModule = $module->getClass()->addFieldsContentAdmin($formModule, $this, $module);

        if ($request->getMethod() == 'POST') {
            $formModule->handleRequest($request);

            $module = $formModule->getData();
            //$module->setContent($content);
            $em = $this->getDoctrine()->getEntityManager();

            $em->persist($module);
            $em->flush();

            $request->getSession()->getFlashBag()->add("success", "Módulo guardado con éxito");

            return new RedirectResponse($this->generateUrl('moduleComposer_view'));
        }

        return array(
            'admin_pool' => $admin_pool,
            'module' => $module,
            'formModule' => $formModule->createView()
        );
    }

    /**
     * @Route("/saveOrderModules/{parentID}/{order_string}", name="moduleComposer_saveOrderModules")
     */
    public function saveOrderModulesAction($parentID, $order_string) {
        $em = $this->get('doctrine')->getManager();

        $repositoryMenu = $em->getRepository("TrazeoMyPageBundle:Menu");
        $temp = explode("-", $parentID);
        $menuID = $temp[1];
        /** @var Menu $menu */
        $menu = $repositoryMenu->findOneById($menuID);

        // Desvinculamos los módulos
        /** @var Module $module */
        foreach($menu->getModules() as $module) {
            $module->setMenu(null);
            $em->persist($module);
            $em->flush();
        }

        // Vinculamos los módulos ordenamos
        $repositoryModule = $em->getRepository("TrazeoMyPageBundle:Module");
        $order_array = explode(",", $order_string);
        $i = 1;

        foreach($order_array as $order) {
            $temp = explode("-", $order);

            $moduleID = $temp[1];
            /** @var Module $module */
            $module = $repositoryModule->findOneById($moduleID);
            if ($module != null) {
                $module->setMenu($menu);
                $module->setPosition($i);
                $em->persist($module);
                $em->flush();
                $i++;
            }
        }

        die("Ok");
    }

    /**
     * @Route("/saveOrderMenus/{order_string}", name="moduleComposer_saveOrderMenus")
     */
    public function saveOrderMenusAction($order_string  ) {
        $em = $this->get('doctrine')->getManager();

        $repositoryMenu = $em->getRepository("TrazeoMyPageBundle:Menu");
        $order_array = explode(",", $order_string);
        $i = 1;

        foreach($order_array as $order) {
            $temp = explode("-", $order);
            $menuID = $temp[1];
            /** @var Menu $menu */
            $menu = $repositoryMenu->findOneById($menuID);
            $menu->setPosition($i);
            $em->persist($menu);
            $em->flush();
            $i++;
        }

        die("Ok");
    }
}