<?php
namespace Sopinet\GCMBundle;

final class GCMEvents
    {
        /**
         * This event occurs when a msg is received
         *
         * The event listener receives an
         * Sopinet\GCMBundle\Event\GCMEvent instance.
         *
         * @var string
         */
        const RECEIVED = 'gcm.received';
    }
?>