<?php

namespace Trazeo\FrontBundle\Controller;

use Application\Sonata\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Trazeo\BaseBundle\Entity\ECatalogItem;
use Trazeo\BaseBundle\Entity\File;
use Trazeo\BaseBundle\Form\CatalogItemType;
/**
* @Route("catalogitem")
*/
class PanelCatalogItemController extends Controller
{
	/**
	 * @Route("/delete/{id}", name="panel_catalogitem_delete")
	 * @ParamConverter("item", class="TrazeoBaseBundle:ECatalogItem")
	 */
	public function deleteItemAction($id, Request $request) {
		$user = $this->get('security.context')->getToken()->getUser();
		$em = $this->get('doctrine.orm.entity_manager');
		$reCatalog = $em->getRepository('TrazeoBaseBundle:ECatalogItem');
		$item = $reCatalog->findOneById($id);
		$em->remove($item);
		$em->flush();
		
		return $this->redirect($this->generateUrl('panel_catalogitems_list'));		
	}

	
	private function showEditView(ECatalogItem $item) {
		$em = $this->get('doctrine.orm.entity_manager');
		$user = $this->get('security.context')->getToken()->getUser();

        $form_item = $this->createForm(new CatalogItemType(), $item, array(
            'action' => $this->generateUrl('panel_catalogitems_save', array('id' => $item->getId())),
            'method' => 'PUT',
        	'attr' => array(
  				'CatalogItem.help.points' => $this->get('translator')->trans('CatalogItem.help.points'),
        		'CatalogItem.help.position' => $this->get('translator')->trans('CatalogItem.help.position'),
        		'CatalogItem.help.company' => $this->get('translator')->trans('CatalogItem.help.company'),
        		'CatalogItem.help.title' => $this->get('translator')->trans('CatalogItem.help.title'),
        		'CatalogItem.help.description' => $this->get('translator')->trans('CatalogItem.help.description'),
                'CatalogItem.help.link' => $this->get('translator')->trans('CatalogItem.help.link'),        		)
        ));

        $form_item->add('submit', 'submit', array('label' => 'Update'));									
		
		// Sacamos los ficheros
        if (count($item->getFile()->toArray())>0){
            $file= $item->getFile()->toArray()[0];
        } else {
            $file = new File();
        }

		return $this->render(
				'TrazeoFrontBundle:PanelCatalogItem:edit.html.twig',
				array(
						'form_item' => $form_item->createView(),
						'file' => $file,
						'item' => $item
				)
		);		
	}
	
	/**
	 * @Route("/edit/{id}", name="panel_catalogitems_edit"))
	 * @ParamConverter("item", class="TrazeoBaseBundle:ECatalogItem")
	 */
	public function itemseditAction(ECatalogItem $id, Request $request)
	{
		$em = $this->get('doctrine.orm.entity_manager');
		$repositoryItem = $em->getRepository("TrazeoBaseBundle:ECatalogItem");
		 
		//Sacamos el item
		$item = $repositoryItem->findOneById($id);
		return $this->showEditView($item);
	}
		

	
	/**
	 * @Route("/save/{id}", name="panel_catalogitems_save"))
	 */
	public function itemssaveAction(Request $request,$id) {
		 
		$em = $this->get('doctrine.orm.entity_manager');
		$repositoryItem = $em->getRepository("TrazeoBaseBundle:ECatalogItem");
		$repositoryFile = $em->getRepository("TrazeoBaseBundle:File");
		 
		//Sacamos el item
		$item = $repositoryItem->findOneById($id);
		 
		//Sacamos los fichero que anteriormente hemos asociado
		$files = $repositoryFile->findByCatalogitems($item);
		 
		//Guardamos los parametros mandados del formulario
		$form = $this->createForm(new CatalogItemType(), $item, array(
            'action' => $this->generateUrl('panel_catalogitems_save', array('id' => $item->getId())),
            'method' => 'PUT',
        	'attr' => array(
  				'CatalogItem.help.points' => $this->get('translator')->trans('CatalogItem.help.points'),
        		'CatalogItem.help.position' => $this->get('translator')->trans('CatalogItem.help.position'),
        		'CatalogItem.help.company' => $this->get('translator')->trans('CatalogItem.help.company'),
        		'CatalogItem.help.title' => $this->get('translator')->trans('CatalogItem.help.title'),
        		'CatalogItem.help.description' => $this->get('translator')->trans('CatalogItem.help.description'),
                'CatalogItem.help.link' => $this->get('translator')->trans('CatalogItem.help.link'),
        		)
        ));
		$form->bind($request);
		 
		foreach($files as $file)
			$item->addFile($file);


		if ($form->isValid()) {
            $item->setComplete(1);
            $city = $request->get('city');
            $helper = $this->get('trazeo_base_helper');
            $city_entity = $helper->getCities($city, 10, true);
            if ($city!='' && count($city_entity) > 0) {
                $item->setCitys($city_entity[0]);
            }
            else{
                $item->setCitys(null);
            }
            $em->persist($item);
            $em->flush();
			return $this->redirect($this->generateUrl('panel_catalogitems_list'));
		}
	}
	
