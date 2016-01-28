<?php
    namespace Sopinet\GCMBundle\Event;

    use Symfony\Component\EventDispatcher\Event;
    use Sopinet\GCMBundle\Model\Msg;



    class GCMEvent extends Event
    {
        private $msg;
        private $container;

        public function __construct($container,$msg)
        {
            $this->msg = $msg;
            $this->container = $container;
        }

        public function getMsg()
        {
            return $this->msg;
        }

        public function getContainer()
        {
            return $this->container;
        }
    }
?>