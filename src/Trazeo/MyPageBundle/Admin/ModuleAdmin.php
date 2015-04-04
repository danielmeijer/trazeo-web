<?php
namespace Trazeo\MyPageBundle\Admin;
 
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Trazeo\MyPageBundle\Entity\Module;

class ModuleAdmin extends Admin
{
  protected $translationDomain = 'TrazeoBaseBundleAdmin';

  protected function configureFormFields(FormMapper $formMapper)
  {
    $formMapper
        ->add('title')
        ->add('content', null, array('required' => false))
        ->add('type', 'choice', array('choices' => array(
            Module::TYPE_COMBOBIGINTRO => "ComboBigIntro",
            Module::TYPE_HTML => "Html",
            Module::TYPE_IMAGE => "Image",
            Module::TYPE_TRAZEOROUTES => "TrazeoRoutes",
            Module::TYPE_FORMCONTACT => "FormContact"
        ) ))
        ->add('position')
        ->add('menu')
    ;
  }
 
  protected function configureDatagridFilters(DatagridMapper $datagridMapper)
  {
    $datagridMapper
        ->add('title')
        ->add('content')
        ->add('type')
        ->add('position')
        ->add('menu')
    ;
  }
 
  protected function configureListFields(ListMapper $listMapper)
  {
    $listMapper
        ->addIdentifier('id')
        ->add('title')
        ->add('content')
        ->add('type')
        ->add('position')
        ->add('menu')
    ;
  }
}
?>