<?php
    namespace Sopinet\GCMBundle\Model;

    /**
     * text: file o texto
     * type: text o file
     * chatid: id del chat
     * from: REGISTRATION_ID
     *
     *
     */
    class Msg {
        const IOS="iOS";
        const ANDROID="Android";

        public $text;

        public $type;

        public $chatid;

        public $chattype;

        public $msgid;

        public $from;

        public $phone;

        public $time;

        public $device;
    }
?>