	/**
	 * @Route("/list", name="panel_catalogitems_list"))
	 * @Template
	 */
	public function itemslistAction()
	{
		$em    = $this->get('doctrine.orm.entity_manager');

        /** @var User $user */
        $user = $this->get('security.context')->getToken()->getUser();
        /** @var ECatalogItemRepository $reItem */
        $reItem = $em->getRepository("TrazeoBaseBundle:ECatalogItem");
        if (!$this->get('security.context')->isGranted('ROLE_CATALOG')) {
            throw new AccessDeniedException();
        }
		if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $items=$reItem->findByCitys($user->getUserExtend()->getCity());
        } else {
            $items=$reItem->findAll();
        }

		return array(
				'items' => $items
		);
	}	

    /**
     * Creates a form to create a CatalogItem entity.
     *
     * @param CatalogItem $item The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ECatalogItem $item)
    {
        $form = $this->createForm(new CatalogItemType(), $item, array(
            'action' => $this->generateUrl('panel_itemscatalog_create',array('id' => $item->getId())),
            'method' => 'POST',
        	'attr' => array(
        				'CatalogItem.help.points' => $this->get('translator')->trans('CatalogItem.help.points'),
        				'CatalogItem.help.position' => $this->get('translator')->trans('CatalogItem.help.position'),
        				'CatalogItem.help.company' => $this->get('translator')->trans('CatalogItem.help.company'),
        				'CatalogItem.help.title' => $this->get('translator')->trans('CatalogItem.help.title'),
        				'CatalogItem.help.description' => $this->get('translator')->trans('CatalogItem.help.description'),
                        'CatalogItem.help.link' => $this->get('translator')->trans('CatalogItem.help.link'),
        		)
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new CatalogItem entity.
     *
     * @Route("/new", name="panel_itemscatalog_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $item = new ECatalogItem();
  		$em = $this->getDoctrine()->getManager();
		$em->persist($item);
		$em->flush();      
        $form   = $this->createCreateForm($item);
		$file= $item->getFile();
        return array(
            'item' => $item,
            'form_item'   => $form->createView(),
            'file' => $file
        );
    }

    
    /**
     * Creates a new Childs entity.
     *
     * @Route("/", name="panel_itemscatalog_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
	    $em = $this->getDoctrine()->getManager();
        /** @var Translator $translator */
        $translator = $this->get('translator');
		$repositoryItem = $em->getRepository("TrazeoBaseBundle:ECatalogItem");
		 
		//Sacamos el item
		$item = $repositoryItem->findOneById($request->get('id'));
        $form = $this->createCreateForm($item);
        $form->handleRequest($request);


        if ($form->isValid()) {
        	$item->setComplete(1); 
			$city = $request->get('city');
			$helper = $this->get('trazeo_base_helper');
			$city_entity = $helper->getCities($city, 10, true);
			if ($city!='' && count($city_entity) > 0) {
				$item->setCitys($city_entity[0]);
			}
            else{
                $item->setCitys(null);
            }
            $em->persist($item);
            $em->flush();
        }
        $container = $this->get('sopinet_flashMessages');
        $notification = $container->addFlashMessages("success", $translator->trans('flash_messages.create_success'));
        return $this->redirect($this->generateUrl('panel_dashboard'));
    }
}