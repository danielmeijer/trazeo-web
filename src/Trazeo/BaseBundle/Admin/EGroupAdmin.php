<?php

namespace Trazeo\BaseBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;
use Symfony\Component\Security\Core\SecurityContextInterface;

class EGroupAdmin extends Admin
{
    /**
     * Security Context
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected $securityContext;

    public function setSecurityContext(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * @param DatagridMapper $datagridMapper
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
            ->add('updatedAt')
        ;
    }

    /**
     * @param ListMapper $listMapper
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
            ->add('updatedAt')  
        ;
    }

    /**
     * @param FormMapper $formMapper
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
            ->add('inviteGroup')
            ->add('ride')
            ->add('page')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('visibility')
            ->add('hasRide')
            ->add('name')
        ;
    }

    public function createQuery($context = 'list')
    {
        $queryBuilder = $this->getModelManager()->getEntityManager($this->getClass())->createQueryBuilder();

        $query = parent::createQuery($context);

        if(!$this->securityContext->isGranted('ROLE_SUPER_ADMIN'))
        {
            $user = $this->securityContext->getToken()->getUser();

            $query->andWhere($query->getRootAlias().'.id=70');
            /**
            $query->add('select', '*')
                //->add('from'  , 'ApplicationSonataUserBundle:User c')
                ->orWhere($query->getRootAlias().".id=80");
                //->orWhere($query->getRootAlias().'.id='.$user->getId());
            ;
             **/

            /**foreach ($user->getChildren()->toArray() as $user) {
                $query->orWhere($query->getRootAlias().'.id='.$user->getId());
                foreach ($user->getChildren()->toArray() as $user) {
                    $query->orWhere($query->getRootAlias().'.id='.$user->getId());
                }
            }**/
        }

        //if is logged admin, show all data
        /**
        if ($this->securityContext->isGranted('ROLE_ADMIN')) {
            $queryBuilder->select('p')
                ->from($this->getClass(), 'p')
            ;
        } else {
            //for other users, show only data, which belongs to them
            $adminId = $this->securityContext->getToken()->getUser()->getAdminId();

            $queryBuilder->select('p')
                ->from($this->getClass(), 'p')
                ->where('p.adminId=:adminId')
                ->setParameter('adminId', $adminId, Type::INTEGER)
            ;
        }
         **/

        return $query;

        /**
        $proxyQuery = new ProxyQuery($queryBuilder);
        return $proxyQuery;
         **/
    }
}
