<?php

/**
 * Class dedicated to all API Calls for LWS SMS
 */
class ApiCalls{

    public function __construct(){}

    /**
     * Fetch needed informations about the user
     * 
     * @return array All fetched informations
     */
    public static function apiInfo($user, $key){
        if (!empty($user) && !empty($key)){
            $resp = wp_remote_post(
                "https://sms.lws.fr/plugin/api",
                array(
                'method'      => 'POST',
                'timeout'     => 30,
                'blocking'    => true,
                'headers'     => array(),
                'body'        => array(
                    'action' => 'check-client',
                    'username' => urlencode($user),
                    'api_key_client' => $key
                    ),
                )                
            );

            if ($resp instanceof WP_Error){
                return $resp;
            } else{
                try{
                    return json_decode($resp['body'], true);
                } catch (Exception $e){
                    return new WP_Error('1', 'An error occured !', $e);
                }
            }            
        }
        else{
            return false;
            wp_die("No user or key | Failed");
        }
    }

    /**
     * Send SMS to all $receivers with $sender being an ID and $message the content
     * Needs a user to be connected to work
     */
    public static function apiSendSMS($sender, $receivers, $message){
        if ( !$client = get_option("lws_user_sms") ){
            return false;
            wp_die("No user available | Failed");
        }
        else{
            return wp_remote_post(
                "https://sms.lws.fr/sms/api",
                array(
                'method'      => 'POST',
                'timeout'     => 30,
                'blocking'    => true,
                'headers'     => array(),
                'body'        => array(
                    'action' => 'send-sms',
                    'to' => $receivers,
                    'from' => $sender,
                    'sms' => $message,
                    'api_key' => $client['api_key']
                    ),
                )
            );
            wp_die("Message sent to : -{$receivers}- by -{$sender}- | Success");
        }

    }

    /**
     * Fetch remaining amount of credits for currently connected user
     * @return int Amount of credits left
     */
    public static function apiGetBalance(){
        if ( !$client = get_option("lws_user_sms") ){
            return false;
            wp_die("No user available | Failed");
        }
        else{
            $resp = wp_remote_post(
                "https://sms.lws.fr/plugin/api",
                array(
                'method'      => 'POST',
                'timeout'     => 30,
                'blocking'    => true,
                'headers'     => array(),
                'body'        => array(
                    'action' => 'solde-client',
                    'id_client' => urlencode($client['id_client']),
                    'api_key_client' => $client['api_key'],
                    ),
                )
            );
            if ($resp instanceof WP_Error){
                return $resp;
            } else{
                return json_decode($resp['body'], true)['solde'];
            }
        }
    }

    /**
     * Check if current API Key is still valid
     * @return boolean false if key is wrong, true otherwise
     */
    public static function apiCheckIsValid(){
        if ( !$client = get_option("lws_user_sms") ){
            return false;
            wp_die("No user available | Failed");
        }
        else{
            $resp = wp_remote_post(
                "https://sms.lws.fr/plugin/api",
                array(
                'method'      => 'POST',
                'timeout'     => 30,
                'blocking'    => true,
                'headers'     => array(),
                'body'        => array(
                    'action' => 'check-api-key',
                    'id_client' => urlencode($client['id_client']),
                    'api_key_client' => $client['api_key'],
                    ),
                )
            );
            if ($resp instanceof WP_Error){
                return $resp;
            } else{
                return json_decode($resp['body'], true)['code'] == "100" ? true : false;
            }
            
        }        
    }
    
    /**
     * Fetch an array with every senders for current user
     * @return array List of senderIDs
     */
    public static function apiLoadSenders(){
        if ( !$client = get_option("lws_user_sms") ){
            return false;
            wp_die("No user available | Failed");
        }
        else{
            $resp = wp_remote_post(
                "https://sms.lws.fr/plugin/api",
                array(
                'method'      => 'POST',
                'timeout'     => 30,
                'blocking'    => true,
                'headers'     => array(),
                'body'        => array(
                    'action' => 'load-sender',
                    'id_client' => urlencode($client['id_client']),
                    ),
                )
            );
            if ($resp instanceof WP_Error){
                return $resp;
            } else{
                return json_decode($resp['body'], true);
            }
        }
    }

    /**
     * Fetch an array with the last 5000 SMS sent by the current user
     * @return array Last 5000 SMS of the user
     */
    public static function apiSMSHistory(){
        if ( !$client = get_option("lws_user_sms") ){
            return false;
            wp_die("No user available | Failed");
        }
        else{
            $resp = wp_remote_post(
                "https://sms.lws.fr/plugin/api",
                array(
                'method'      => 'POST',
                'timeout'     => 30,
                'blocking'    => true,
                'headers'     => array(),
                'body'        => array(
                    'action' => 'get-sms-history',
                    'id_client' => urlencode($client['id_client']),
                    ),
                )
            );
            if (is_wp_error($resp)){
                return $resp;
            } else{
                return json_decode($resp['body'], true)['sms_history'];
            }
        }
    }

    /**
     * Add a new sender $id to the current client.
     * Will need time to get validated
     * 
     * @return boolean true if added, false otherwise
     */
    public static function apiAddSender($id){
        if ( !$client = get_option("lws_user_sms") ){
            return false;
            wp_die("No user available | Failed");
        }
        else{
            $resp = wp_remote_post(
                "https://sms.lws.fr/plugin/api",
                array(
                'method'      => 'POST',
                'timeout'     => 30,
                'blocking'    => true,
                'headers'     => array(),
                'body'        => array(
                    'action' => 'add-sender-wp',
                    'sender' => $id,
                    'id_client' => urlencode($client['id_client']),
                    ),
                )
            );
            if ($resp instanceof WP_Error){
                return $resp;
            } else{
                return json_decode($resp['body'], true)['code'] == "100" ? true : false;
            }
        }
    }
}