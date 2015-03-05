<?php
    namespace Sopinet\GCMBundle\Entity;
    use Application\Sopinet\UserBundle\ApplicationSopinetUserBundle;
    use Application\Sopinet\UserBundle\Entity\User;
    use Doctrine\ORM\EntityRepository;

    class DeviceRepository extends EntityRepository {
        /**
         * Comprueba si existe un dispositivo en la base de datos
         * TODO: Sería bueno comprobar por Usuario, porque puede que un dispositivo cambie de "dueño"
         *
         * @param $device_id
         * @return bool
         */
        public function existsDevice($device_id) {
            $device = $this->findByToken($device_id);
            return (count($device) > 0);
        }

        /**
         * Añade un dispositivo a un usuario en la base de datos
         *
         * @param $device_id
         * @param $user
         * @param $type('Android','iOS')
         * @return bool|Device
         */
        public function addDevice($device_id, User $user ,$type='Android') {
            $em = $this->getEntityManager();
            $repositoryUser = $em->getRepository("ApplicationSopinetUserBundle:User");

            if (!$this->existsDevice($device_id)) {
                // TODO: Esto hay que revisarlo
                // Eliminar los devices antiguos del sistema, dejar sólo 1
                    $repositoryUser->clearDevices($user);
                // Hasta aquí hay que cambiarlo
                $device = new Device();
                $device->setToken($device_id);
                $device->setDateRegister(new \DateTime());
                $device->setType($type);
                $device->setUser($user);

                $em->persist($device);
                $em->flush();
                return $device;
            } else {
                return false;
            }
        }

        public function supportsClass($class) {
            return $class === 'Sopinet\GCMBundle\Entity\Device';
        }
    }
?>