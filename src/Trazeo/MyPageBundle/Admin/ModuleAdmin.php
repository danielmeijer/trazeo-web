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

  protected function configureFormFields(FormMapper $formMapper)
  {
    $formMapper
        ->add('title', null, array('label' => 'list.label_title'))
        ->add('content', null, array('required' => false, 'label' => 'list.label_content'))
        ->add('type', 'choice', array('label' => 'list.label_type', 'choices' => array(
            Module::TYPE_COMBOBIGINTRO => "ComboBigIntro",
            Module::TYPE_HTML => "Html",
            Module::TYPE_IMAGE => "Image",
            Module::TYPE_IMAGES_ROW => "ImagesRow",
            Module::TYPE_TRAZEOROUTES => "TrazeoRoutes",
            Module::TYPE_TRAZEOROUTESSCHOOL => "TrazeoRoutesSchool",
            Module::TYPE_FORMCONTACT => "FormContact",
            Module::TYPE_GENERALDATA => "GeneralData"
        ) ))
        ->add('position', null, array('label' => 'list.label_position'))
        ->add('menu', null, array('label' => 'list.label_menu'))
    ;
  }
 
  protected function configureDatagridFilters(DatagridMapper $datagridMapper)
  {
    $datagridMapper
        ->add('title', null, array('label' => 'list.label_title'))
        ->add('content', null, array('label' => 'list.label_content'))
        ->add('type', null, array('label' => 'list.label_type'))
        ->add('position', null, array('label' => 'list.label_position'))
        ->add('menu', null, array('label' => 'list.label_menu'))
    ;
  }
 
  protected function configureListFields(ListMapper $listMapper)
  {
    $listMapper
        ->addIdentifier('id')
        ->add('title', null, array('label' => 'list.label_title'))
        ->add('content', null, array('label' => 'list.label_content'))
        ->add('type', null, array('label' => 'list.label_type'))
        ->add('position', null, array('label' => 'list.label_position'))
        ->add('menu', null, array('label' => 'list.label_menu'))
    ;
  }
}
?>
