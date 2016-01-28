<?php
namespace Sopinet\GCMBundle\Entity;
use Application\Sopinet\UserBundle\ApplicationSopinetUserBundle;
use Application\Sopinet\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class DeviceRepository extends EntityRepository
{
    /**
     * Comprueba si existe un dispositivo en la base de datos
     *
     * @param String $deviceId
     *
     * @return bool
     */
    public function existsDevice($deviceId)
    {
        return $this->findOneByDeviceId($deviceId)!=null;
    }

    /**
     * Añade un dispositivo a un usuario en la base de datos
     *
     * @param String $deviceId
     * @param String $token
     * @param $user
     * @param String $type('Android','iOS')
     *
     * @return Device
     */
    public function addDevice($deviceId, $user, $token,$type='Android')
    {
        $em = $this->getEntityManager();

        if (!$this->existsDevice($deviceId)) {
            $device = new Device();
            $device->setDeviceId($deviceId);
        } else {
            $device = $this->findOneByDeviceId($deviceId);
        }
        $device->setToken($token);
        $device->setDateRegister(new \DateTime());
        $device->setType($type);
        $device->setUser($user);
        $em->persist($device);
        $em->flush();

        return $device;
    }

    public function supportsClass($class)
    {
        return $class === 'Sopinet\GCMBundle\Entity\Device';
    }
}
?>