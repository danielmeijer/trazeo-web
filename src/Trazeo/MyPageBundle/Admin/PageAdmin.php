<?php
namespace Trazeo\MyPageBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Trazeo\MyPageBundle\Entity\Page;

class PageAdmin extends Admin
{
    protected $translationDomain = 'TrazeoBaseBundleAdmin';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title')
            ->add('subdomain')
            ->add('userextend', null, array('required' => false))
            ->add('groups')
            ->add('data_email')
            ->add('data_facebook')
            ->add('data_twitter')
            ->add('data_web')
            ->add('data_phone')
            ->add('menus');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('subdomain')
            ->add('userextend')
            ->add('groups')
            ->add('menus');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('title')
            ->add('subdomain')
            ->add('userextend')
            ->add('data_phone')
            ->add('groups')
            ->add('menus');
    }

    public function postPersist($object)
    {
        /** @var Page $object */
        $user = $object->getUserextend()->getUser();
        $user->addRole('ROLE_ADMIN');
        $container = $this->getConfigurationPool()->getContainer();
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $entityManager->persist($user);
        $entityManager->flush();
    }
}

?>