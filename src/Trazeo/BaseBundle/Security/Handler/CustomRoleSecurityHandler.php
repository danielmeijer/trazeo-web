<?php
namespace Trazeo\BaseBundle\Security\Handler;

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Security\Handler\RoleSecurityHandler;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Trazeo\BaseBundle\Entity\EGroup;
use Trazeo\MyPageBundle\Entity\Page;

/**
 * Class CustomRoleSecurityHandler
 */
class CustomRoleSecurityHandler extends RoleSecurityHandler
{
    protected $securityContext;

    protected $superAdminRoles;

    protected $roles;

    /**
     * @param \Symfony\Component\Security\Core\SecurityContextInterface $securityContext
     * @param array $superAdminRoles
     * @param $roles
     */
    public function __construct(SecurityContextInterface $securityContext, array $superAdminRoles, $roles)
    {
        $this->securityContext = $securityContext;
        $this->superAdminRoles = $superAdminRoles;
        $this->roles = $roles;
    }

    /**
     * {@inheritDoc}
     */
    public function isGranted(AdminInterface $admin, $attributes, $object = null)
    {
        // VIEW / EDIT
        if ($this->securityContext->isGranted('ROLE_SUPER_ADMIN')) return true;

        if (get_class($object) == "Trazeo\BaseBundle\Admin\EGroupAdmin") {
            $object->setSecurityContext($this->securityContext);
        }
        //return $this->isGranted($admin, $attributes, $object);
        if (get_class($object) != "Trazeo\BaseBundle\Entity\EGroup"
        && get_class($object) != "Trazeo\BaseBundle\Entity\EChild") {
            return true;
            //return VoterInterface::ACCESS_ABSTAIN;
        }

        if (get_class($object) == "Trazeo\BaseBundle\Entity\EChild") {
            $user = $this->securityContext->getToken()->getUser();
            foreach ($user->getUserExtend()->getPageFront() as $page) {
                /** @var EGroup $group */
                foreach ($page->getGroups() as $group) {
                    foreach ($group->getChilds() as $child) {
                        if ($child->getId() == $object->getId()) return true;
                    }
                }
            }
        }

        if (get_class($object) == "Trazeo\BaseBundle\Entity\EGroup") {
            $user = $this->securityContext->getToken()->getUser();
            foreach ($user->getUserExtend()->getPageFront() as $page) {
                /** @var EGroup $group */
                foreach ($page->getGroups() as $group) {
                    if ($group->getId() == $object->getId()) return true;
                }
            }
        }
        //ldd($user);

        return false;
        /*
        if ($user->getUserExtend()->getId() == $object->getAdmin()->getId()) {
            return true;
            //return VoterInterface::ACCESS_GRANTED;
        } else {
            return false;
            //return VoterInterface::ACCESS_DENIED;
        }
        ldd($user->getUserExtend());
        //if ()
        ldd($attributes);
        /** @var $user User */



        // do your stuff
    }
}