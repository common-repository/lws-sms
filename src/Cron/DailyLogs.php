<?php

class DailyLogs{

    public function __construct(){}

    public static function init(){
        add_action('daily_logs_cron', array(get_called_class(), 'sendLogs'));
    }

    public static function sendLogs(){

        if ( ! class_exists( 'ApiCalls', false ) ) {
            include_once LWS_SMS_DIR . '/src/API/ApiCalls.php';
        }

        global $wpdb;
        $order_created = array();
        $users = array();
        $sessions = array();
        $sales = 0;
        $product = 0;
        $turnover = 0;
        $usage = "dailies";
        $users_registered = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix."users");
        $order_stats = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix."wc_order_stats");
        $order_sessions = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix."woocommerce_sessions");
        $product_lookup = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix."wc_product_meta_lookup");
        
        //Check for every lost carts of clients
        foreach ($order_sessions as $session) {
            //Check expiry of the session
            $expire = $session->session_expiry;
            //Get their cart if any
            $cart = unserialize(unserialize($session->session_value)['cart']);
            
            //If it is a lost cart and said cart is not empty, count it
            if ($expire - time() <= 43200 && $expire - time() > 0 && !empty($cart)) {
                $sessions[] = $session;
            }
        }

        //Check for the best sellings products
        foreach ($product_lookup as $product) {
            //Check for the total sales
            if ($product->total_sales > $sales) {
                $sales = $product->total_sales;
                $product_best = wc_get_product($product->product_id);
            }
        }
        $product = $product_best->name . __(" | Sales: ", "lws-sms") . $sales;
        
        //Check for every user created the day before the cron's activation
        foreach ($users_registered as $user) {
            if (explode(" ", $user->user_registered)[0] == (gmdate("Y-m-d", time() - 86400))) {
                $users[] = $user;
            }
        }
        
        //Check for every orders created the day before the cron activation
        foreach ($order_stats as $stats) {
            if (explode(" ", $stats->date_created_gmt)[0] == (gmdate("Y-m-d", time() - 86400))) {
                $order_created[] = $stats;
                //If the paiement is accepted
                if ($stats->status == "wc-processing") {
                    //Total amount gained that day
                    $turnover += floatval($stats->net_total);
                }
            }
        }
        
        //Stock the amount of new orders
        $new_orders = count($order_created);
        
        //Stock the amount of new users
        $new_users = count($users);
        
        //Stock the amount of lost carts
        $lost_carts = count($sessions);
        
        //Send SMS
        $admin_check = get_option('lws_checked_options');
        //If activated
        if ($admin_check[$usage]['is_checked']) {
            //Only if client has authorized SMS for this
            $checkboxes = array();
            get_user_meta($user->ID, 'checkboxes', true) ?
            $checkboxes = get_user_meta($user->ID, 'checkboxes', true) : update_user_meta($user->ID, 'checkboxes', $checkboxes);
            
            if (count($checkboxes) == 0 || $checkboxes[$usage]['is_checked']) {
                $models = get_option('lws_model_list');
                //Get every user who can manage woocommerce (admin)
                $users = get_users();
                $users_can = array();
                foreach ($users as $user) {
                    if (user_can($user, 'manage_woocommerce')) {
                        $users_can[] = $user;
                    }
                }
                //Get their phone numbers
                $num = "";
                $count = 0;
                foreach ($users_can as $user) {
                    if ($user->phone_sms) {
                        $num = $num . $user->phone_sms . ",";
                        $count++;
                    }
                }
                foreach ($models as $model) {
                    foreach($model['in_use'] as $in_use) {
                        if ($in_use == $usage) {
                            $balance = ApiCalls::apiGetBalance();
                            if (!is_numeric($balance)){
                                $balance = 0;
                            }
                            if ($balance - $model['nb_sms'] - $count >= 0) {
                                $message = $model['message'];
                                $message = str_replace("[[PanierAbandonnes]]", $lost_carts, $message);
                                $message = str_replace("[[MontantTotal]]", $turnover, $message);
                                $message = str_replace("[[Commandes]]", $new_orders, $message);
                                $message = str_replace("[[NouveauxClients]]", $new_users, $message);
                                $message = str_replace("[[MeilleureVente]]", $product, $message);
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
                                $message = str_replace("[[ListePanier]]", "", $message);                        
                                ApiCalls::apiSendSMS($model['sender'], $num, $message);
                                break;
                            }
                        }
                    }
                }
            }
        }
    }
}