<?php

class CheckSessions{

    public function __construct(){}

    public static function check(){
        if ( ! class_exists( 'ApiCalls', false ) ) {
            include_once LWS_SMS_DIR . '/src/API/ApiCalls.php';
        }

        $usage = 'sessions';
        $admin_check = get_option('lws_checked_options');
        $timer = get_option("lws_timer_cron");
        $time = 43200;
        //Based on a 48h-expiry
        switch ($timer) {
            case "hour":
                $time = 3600;
                break;
            case "three":
                $time = 10800;
                break;
            case "six":
                $time = 21600;
                break;
            case "twelve":
                $time = 43200;
                break;
            case "twentyfour":
                $time = 86400;
                break;
        }

        if ($admin_check[$usage]['is_checked']) {
            //Get every sessions stocked
            global $wpdb;
            $order_sessions = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix."woocommerce_sessions");
            
            foreach ($order_sessions as $session) {
                //Get everything known about the user
                $customer = unserialize(unserialize($session->session_value)['customer']);
                $user = get_user_by('id', $customer['id']);

                $checkboxes = array();
                get_user_meta($user->ID, 'checkboxes', true) ?
                $checkboxes = get_user_meta($user->ID, 'checkboxes', true) : update_user_meta($user->ID, 'checkboxes', $checkboxes);

                if (count($checkboxes) == 0 || $checkboxes[$usage]['is_checked']) {
                    //Get the phone_sms of an existing customer, if exist
                    $num = get_user_meta($customer['id'], 'phone_sms', true);
                    
                    //Check expiry of the session
                    $expire = ($session->session_expiry);
                    
                    //Get their cart if any
                    $cart = unserialize(unserialize($session->session_value)['cart']);
                    
                    //Get the total amount of the cart
                    $total = unserialize(unserialize($session->session_value)['cart_totals'])['total'];
                    
                    //Get every applied coupons on the cart
                    $coupons = unserialize(unserialize($session->session_value)['applied_coupons']);
                    
                    $list_coupon = "";
                    foreach ($coupons as $coupon) {
                        $listcoupon = $list_coupon . $coupon . "&nbsp;";
                    }
                    
                    $list_product = "";
                    foreach ($cart as $c) {
                        $p = wc_get_product($c['product_id'])->get_data();
                        $list_product = $list_product . $p['name'] . "(" . $c['quantity'] . ") ; " ;
                    }

                    if ($expire - time() <= $time && $expire - time() > 0) {      
                        $model = null;
                        foreach ($models as $m){
                            if (array_search($usage, $m['in_use'])){
                                $model = $m;
                            }
                        }       
                        if($num && !empty($cart) && $model !== null) {
                            $balance = ApiCalls::apiGetBalance();
                            if (!is_numeric($balance)){
                                $balance = 0;
                            }
                            if ($balance - $model['nb_sms'] >= 0) {
                                $message = $model['message'];
                                $message = str_replace("[[Nom]]", $user->last_name, $message);
                                $message = str_replace("[[Prenom]]", $user->first_name, $message);

                                $message = str_replace("[[MontantTotal]]", $total, $message);
                                $message = str_replace("[[ListePanier]]", $list_product, $message);

                                $message = str_replace("[[NomSite]]", get_bloginfo('name'), $message);
                                $message = str_replace("[[URLSite]]", get_bloginfo('url'), $message);
                                $message = str_replace("[[Date]]", gmdate("Y-m-d - H:i:s : ", time()), $message);

                                $message = str_replace("[[Prix]]", "", $message);
                                $message = str_replace("[[NumCmde]]", "", $message);                                
                                $message = str_replace("[[CodeCoupon]]", "", $message);
                                $message = str_replace("[[DescriptionCoupon]]", "", $message);
                                $message = str_replace("[[ValeurCoupon]]", "", $message);                                                
                                $message = str_replace("[[NomProduit]]", "", $message);
                                $message = str_replace("[[PrixProduit]]", "", $message);
                                $message = str_replace("[[DescriptionProduit]]", "", $message);
                                $message = str_replace("[[PanierAbandonnes]]", "", $message);
                                $message = str_replace("[[Commandes]]", "", $message);
                                $message = str_replace("[[NouveauxClients]]", "", $message);
                                $message = str_replace("[[MeilleureVente]]", "", $message);
                                ApiCalls::apiSendSMS($model['sender'], $num, $message);
                            }
                        }
                    }
                }
            }
        }
    }
}