<?php

namespace Trazeo\BaseBundle\Service;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class EncoderBack implements PasswordEncoderInterface
{

    public function encodePassword($raw, $salt)
    {
        return md5($raw);
        //return hash('sha256', $salt . raw); // Custom function for encrypt
    }

    public function isPasswordValid($encoded, $raw, $salt)
    {
        return $encoded === $this->encodePassword($raw, $salt);
    }

}