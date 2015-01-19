<?php
// src/Acme/AcmeBundle/YourBundle/Security/Authorization/Voter/CompanyVoter.php
namespace Trazeo\BaseBundle\Voter;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Application\Sonata\UserBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

class ProjectVoter implements VoterInterface
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    public function __construct($data) {
        //ldd($data);
    }

    public function supportsAttribute($attribute)
    {
        //ldd($attribute);
        return in_array($attribute, array(
            self::VIEW,
            self::EDIT,
            self::DELETE
        ));
    }

    public function supportsClass($class)
    {
        //return true;
        $supportedClass = 'Trazeo\BaseBundle\Entity\EGroup';

        //ld($class);
        if ($class === "Trazeo\BaseBundle\Voter\ProjectVoter") {
            //ldd($this);
        }
        //ld($supportedClass);
        //ld($class);
        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        //ldd(get_class($object));
        //ldd($object);
        // check if class of this object is supported by this voter
        if (!$this->supportsClass(get_class($object))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        ldd($object);

        // check if the voter is used correct, only allow one attribute
        // this isn't a requirement, it's just one easy way for you to
        // design your voter
        if (1 !== count($attributes)) {
            throw new \InvalidArgumentException(
                'Only one attribute is allowed for VIEW or EDIT'
            );
        }

        ldd($attributes);

        /**
        foreach ($attributes as $attribute) {
            if ( !$this->supportsAttribute($attribute) ) {
                return VoterInterface::ACCESS_ABSTAIN;
            }
        }

        $user = $token->getUser();
        if ( !($user instanceof UserInterface) ) {
            return VoterInterface::ACCESS_DENIED;
        }

        // check if the user has the same company
        if ( $user->getCompany() == $object->getCompany() ) {
            return VoterInterface::ACCESS_GRANTED;
        }
         **/

        return VoterInterface::ACCESS_DENIED;
    }

}
?>