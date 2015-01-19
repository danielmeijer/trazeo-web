<?php
// src/Trazeo/BaseBundle/Voter/PostVoter.php
namespace Trazeo\BaseBundle\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProVoter extends AbstractVoter
{
    const VIEW = 'view';
    const EDIT = 'edit';

    protected function getSupportedAttributes()
    {
        return array(self::VIEW, self::EDIT);
    }

    protected function getSupportedClasses()
    {
        ldd($this);
        return array('Trazeo\BaseBundle\Entity\EGroup');
    }

    protected function isGranted($attribute, $post, $user = null)
    {
        ldd("H");
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }

        // custom business logic to decide if the given user can view
        // and/or edit the given post
        if ($attribute == self::VIEW && !$post->isPrivate()) {
            return true;
        }

        if ($attribute == self::EDIT && $user->getId() === $post->getOwner()->getId()) {
            return true;
        }

        return false;
    }
}