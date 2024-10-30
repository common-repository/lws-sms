<?php

class CheckSender{

    public function __construct(){}

    public static function init(){
        add_action('update_senders', array(get_called_class(), 'check'));
    }

    public static function check(){
        if ( ! class_exists( 'ApiCalls', false ) ) {
            include_once LWS_SMS_DIR . '/src/API/ApiCalls.php';
        } 
        $senders = ApiCalls::apiLoadSenders();
        $sender = array();
        if ($senders['code'] == "100"){
            if (!empty($senders['list_senderid'])){
                foreach ($senders['list_senderid'] as $s) {
                    if ($s['status'] == "unblock"){
                        $sender[$s['id']] = $s['sender_id'];
                    }
                }
                update_option("lws_sender_id", $sender);       
                return true; 
            }
            else{
                update_option("lws_sender_id", 'NOSENDER');
                return true;
            }
        }
        else{
            return false;
            wp_die("Error | Failed");
        }      
    }
}