<?php

namespace Trazeo\BaseBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Trazeo\BaseBundle\Entity\EChild;
use Trazeo\BaseBundle\Entity\EGroup;
use Trazeo\BaseBundle\Service\Helper;
use Trazeo\MyPageBundle\Entity\Page;

class EGroupAdmin extends Admin
{
    /**
     * Security Context
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected $securityContext;

    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    private $container;

    public function setSecurityContext(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function setContainer (\Symfony\Component\DependencyInjection\ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('visibility')
            ->add('hasRide')
            ->add('name')
            ->add('admin')
            ->add('childs')
            ->add('route')
            ->add('inviteGroup')
            ->add('ride')
            ->add('createdAt')
            ->add('updatedAt');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('visibility')
            ->add('hasRide')
            ->add('name')
            ->add('admin')
            ->add('childs')
            ->add('route')
            ->add('inviteGroup')
            ->add('ride')
            ->add('page')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
            ->add('createdAt')
            ->add('updatedAt');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('visibility')
            ->add('hasRide')
            ->add('name')
            ->add('admin')
            ->add('childs')
            ->add('route')
            ->add('bymode', 'choice', array('choices' => array(
                EGroup::BYMODE_PEDIBUS => "Pedibús",
                EGroup::BYMODE_BICIBUS => "Bicibús",
            ) ))
            ->add('inviteGroup')
            ->add('ride')
            ->add('page');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('visibility')
            ->add('hasRide')
            ->add('name');
    }

    /**
     * {@inheritdoc}
     */
    public function createQuery($context = 'list')
    {
        //ldd($this->securityContext->isGranted('ROLE_ADMIN'));

        // $queryBuilder = $this->getModelManager()->getEntityManager($this->getClass())->createQueryBuilder();

        $query = parent::createQuery($context);

        if (!$this->securityContext->isGranted('ROLE_SUPER_ADMIN')) {
            $user = $this->securityContext->getToken()->getUser();

            /** @var Helper $helper */
            $helper = $this->container->get('trazeo_base_helper');
            /** @var Page $page */
            $page = $helper->getPageBySubdomain();

            if ($page == null) {
                die("No Project for you");
            }

            if ($page->getUserextend()->getUser()->getId() != $user->getId()) {
                die("No Project for you");
            }

            foreach ($page->getGroups() as $group) {
                $query->orWhere($query->getRootAlias() . '.id=' . $group->getId());
            }
        }

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($group)
    {
        // Si elimino un padre se quitan los niños que pueda tener

        /** @var EGroup $group */
        /** @var EChild $child */
        foreach ($group->getChilds() as $child) {
            $isChild = false;
            foreach ($child->getUserextendchilds() as $userC) {
                foreach ($group->getUserextendgroups() as $userG) {
                    if ($userC->getId() == $userG->getId()) {
                        $isChild = true;
                    }
                }
            }
            if (!$isChild) {
                $group->removeChild($child);
            }
        }

        //Si saco de un grupo a un padre sale tb del chat
        if ($group->getChat()!=null) {
            $chat=$group->getChat();
            $chat->getChatMembers()->map(function($chatMember) use($group) {
                /** @var UserExtend $chatMember */
                if (!$group->getUserextendgroups()->exists($chatMember)) {
                    $chatMember->removeChat($group->getChat());
                    $group->getChat()->removeChatMember($chatMember);

                    return null;
                }
                /** @var EntityManager $em */
                $em=$this->container->get('doctrine.orm.default_entity_manager');
                $em->persist($chatMember);

                return $chatMember;
            });
            /** @var EntityManager $em */
            $em=$this->container->get('doctrine.orm.default_entity_manager');
            $em->persist($chat);
            $em->flush();
        }
    }
}
