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
class CustomMsg {

    public function __construct(){
        $this->msg=new Msg();
    }

    const IOS="iOS";
    const ANDROID="Android";

    public $msg;

    public $custom_data;
}
?>