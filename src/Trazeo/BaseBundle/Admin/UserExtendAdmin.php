<?php
namespace Trazeo\BaseBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class UserExtendAdmin extends Admin
{
    protected $translationDomain = 'TrazeoBaseBundleAdmin';
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('nick')
            ->add('points')
            ->add('spendedPoints')
            ->add('mobile')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('nick')
            ->add('groups')
            ->add('tutorial')
            ->add('tutorialMap')
            ->add('points')
            ->add('spendedPoints')
            ->add('mobile')
            ->add('city.nameUtf8')
            ->add('useLike')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('nick')
            ->add('points')
            ->add('spendedPoints')
            ->add('mobile')
            ->add('city.nameUtf8')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }
    protected function configureShowFields(ShowMapper $showMapper)
    {
        // Here we set the fields of the ShowMapper variable, $showMapper (but this can be called anything)
        $showMapper
            ->add('nick')
            ->add('points')
            ->add('spendedPoints')
            ->add('mobile')
        ;
    }
    public function validate(ErrorElement $errorElement, $object)
    {
        /*$errorElement
          ->with('text')
          ->assertMaxLength(array('limit' => 3))
          ->end()
        ;*/
    }
}
?>