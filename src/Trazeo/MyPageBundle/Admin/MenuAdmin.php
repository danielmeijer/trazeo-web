<?php
namespace Trazeo\MyPageBundle\Admin;
 
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Trazeo\BaseBundle\Service\Helper;
use Trazeo\MyPageBundle\Entity\Menu;

class MenuAdmin extends Admin
{
  /** @var Helper $baseHelper */
  private $baseHelper;
  /** @var \Trazeo\MyPageBundle\Entity\Page $page */
  private $page;

  public function __construct($code, $class, $baseControllerName, Helper $baseHelper)
  {
      $this->baseHelper = $baseHelper;
      $this->page = $this->baseHelper->getPageBySubdomain();
      return parent::__construct($code, $class, $baseControllerName);
  }

    public function getNewInstance() {
      /** @var Menu $instance */
      $instance = parent::getNewInstance();

      $instance->setPage($this->page);
      $instance->setPosition(0);
      $instance->setLink("#");

      return $instance;
  }

  protected function configureFormFields(FormMapper $formMapper)
  {
    $formMapper
        ->add('title', null, array('label' => 'list.label_title'))
        ->add('color_back')
        ->add('color_front')
    ;

    if ($this->page == null) {
        $formMapper
        ->add('page', null, array('label' => 'form.label_page'))
        ->add('modules', null, array('label' => 'list.label_modules'))
        ->add('position', null, array('label' => 'list.label_position'))
        ->add('link', null, array('label' => 'list.label_link'))
        ;
    }
  }
 
  protected function configureDatagridFilters(DatagridMapper $datagridMapper)
  {
    $datagridMapper
        ->add('title', null, array('label' => 'list.label_title'))
        ->add('page', null, array('label' => 'form.label_page'))
        ->add('modules', null, array('label' => 'list.label_modules'))
        ->add('position', null, array('label' => 'list.label_position'))
        ->add('link', null, array('label' => 'list.label_link'))
    ;
  }
 
  protected function configureListFields(ListMapper $listMapper)
  {
    if ($this->page != null) {
        $redirection = new RedirectResponse($this->getConfigurationPool()->getContainer()->get('router')->generate('moduleComposer_view'));
        $redirection->send();
    }

    $listMapper
        ->addIdentifier('id')
        ->add('title', null, array('label' => 'list.label_title'))
        ->add('page', null, array('label' => 'form.label_page'))
        ->add('modules', null, array('label' => 'list.label_modules'))
        ->add('position', null, array('label' => 'list.label_position'))
        ->add('link', null, array('label' => 'list.label_link'))
    ;
  }
}
?>