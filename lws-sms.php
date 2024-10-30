<?php

/**
 * Plugin Name:       LWS SMS
 * Plugin URI:        https://www.lws.fr/
 * Description:       With LWS SMS, create SMS Templates and configurate your website to send SMS to clients when you want it !
 * Version:           2.4.1
 * Author:            LWS
 * Author URI:        https://www.lws.fr
 * Tested up to:      6.6
 * Domain Path:       /languages
 *
 * @link              https://sms.lws.fr/
 * @since             1.0
 * @package           lws-sms
*/

if (! defined('ABSPATH')) {
    exit; //Exit if accessed directly
}

define('LWS_SMS_URL', plugin_dir_url(__FILE__));
define('LWS_SMS_DIR', __DIR__);
define('LWS_SMS_FILE', __FILE__);

// Remove all notices and popup while on the config page
add_action('admin_notices', function () {
    if (substr(get_current_screen()->id, 0, 30) == "toplevel_page_sms-api-settings") {
        remove_all_actions('admin_notices');
    }
}, 0);

/**
 * All hooks to be used to send SMS
 */
define('HOOKS', array(
    'completed',
    'processing',
    'cancelled',
    'coupon', 
    'sessions'
));

define('HOOKS_TRAD', array(
    __('Order completed', 'lws-sms'),
    __('Order processing', 'lws-sms'),
    __('Order cancelled', 'lws-sms'),
    __('New coupon', 'lws-sms'),
    __("Cart abandonned", "lws-sms")
));

define('ADMIN_HOOKS', array(
    'admin_new_order',
    'admin_dailies'
));

define('ADMIN_HOOKS_TRAD', array(
    __('New order', 'lws-sms'),
    __('Daily Logs', 'lws-sms')
));

define('HOOKS_BOTH_CATEGORIES', array(
    'completed',
    'processing',
    'cancelled',
    'coupon', 
    'sessions',
    'admin_new_order',
    'admin_dailies'
));

add_filter( 'cron_schedules', 'cron_add_five_minutes' );
function cron_add_five_minutes( $schedules ) {
    // Adds once weekly to the existing schedules.
    $schedules['two_min'] = array(
        'interval' => 120,
        'display' => esc_html__( 'Every 2 minutes' )
    );
    return $schedules;
}

if ( ! class_exists( 'CheckSender', false ) ) {
    include_once LWS_SMS_DIR . '/src/Cron/CheckSender.php';
}

if ( ! class_exists( 'CheckBalance', false ) ) {
    include_once LWS_SMS_DIR . '/src/Cron/CheckBalance.php';
}

if ( ! class_exists( 'DailyLogs', false ) ) {
    include_once LWS_SMS_DIR . '/src/Cron/DailyLogs.php';
}

CheckSender::init();
CheckBalance::init();
DailyLogs::init();

/**
 * On activation, activate a WP-Cron to check, every 5 minutes, if there is new senders
 */
register_activation_hook(__FILE__, 'lws_sms_on_activation');
function lws_sms_on_activation()
{
    set_transient('lwssms_remind_me', 2160000);        
    if (!wp_next_scheduled('update_senders')) {
        wp_schedule_event(time(), 'two_min', 'update_senders');
    }
}

/**
 * On deactivation, remove every crons
 */
register_deactivation_hook(__FILE__, 'lws_sms_cron_lws_off');
function lws_sms_cron_lws_off()
{
    wp_clear_scheduled_hook('update_senders');
    $args = array(wp_get_current_user(), get_option('lws_sms_alert'));
    wp_clear_scheduled_hook('send_mail_balance', $args);
    wp_clear_scheduled_hook('check_sessions_cron');
    wp_clear_scheduled_hook('daily_logs_cron');
}

/**
 * On uninstallation, delete all options
 */
register_uninstall_hook(__FILE__, "lws_sms_delete_LWS_options");
function lws_sms_delete_LWS_options()
{
    delete_option("lws_sender_id");
    delete_option("lws_user_sms");
    delete_option("lws_model_list");
    delete_option("lws_checked_options");
    delete_option("lws_checked_client");
    delete_user_meta(wp_get_current_user(), 'phone_sms');
    delete_user_meta(wp_get_current_user(), 'phone_sms_print');
    wp_clear_scheduled_hook('update_senders');
    $args = array(wp_get_current_user(), get_option('lws_sms_alert'));
    wp_clear_scheduled_hook('send_mail_balance', $args);
    wp_clear_scheduled_hook('check_sessions_cron');
    wp_clear_scheduled_hook('daily_logs_cron');
}

/**
 * Load all scripts and styles
 */
add_action('admin_enqueue_scripts', 'lws_sms_setting_up_scripts');
function lws_sms_setting_up_scripts()
{
    if (get_current_screen()->base == ('toplevel_page_sms-api-settings')) {
        wp_enqueue_style('config_css', plugins_url('css/config_page.css', __FILE__));
        wp_enqueue_style('dt_css', plugins_url('css/jquery.dataTables.min.css', __FILE__));
        wp_enqueue_style('dt_resp_css', plugins_url('css/responsive.dataTables.min.css', __FILE__));
        wp_enqueue_script('dt_js', plugins_url('js/jquery.dataTables.min.js', __FILE__));
        wp_enqueue_script('dt_resp_js', plugins_url('js/dataTables.responsive.min.js', __FILE__));
        wp_enqueue_style('lws_sms-Poppins', 'https://fonts.googleapis.com/css?family=Poppins');
    }
    else{
        wp_enqueue_style('lws_sms_css_out', LWS_SMS_URL . "css/lwssms_configpage_out.css");
        if (!get_transient('lwssms_remind_me') && !get_option('lwssms_do_not_ask_again')){
            add_action( 'admin_notices', 'lwssms_review_ad_plugin' );
        }
    }
}

function lwssms_review_ad_plugin(){
    ?>
    <script>
        function lwsst_remind_me(){
            var data = {                
                _ajax_nonce: '<?php echo esc_attr(wp_create_nonce('reminder_for_sms')); ?>',        
                action: "lws_sms_reminder_ajax",
                data: true,
            };
            jQuery.post(ajaxurl, data, function(response){
                jQuery("#lwssms_review_notice").addClass("animationFadeOut");
                setTimeout(() => {
                    jQuery("#lwssms_review_notice").addClass("lws_hidden");
                }, 800);    
            });

        }

        function lwsst_do_not_bother_me(){
            var data = {                
                _ajax_nonce: '<?php echo esc_attr(wp_create_nonce('donotask_for_sms')); ?>',        
                action: "lws_sms_donotask_ajax",
                data: true,
            };
            jQuery.post(ajaxurl, data, function(response){
                jQuery("#lwssms_review_notice").addClass("animationFadeOut");
                setTimeout(() => {
                    jQuery("#lwssms_review_notice").addClass("lws_hidden");
                }, 800);    
            });            
        }
    </script>

    <div class="notice notice-info is-dismissible lwssms_review_block_general" id="lwssms_review_notice">
        <div class="lwsst_circle">
            <img class="lwssms_review_block_image" src="<?php echo esc_url(plugins_url('images/plugin_lws-sms.svg', __FILE__))?>" width="40px" height="40px">
        </div>
        <div style="padding:16px">
            <h1 class="lwssms_review_block_title"> <?php esc_html_e('Thank you for using LWS SMS!', 'lws-sms'); ?></h1>
            <p class="lwssms_review_block_desc"><?php _e('Evaluate our plugin to help others send SMS with their WooCommerce shop!', 'lws-sms' ); ?></p>
            <a class="lwssms_button_rate_plugin" href="https://wordpress.org/support/plugin/lws-sms/reviews/" target="_blank" ><img style="margin-right: 8px;" src="<?php echo esc_url(plugins_url('images/noter.svg', __FILE__))?>" width="15px" height="15px"><?php esc_html_e('Rate', 'lws-sms'); ?></a>
            <a class="lwssms_review_button_secondary" onclick="lwsst_remind_me()"><?php esc_html_e('Remind me later', 'lws-sms'); ?></a>
            <a class="lwssms_review_button_secondary" onclick="lwsst_do_not_bother_me()"><?php esc_html_e('Do not ask again', 'lws-sms'); ?></a>
        </div>
    </div>
    <?php
}

//AJAX Reminder//
add_action("wp_ajax_lws_sms_reminder_ajax", "lws_sms_remind_me_later");
function lws_sms_remind_me_later(){
    check_ajax_referer('reminder_for_sms', '_ajax_nonce');
    if (isset($_POST['data'])){
        set_transient('lwssms_remind_me', 2160000);        
    }
}

//AJAX Reminder//
add_action("wp_ajax_lws_sms_donotask_ajax", "lws_sms_do_not_ask");
function lws_sms_do_not_ask(){
    check_ajax_referer('donotask_for_sms', '_ajax_nonce');
    if (isset($_POST['data'])){
        update_option('lwssms_do_not_ask_again', true);        
    }
}



/**
 * Load translations and classes on init
 */
add_action('init', 'lws_sms_on_init');
function lws_sms_on_init()
{
    load_plugin_textdomain('lws-sms', false, dirname(plugin_basename(__FILE__)) . '/languages');
    if ( ! class_exists( 'ApiCalls', false ) ) {
        include_once LWS_SMS_DIR . '/src/API/ApiCalls.php';
    }
}

/**
 * Create the configuration page for LWS SMS
 */
add_action('admin_menu', 'lws_sms_add_menu');
function lws_sms_add_menu()
{
    add_menu_page(__('LWS SMS Settings', 'lws-sms'), 'LWS SMS', 'manage_options', 'sms-api-settings', 'lws_sms_config', plugins_url('/images/plugin_lws_sms.svg', __FILE__), 58);
}

function lws_sms_config()
{
    global $wpdb;
    if ( ! class_exists( 'ApiCalls', false ) ) {
        include_once LWS_SMS_DIR . '/src/API/ApiCalls.php';
    }

    /**
     * Fetch the credentials and attempts to connect to the API and fetch all data
     */
    if (isset($_POST['validate_connexion']) && isset($_POST['username']) && isset($_POST['APIKey'])) {
        $username = sanitize_text_field($_POST['username']);
        $apikey = sanitize_text_field($_POST['APIKey']);
        $data = ApiCalls::apiInfo($username, $apikey);
        if (is_wp_error($data)){
            $formError = true;
            $formErrorMessage = $data->get_error_message();
        } else{
            if ($data['code'] === '100') {
                $sender = array();
                if (empty($data['list_senderid'])){
                    $sender[] = "NOSENDER";
                }
                else{
                    foreach ($data['list_senderid'] as $s) {
                        $sender[$s['id']] = $s['sender_id'];
                    }
                }
                
                update_option("lws_sender_id", $sender);
                update_option("lws_user_sms", array(
                        'username' => $data['username'],
                        'api_key' => $data['api_key_client'],
                        'id_client' => $data['id_client']
                    )
                );

                if (!get_option('lws_sms_alert')) {
                    update_option("lws_sms_alert", 1);
                }
            }
            else {
                $formError = true;
                switch ($data['code']) {
                    case '101':
                        $formErrorMessage = __("Invalid username or API Key", "lws-sms");
                        break;
                    case '102':
                        $formErrorMessage = __("Please fill in your username", "lws-sms");
                        break;
                    case '103':
                        $formErrorMessage = __("Please fill in your API Key", "lws-sms");
                        break;
                    default:
                        $formErrorMessage = __("Something went wrong with our API", "lws-sms");
                        break;
                }
            }
        }
    }

    /**
     * If disconnecting, delete credentials
     */
    if (isset($_POST['disconnect_user'])) {
        delete_option("lws_sender_id");
        delete_option("lws_user_sms");
        delete_option("lws_sms_alert");
    }

    /**
     * If ApiKey is wrong, inform the user
     */
    $check = ApiCalls::apiCheckIsValid();
    if (is_bool($check)){
        if (get_option("lws_user_sms") && get_option("lws_sender_id") && !ApiCalls::apiCheckIsValid()) {
            include __DIR__ . "/view/alert/apikey_wrong.php";
        }
    }

    if ((get_option("lws_sender_id")) && (get_option("lws_user_sms")) && is_plugin_active('woocommerce/woocommerce.php')) {

    /**
     * GENERAL PAGE
     */
        if (isset($_POST['change_alert_sms'])) {
            $args_delete = array(wp_get_current_user(), get_option('lws_sms_alert'));
            wp_clear_scheduled_hook('send_mail_balance', $args_delete);
            update_option("lws_sms_alert", sanitize_text_field($_POST['alert_sms']));
            $args = array(wp_get_current_user(), sanitize_text_field($_POST['alert_sms']));
            if (!wp_next_scheduled('send_mail_balance', $args_delete) && !wp_next_scheduled('send_mail_balance', $args)) {
                wp_schedule_event(time(), 'daily', 'send_mail_balance', $args);
            }
        }

        $SMSBalance = ApiCalls::apiGetBalance();
        if (!is_numeric($SMSBalance)){
            $SMSBalance = 0;
        }
        $sender_ids = get_option("lws_sender_id");
        $alert_sms = get_option('lws_sms_alert');
    /**
     * TEMPLATES PAGE
     */
        $model_error = false;
        $edit_model = false;

        /**
         * Add another template to the list
         */
        if (isset($_POST['validate_model'])) {
            if (!empty(sanitize_text_field($_POST['model_name'])) && !empty(sanitize_textarea_field($_POST['sms_model']))) {
                $already_done = false;
                if (!$models = get_option('lws_model_list')) {
                    $models[] = array(
                        'id' => uniqid('', true),
                        'name' => sanitize_text_field($_POST['model_name']),
                        'sender' => sanitize_text_field($_POST['senders_select']),
                        'message' => sanitize_textarea_field($_POST['sms_model']),
                        'nb_sms' => sanitize_text_field($_POST['number_message']),
                        'in_use' => array('NO')
                    );
                    update_option('lws_model_list', $models);
                    $already_done = true;
                } else {
                    foreach ($models as $key => $value) {
                        if ($models[$key]['name'] == sanitize_text_field($_POST['model_name'])){
                            if (!isset($_POST['editing'])){
                                $model_error = true;
                                $model_error_message = __("This model already exist, choose another name", "lws-sms");
                            }
                            else if (isset($_POST['editing']) && $models[$key]['id'] != sanitize_text_field($_POST['model_id'])){
                                $model_error = true;
                                $model_error_message = __("This model already exist, choose another name", "lws-sms");
                            }
                        }
                    }
                }

                if (!$model_error && !$already_done) {
                    if (!isset($_POST['editing'])){
                        $models[] = array(
                            'id' => uniqid('', true),
                            'name' => sanitize_text_field($_POST['model_name']),
                            'sender' => sanitize_text_field($_POST['senders_select']),
                            'message' => sanitize_textarea_field($_POST['sms_model']),
                            'nb_sms' => sanitize_textarea_field($_POST['number_message']),
                            'in_use' => array('NO'));
                        update_option('lws_model_list', $models);
                    }
                    else{
                        $model_key = sanitize_text_field($_POST['model_key']);                        
                        $models[$model_key] = array(
                            'id' => $models[$model_key]['id'],
                            'name' => sanitize_text_field($_POST['model_name']),
                            'sender' => sanitize_text_field($_POST['senders_select']),
                            'message' => sanitize_textarea_field($_POST['sms_model']),
                            'nb_sms' => sanitize_textarea_field($_POST['number_message']),
                            'in_use' => $models[$model_key]['in_use']);
                        update_option('lws_model_list', $models);
                    }
                }
            }

            $change_tab = 'nav-templates';
        }
                
        /**
         * Re-fetch the templates
         */
        $models = get_option('lws_model_list');

        /**
         * Launch editing mode for templates
         */
        if (isset($_POST['edit_model'])) {
            foreach ($models as $key => $model) {
                if ($model['name'] == sanitize_text_field($_POST['model_name'])) {
                    $edit_model = $model;
                    $edit_key = $key;
                    $edit_id = $model['id'];
                }
            }
            $change_tab = 'nav-templates';
        }

        /**
         * Remove a template from the list
         */
        if (isset($_POST['delete_model'])) {
            $checked_options = get_option('lws_checked_options');
            if (!$checked_options){
                $checked_options = array();
            }
            foreach ($models as $key => $value) {
                if ($models[$key]['name'] == sanitize_text_field($_POST['model_name'])) {
                    foreach ($models[$key]['in_use'] as $usage){
                        foreach (HOOKS_BOTH_CATEGORIES as $hook) {
                            if ($hook == $usage){
                                $checked_options[$hook]['is_checked'] = false;
                                $checked_options[$hook]['used'] = 'NO';
                            }
                        }
                    }
                    update_option('lws_checked_options', $checked_options);
                    unset($models[$key]);                    
                    break;
                }
            }
            update_option('lws_model_list', $models);            
            $change_tab = 'nav-templates';
        }
         
    /**
     * SENDER PAGE
     */

        /**
         * Add a sender to the list for the current user
         */
        if (isset($_POST['new_sender']) && !empty($_POST['sender_name'])) {
            $sender = sanitize_text_field($_POST['sender_name']);
            // Max 11 chars, alphanumerical and at least a letter (and not empty, ofc)
            if (ApiCalls::apiAddSender($sender) == "100") {
                $is_added = true;
                $add_message = __("Your request for a Sender ID has been acknowledged. Please wait for an operator to validate it.", "lws-sms");
            } else {
                $is_added = false;
                $add_message = __("Your Sender ID must only contain alphanumerical characters and at least 1 letter.", "lws-sms");
            }
            $change_tab = 'nav-sender';
        }

    /**
     * SMS HISTORY PAGE
     */

        //Get every SMS (up to 5000)
        $list_history = ApiCalls::apiSMSHistory();
        if ($list_history instanceof WP_Error){
            error_log($list_history);
            $list_history = array();            
        }
        $users = array();

        //Get every users with a phone_sms field filled
        foreach (get_users(array('meta_query' => array(array('key' => 'phone_sms')))) as $user) {
            $users[$user->display_name] =  get_user_meta($user->ID, 'phone_sms', true);
        }

    /**
     * AUTOMATION PAGE
     */

        $already_on = false;
        $user = wp_get_current_user();
        $sender_model = "";
        $timer_cron = get_option("lws_timer_cron");
                
        //Getting all checkboxes status
        if (!$checked_options = get_option('lws_checked_options')) {
            $checked_options = array();
            foreach (HOOKS_BOTH_CATEGORIES as $hook) {
                $checked_options[$hook]['is_checked'] = false;
                $checked_options[$hook]['used'] = 'NO';
            }
            update_option('lws_checked_options', $checked_options);
        }
        
        //Submitted form phone SMS
        if (isset($_POST['update_phone_sms'])) {
            $user = wp_get_current_user();
            if (isset($_POST['phone_sms']) && strlen(sanitize_text_field($_POST['phone_sms'])) > 1 && is_numeric(sanitize_text_field($_POST['phone_sms']))) {
                $num = sanitize_text_field($_POST['phone_sms']);
                if (substr($num, 0, 1) == "0" && strlen($num) == 10) {
                    $num = substr($num, 1);
                }
                $num = sanitize_text_field($_POST['countryCode']) . $num;
                update_user_meta($user->ID, 'phone_sms', $num);
                update_user_meta($user->ID, 'phone_sms_print', sanitize_text_field($_POST['phone_sms']));
            } else {
                delete_user_meta($user->ID, 'phone_sms');
                delete_user_meta($user->ID, 'phone_sms_print');
            }
            $update_sms = true;
            $change_tab = 'nav-automatisation';
        }
        
    /**
     * CAMPAIGN PAGE
     */

        $no_balance = false;
        $sms_sent = -1;
        $empty_template = false;
        $client_filter_country = array();
        $clients_SMS = array();
        $returning = array();
        $month_old = array();
        $ten_month_old = array();
            
        $clients_ok = array();
        if (!$clients = get_option('lws_ads_clients')) {
            $clients = array();
            update_option('lws_ads_clients', $clients);
        }
            
        foreach ($clients as $id => $value) {
            if ($value) {
                $clients_ok[] = $id;
            }
        }

        if (!$models = get_option('lws_model_list')) {
            $models = array();
            update_option('lws_model_list', $models);
        }
                    
        //Get country of every customers and their registered time (if < 1 month) for later use
        $customer_lookup = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "wc_customer_lookup");
        foreach ($customer_lookup as $customer) {
            if ($customer->customer_id === null || $customer->date_registered === null){
                continue;
            }
            $client_filter_country[$customer->customer_id] = $customer->country;
            if (time() - strtotime($customer->date_registered) <= 2629743) {
                $month_old[] = $customer->customer_id;
            }
            if (time() - strtotime($customer->date_registered) >= 26297430) {
                $ten_month_old[] = $customer->customer_id;
            }
        }
                
        //Get returning customers (having already ordered once)
        $order_stats = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "wc_order_stats");
        foreach ($order_stats as $order) {
            if ($order->returning_customer) {
                $returning[] = $order->customer_id;
            }
        }
                    
        //Filters for client >  1 month and returning and > 10 months
        $client_filter_registered_1_month = array();
        $client_filter_registered_10_months = array();
        $client_filter_returning = array();
        foreach ($clients_ok as $client) {
            foreach ($month_old as $filter) {
                if ($filter == $client) {
                    $client_filter_registered_1_month[] = esc_html($client);
                    break;
                }
            }
            foreach ($ten_month_old as $filter) {
                if ($filter == $client) {
                    $client_filter_registered_10_months[] = esc_html($client);
                    break;
                }
            }
                
            foreach ($returning as $return) {
                if ($return == $client) {
                    $client_filter_returning[] = esc_html($client);
                    break;
                }
            }
        }
        
        /**
         * Get message, sender and number of SMS to be sent whether user is
         * using a template or not
         * Also fetch all filters, if used
         */
        $message = "";
        $sender = "";
        $nb_sms = 0;
        if (isset($_POST['send_sms_market_model']) || isset($_POST['send_sms_market'])){
            switch(sanitize_text_field($_POST['filter'])) {
                case "country":
                    foreach ($client_filter_country as $client => $value) {
                        if (sanitize_text_field($_POST['country_value']) == $value) {
                            $clients_SMS[] = $client;
                        }
                    }
                    break;
                case "return":
                    $clients_SMS = $client_filter_returning;
                    break;
                case "month_old":
                    $clients_SMS = $client_filter_registered_1_month;
                    break;
                case "month_ten":
                    $clients_SMS = $client_filter_registered_10_months;
                    break;
                default:
                    $clients_SMS = $clients_ok;
                    break;
            }

            if (isset($_POST['send_sms_market_model'])) {
                if (sanitize_text_field($_POST['models']) != "NO") {
                    foreach ($models as $model) {
                        if ($model['name'] == sanitize_text_field($_POST['models'])) {
                            $message = $model['message'];
                            $sender = $model['sender'];
                            $nb_sms = $model['nb_sms'] * count($clients_SMS);
                        }
                    }
                }

            }
            elseif(isset($_POST['send_sms_market'])){
                $message = sanitize_text_field($_POST['marketing_model']);
                $sender = sanitize_text_field($_POST['senders']);
                $nb_sms = sanitize_text_field($_POST['number_message_campaign']) * count($clients_SMS);
            }

            $sms_sent = 0;
            /**
             * Send a SMS using the API
             * Will send as many SMS as there are clients in the list, but only if there is enough credits
             */
            $balance = ApiCalls::apiGetBalance();
            if (!is_numeric($balance)){
                $balance = 0;
            }
            if ($balance - $nb_sms >= 0) {
                foreach ($clients_SMS as $client) {
                    $user = get_user_by('id', $client);
                    if ($user->phone_sms) {
                        $message = str_replace("[[Nom]]", $user->last_name, $message);
                        $message = str_replace("[[Prenom]]", $user->first_name, $message);
                        $message = str_replace(
                            "[[Adresse]]",
                            $user->shipping_address_1 . ', ' .
                            $user->shipping_address_2 . ' ' .
                            $user->shipping_city      . ', ' .
                            $user->shipping_postcode     . ' ' .
                            $user->shipping_country,
                            $message
                        );
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
                        $message = str_replace("[[PanierAbandonnes]]", "", $message);
                        $message = str_replace("[[MontantTotal]]", "", $message);
                        $message = str_replace("[[Commandes]]", "", $message);
                        $message = str_replace("[[NouveauxClients]]", "", $message);
                        $message = str_replace("[[MeilleureVente]]", "", $message);

                        ApiCalls::apiSendSMS($sender, $user->phone_sms, $message);
                        $sms_sent += 1;                                    
                    }
                }
                $SMSBalance -+ $sms_sent;
                $sms_sent = true;
            }
            else {
                $no_balance = true;
            } 
            $change_tab = 'nav-campaign';            
        }                                     
        $model_list = wp_json_encode($models, JSON_PRETTY_PRINT);
    }

    //Launch the basic layout for the settings
    include __DIR__ . '/view/tabs.php';
}

/**
 * Check whether $content is containing UNICODE characters or not
 * @return boolean whether there is UNICODE or not
 */
function lws_sms_requiresUnicodeEncoding($content)
{
    $gsmCodePoints = array_map(
        lws_sms_convertIntoUnicode(),
        [
        '@', '£', '$', '¥', 'è', 'é', 'ù', 'ì', 'ò', 'ç', "\r", 'Ø', 'ø', "\n", 'Å', 'å',
        'Δ', '_', 'Φ', 'Γ', 'Λ', 'Ω', 'Π', 'Ψ', 'Σ', 'Θ', 'Ξ', 'Æ', 'æ', 'ß', 'É',
        ' ', '!', '"', '#', '¤', '%', '&', '\'', '(', ')', '*', '+', ',', '-', '.', '/',
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ':', ';', '<', '=', '>', '?',
        '¡', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O',
        'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Ä', 'Ö', 'Ñ', 'Ü', '§',
        '¿', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o',
        'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'ä', 'ö', 'ñ', 'ü', 'à',
        "\f", '^', '{', '}', '\\', '[', '~', ']', '|', '€',
        ]
    );

    // Split $text into an array in a way that respects multibyte characters.
    $textChars = preg_split('//u', $content, -1, PREG_SPLIT_NO_EMPTY);

    // Array of codepoint values for characters in $text.
    $textCodePoints = array_map(lws_sms_convertIntoUnicode(), $textChars);

    // Filter the array to contain only codepoints from $text that are not in the set of valid GSM codepoints.
    $nonGsmCodePoints = array_diff($textCodePoints, $gsmCodePoints);

    // The text contains unicode if the result is not empty.
    return !empty($nonGsmCodePoints);
}

function lws_sms_convertIntoUnicode()
{
    return function ($char) {
        $k = mb_convert_encoding($char, 'UTF-16LE', 'UTF-8');
        $k1 = ord(substr($k, 0, 1));
        $k2 = ord(substr($k, 1, 1));

        return $k2 * 256 + $k1;
    };
}

//AJAX DL Plugin//
add_action("wp_ajax_lws_sms_downloadPlugin", "wp_ajax_install_plugin");
//

//AJAX Activate Plugin//
add_action("wp_ajax_lws_sms_activatePlugin", "lws_sms_activate_plugin");
function lws_sms_activate_plugin()
{
    check_ajax_referer('activate_lws_sms_plugins', '_ajax_nonce');
    if (isset($_POST['ajax_slug'])) {
        switch (sanitize_textarea_field($_POST['ajax_slug'])) {
            case 'lws-hide-login':
                activate_plugin('lws-hide-login/lws-hide-login.php');
                break;
            case 'lws-sms':
                activate_plugin('lws-sms/lws-sms.php');
                break;
            case 'lws-tools':
                activate_plugin('lws-tools/lws-tools.php');
                break;
            case 'lws-affiliation':
                activate_plugin('lws-affiliation/lws-affiliation.php');
                break;
            case 'lws-cleaner':
                activate_plugin('lws-cleaner/lws-cleaner.php');
                break;
            case 'lwscache':
                activate_plugin('lwscache/lwscache.php');
                break;
            case 'lws-optimize':
                activate_plugin('lws-optimize/lws-optimize.php');
                break;
            case 'lws-migrator':
                activate_plugin('lws-migrator/lws-migrator.php');
                break;
        }
    }
    wp_die();
}

/**
 * AJAX using the aforementionned Unicode function and echo the response
 */
add_action('wp_ajax_unicodeChecker', 'lws_sms_ajax_unicode');
function lws_sms_ajax_unicode()
{
    check_ajax_referer('check_unicode_lws_sms', '_ajax_nonce');
    if(isset($_POST['ajax_text'])) {
        $text = sanitize_textarea_field($_POST['ajax_text']);
        echo esc_html(lws_sms_requiresUnicodeEncoding($text));
    }
    wp_die();
}

/**
 * Update the database with the latest changes in regards to SMS sending
 */
add_action('wp_ajax_automation_update', 'lws_sms_ajax_automation');
function lws_sms_ajax_automation()
{
    check_ajax_referer('update_automation_lws_sms', '_ajax_nonce');
    if (!$models = get_option('lws_model_list')){
        wp_die("Error, no templates | Failed");
    }
    $already_on = false;
    $checked_options = get_option('lws_checked_options');
    foreach ($_POST as $P) {
        $POST[] = sanitize_text_field($P);
    }

    $hook_info = explode("|", $POST[1]);
    $hook = $hook_info[0];
    if (substr($hook, 0, 5) == "admin") {
        $id_hook = "A|" . $hook_info[1];
    } else {
        $id_hook = $hook_info[1];
    }

    //When submitted ()
    if ($POST[3] == "true" && $POST[2] != "NO") {
        $select = $POST[2];
        foreach ($models as $key => $model) {
            //Stock the choosen model for later
            if ($model['name'] == $select) {
                $model_active = $model;
                $model_active_key = $key;
                //If for some reason there is nothing in ['in_use'],
                //create a new entry with 'NO'
                if (count($model['in_use']) == 0) {
                    $model['in_use'][] = 'NO';
                }
            }
            foreach($model['in_use'] as $key2 => $used) {
                //Check every models and delete any tags already on
                if ($used == $hook) {
                    if (count($model['in_use']) == 1) {
                        $models[$key]['in_use'][$key2] = 'NO';
                    } else {
                        unset($models[$key]['in_use'][$key2]);
                    }
                }
            }
        }
        //Take the saved model and check his tags
        foreach ($model_active['in_use'] as $key2 => $used) {
            //Delete any 'NO' tags
            if ($used == 'NO') {
                unset($models[$model_active_key]['in_use'][$key2]);
            }
            //Add the tag if it is not already set
            if (!$already_on) {
                $models[$model_active_key]['in_use'][$id_hook] = $hook;
                $already_on = true;
                update_option("lws_model_list", $models);
            }
        }
        //Update the checkboxes checker to save the new values
        $checked_options[$hook]['is_checked'] = true;
        $checked_options[$hook]['used'] = $model_active['id'];
        if ($hook == 'sessions' && ($POST[4] != "NO")) {
            wp_clear_scheduled_hook('check_sessions_cron');
            if (!wp_next_scheduled('check_sessions_cron') && $POST[4] != "NO") {
                update_option("lws_timer_cron", $POST[4]);
                $rep = wp_schedule_event(time(), "twicedaily", 'check_sessions_cron');
            }
        }
        if ($hook == 'dailies') {
            wp_clear_scheduled_hook('daily_logs_cron');
            if (!wp_next_scheduled('daily_logs_cron')) {
                $rep = wp_schedule_event(time(), "daily", 'daily_logs_cron');
            }
        }
        //1 == checkbox activée
        echo esc_html(1);
    }

    //If the checkbox is unchecked, update the checker
    else {
        $checked_options[$hook]['is_checked'] = false;
        $checked_options[$hook]['used'] = 'NO';
        if ($hook == 'sessions') {
            $rep = wp_clear_scheduled_hook('check_sessions_cron');
            delete_option("lws_timer_cron");
        }
        if ($hook == 'dailies') {
            wp_clear_scheduled_hook('daily_logs_cron');
        }

        foreach ($models as $key => $model) {
            foreach ($model['in_use'] as $key2 => $used) {
                if ($used == $hook) {
                    if (count($model['in_use']) <= 1) {
                        unset($models[$key]['in_use'][$id_hook]);
                        $models[$key]['in_use'][] = "NO";
                    } else {
                        unset($models[$key]['in_use'][$id_hook]);
                    }
                    break;
                }
            }
            update_option("lws_model_list", $models);
            echo esc_html(-1);
            break;
        }
    }

    update_option('lws_checked_options', $checked_options);
    wp_die();
}

//WOOCOMMERCE//
//ADD EVERY FIELDS NECESSARY IN WOOCOMMERCE PAGES//

//Add fields to the My Account edition form of WC
add_action("woocommerce_edit_account_form", "lws_sms_add_fields");
function lws_sms_add_fields()
{
    $user = wp_get_current_user();
    ?>
<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
    <label
        for="phone_sms"><?php esc_html_e('Phone number', "lws-sms"); ?></label>
    <?php include __DIR__ . "/php/select_country.php";?>
    <input required class="woocommerce-Input woocommerce-Input--text input-text input_phone_sms" type="tel"
        id="phone_sms" name="phone_sms"
        value="<?php echo esc_attr($user->phone_sms_print);?>">
    <br>
    <small
        style="font-size: 11px;"><?php esc_html_e('Allow access to SMS marketing such as alerts when your order is delivered', "lws-sms"); ?>
        <br>
        <?php esc_html_e('Do not add the country code to your number.', "lws-sms");?></small>
    <br>
    <?php $okay_ad = get_option('lws_ads_clients');
    if (!$okay_ad) {
        $okay_ad = array($user->ID => false);
        update_option('lws_ads_clients', $okay_ad, true);
    }
    ?>
    <br>
    <label
        for="checkboxes_ads"><?php esc_html_e("I agree to receive marketing communications by SMS", "lws-sms")?>
        <input type="checkbox" id="checkboxes_ads" name="checkboxes_ads"
            <?php echo $okay_ad[$user->ID] ? esc_attr('checked') : '';?>>
    </label>

    <script>
        document.querySelector("#checkboxes_ads").addEventListener("change", function() {
            if (this.checked) {
                jQuery("#phone_sms").prop("required", true);
            } else {
                jQuery("#phone_sms").prop("required", false);
            }
        });
    </script>
</p>
<?php
}

//Save the SMS Phone Field from the My Account edition form of WC
add_action('woocommerce_save_account_details', 'lws_sms_save_phone_number');
function lws_sms_save_phone_number($user_id)
{
    update_user_meta($user_id, 'checkboxes', array());
    if (isset($_POST['phone_sms']) && strlen((sanitize_text_field($_POST['phone_sms']))) > 1 && is_numeric((sanitize_text_field($_POST['phone_sms'])))) {
        $num = (sanitize_text_field($_POST['phone_sms']));
        if (substr($num, 0, 1) == "0" && strlen($num) == 10) {
            $num = substr($num, 1);
        }
        $num = sanitize_text_field($_POST['countryCode']) . $num;
        $client_ok_ads = get_option('lws_ads_clients');
        if (!$client_ok_ads) {
            $client_ok_ads = array($user_id => false);
            update_option('lws_ads_clients', $client_ok_ads, true);
        }
        if (sanitize_text_field($_POST['checkboxes_ads']) == 'on') {
            $client_ok_ads[$user_id] = true;
            update_option('lws_ads_clients', $client_ok_ads, true);
        } else {
            $client_ok_ads[$user_id] = false;
            update_option('lws_ads_clients', $client_ok_ads, true);
        }

        update_user_meta($user_id, 'phone_sms', $num);
        update_user_meta($user_id, 'phone_sms_print', sanitize_text_field($_POST['phone_sms']));
    } else {
        delete_user_meta($user_id, 'phone_sms');
        delete_user_meta($user_id, 'phone_sms_print');
    }
}

//Add a SMS Phone Field to the Register form of WC
add_action('woocommerce_register_form', 'lws_sms_add_phone_number_to_register');
function lws_sms_add_phone_number_to_register()
{
    ?>
<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
    <label
        for="phone_sms"><?php esc_html_e('Phone number', "lws-sms"); ?></label>
    <?php include __DIR__ . "/php/select_country.php";?>
    <input required class="woocommerce-Input woocommerce-Input--text input-text input_phone_sms" type="tel"
        id="phone_sms" name="phone_sms" value="">
    <br>
    <small
        style="font-size: 11px;"><?php esc_html_e('Allow access to SMS marketing such as alerts when your order is delivered', "lws-sms"); ?>
        <br>
        <?php esc_html_e('Do not add the country code to your number.', "lws-sms");?></small>
    <br>
    <label
        for="checkboxes_ads"><?php esc_html_e("I agree to receive marketing communications by SMS", "lws-sms")?>
        <input type="checkbox" id="checkboxes_ads" name="checkboxes_ads" checked>
    </label>

    <script>
        document.querySelector("#checkboxes_ads").addEventListener("change", function() {
            if (this.checked) {
                jQuery("#phone_sms").prop("required", true);
            } else {
                jQuery("#phone_sms").prop("required", false);
            }
        });
    </script>
</p>
<?php
}

//Save the SMS Phone Field of the new created user
add_action('woocommerce_created_customer', 'lws_sms_add_phone_sms_customer');
function lws_sms_add_phone_sms_customer($customer_id)
{
    update_user_meta($customer_id, 'checkboxes', array());
    if (isset($_POST['phone_sms']) && strlen(sanitize_text_field($_POST['phone_sms'])) > 1 && is_numeric(sanitize_text_field($_POST['phone_sms']))) {
        $num = sanitize_text_field($_POST['phone_sms']);
        if (substr($num, 0, 1) == "0" && strlen($num) == 10) {
            $num = substr($num, 1);
        }
        $num = sanitize_text_field($_POST['countryCode']) . $num;
        $client_ok_ads = get_option('lws_ads_clients');
        if (!$client_ok_ads) {
            $client_ok_ads = array($customer_id => false);
            update_option('lws_ads_clients', $client_ok_ads, true);
        }
        if (sanitize_text_field($_POST['checkboxes_ads']) == 'on') {
            $client_ok_ads[$customer_id] = true;
            update_option('lws_ads_clients', $client_ok_ads, true);
        } else {
            $client_ok_ads[$customer_id] = false;
            update_option('lws_ads_clients', $client_ok_ads, true);
        }
        update_user_meta($customer_id, 'phone_sms', $num);
        update_user_meta($customer_id, 'phone_sms_print', sanitize_text_field($_POST['phone_sms']));
    }
}

//Add the usual field to the Register form in the checkout page
add_action('woocommerce_after_checkout_registration_form', 'lws_sms_register_checkout_phone_sms');
function lws_sms_register_checkout_phone_sms()
{
    ?>
<p id="phone_sms_checkout" class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide"></p>
<script>
    if (!!document.getElementById("createaccount")) {
        var check = document.getElementById("createaccount")
        check.addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('phone_sms_checkout').innerHTML = `
                <label for="phone_sms"><?php esc_html_e('Phone number', "lws-sms"); ?></label> 
                <?php include __DIR__ . "/php/select_country.php";?>
                <input required class="woocommerce-Input woocommerce-Input--text input-text input_phone_sms" type="tel" id="phone_sms" name="phone_sms" value="">
                <br>
                <small style="font-size: 11px;"><?php esc_html_e('Allow access to SMS marketing such as alerts when your order is delivered', "lws-sms"); ?>
                <br>
                <?php esc_html_e('Do not add the country code to your number.', "lws-sms");?></small> <br>                
                <label for="checkboxes_ads"><?php esc_html_e("I agree to receive marketing communications by SMS", "lws-sms")?>
                <input type="checkbox" id="checkboxes_ads"  name="checkboxes_ads" checked>
                </label>
                <script>
                    document.querySelector("#checkboxes_ads").addEventListener("change", function() {
                        if (this.checked){
                            jQuery("#phone_sms").prop("required", true);
                        }
                        else{
                            jQuery("#phone_sms").prop("required", false);
                        }
                    });<//script>`
            } else {
                document.getElementById('phone_sms_checkout').innerHTML = ""
            }
        })
    } else {
        document.getElementById('phone_sms_checkout').innerHTML = `
            <label for="phone_sms"><?php esc_html_e('Phone number', "lws-sms"); ?></label> 
            <?php include __DIR__ . "/php/select_country.php";?>
            <input required class="woocommerce-Input woocommerce-Input--text input-text input_phone_sms" type="tel" id="phone_sms" name="phone_sms" value="">
            <br>
            <small style="font-size: 11px;"><?php esc_html_e('Allow access to SMS marketing such as alerts when your order is delivered', "lws-sms");?>
            <br> 
            <?php esc_html_e('Do not add the country code to your number.', "lws-sms");?></small> <br>
            <label for="checkboxes_ads"><?php esc_html_e("I agree to receive marketing communications by SMS", "lws-sms")?>
            <input type="checkbox" id="checkboxes_ads"  name="checkboxes_ads" checked>
            </label>
            <script>
                document.querySelector("#checkboxes_ads").addEventListener("change", function() {
                    if (this.checked){
                        jQuery("#phone_sms").prop("required", true);
                    }
                    else{
                        jQuery("#phone_sms").prop("required", false);
                    }
                });
            <//script>
        `
    }
</script>
<?php
}

//Save the fields when the user is created
add_action('user_register', 'lws_sms_myplugin_registration_save', 10, 1);
function lws_sms_myplugin_registration_save($user_id)
{
    if (isset($_POST['phone_sms']) && strlen(sanitize_text_field($_POST['phone_sms'])) > 1 && is_numeric(sanitize_text_field($_POST['phone_sms']))) {
        $num = sanitize_text_field($_POST['phone_sms']);
        if (substr($num, 0, 1) == "0" && strlen($num) == 10) {
            $num = substr($num, 1);
        }
        $num = sanitize_text_field($_POST['countryCode']) . $num;
        update_user_meta($user_id, 'phone_sms', $num);
        update_user_meta($user_id, 'phone_sms_print', sanitize_text_field($_POST['phone_sms']));
        //Getting all checkboxes status, creating it if needed of fetching it for the client side
        $checked_options_client = array();
        if (!get_option('lws_checked_options_client')) {
            foreach (HOOKS as $hook) {
                $checked_options_client[$hook]['is_checked'] = true;
            }
            update_option('lws_checked_options_client', $checked_options_client);
        }
        $client_ok_ads = get_option('lws_ads_clients');
        if (!$client_ok_ads) {
            $client_ok_ads = array($user_id => false);
            update_option('lws_ads_clients', $client_ok_ads, true);
        }
        if (sanitize_text_field($_POST['checkboxes_ads']) == 'on') {
            $client_ok_ads[$user_id] = true;
            update_option('lws_ads_clients', $client_ok_ads, true);
        } else {
            $client_ok_ads[$user_id] = false;
            update_option('lws_ads_clients', $client_ok_ads, true);
        }
    }
}

//END FIELDS//

//SMS SCENARIOS//

//Main function of the segment. Send a SMS with the given parameters
function lws_sms_send_SMS($user, $usage, $num, $coupon = null, $product = null, $order = null)
{

    if ( ! class_exists( 'ApiCalls', false ) ) {
        include_once LWS_SMS_DIR . '/src/API/ApiCalls.php';
    }

    $models = get_option('lws_model_list');
    $model = null;
    foreach ($models as $m){
        if ($m['in_use'][array_search($usage, $m['in_use'])] === $usage){
            $model = $m;
            break;
        }
    }

    if ($model === null) { wp_die("No template | Failed");}
    $balance = ApiCalls::apiGetBalance();
    if (!is_numeric($balance)){
        $balance = 0;
    }
    if ($balance - $model['nb_sms'] >= 0) {
        $message = $model['message'];
        $message = str_replace("[[Nom]]", $user->last_name, $message);
        $message = str_replace("[[Prenom]]", $user->first_name, $message);
        if ($order) {
            $message = str_replace("[[Prix]]", $order->price, $message);
            $message = str_replace("[[NumCmde]]", $order->ID, $message);
        } else {
            $message = str_replace("[[Prix]]", "", $message);
            $message = str_replace("[[NumCmde]]", "", $message);
        }

        $message = str_replace("[[Adresse]]",
            $user->shipping_address_1 . ', ' . $user->shipping_address_2 . ' ' . $user->shipping_city . ', ' .
            $user->shipping_postcode . ' ' . $user->shipping_country,
            $message);

        if ($coupon) {
            $message = str_replace("[[CodeCoupon]]", $coupon->code, $message);
            $message = str_replace("[[DescriptionCoupon]]", $coupon->get_description(), $message);
            $message = str_replace("[[ValeurCoupon]]", $coupon->amount, $message);
        } else {
            $message = str_replace("[[CodeCoupon]]", "", $message);
            $message = str_replace("[[DescriptionCoupon]]", "", $message);
            $message = str_replace("[[ValeurCoupon]]", "", $message);
        }
            
        if ($product) {
            $message = str_replace("[[NomProduit]]", $product->name, $message);
            $message = str_replace("[[PrixProduit]]", $product->price, $message);
            $message = str_replace("[[DescriptionProduit]]", $product->description, $message);
            $message = str_replace("[[ListePanier]]", "", $message);
        } else {
            $message = str_replace("[[NomProduit]]", "", $message);
            $message = str_replace("[[PrixProduit]]", "", $message);
            $message = str_replace("[[DescriptionProduit]]", "", $message);
            $message = str_replace("[[ListePanier]]", "", $message);
        }
            
        $message = str_replace("[[PanierAbandonnes]]", "", $message);
        $message = str_replace("[[MontantTotal]]", "", $message);
        $message = str_replace("[[Commandes]]", "", $message);
        $message = str_replace("[[NouveauxClients]]", "", $message);
        $message = str_replace("[[MeilleureVente]]", "", $message);

            
        $message = str_replace("[[NomSite]]", get_bloginfo('name'), $message);
        $message = str_replace("[[URLSite]]", get_bloginfo('url'), $message);
        $message = str_replace("[[Date]]", gmdate("Y-m-d - H:i:s : ", time()), $message);
        return ApiCalls::apiSendSMS($model['sender'], $num, $message);
    }
}

//Send a SMS to the order's owner when their order's status is "ongoing"
add_action('woocommerce_order_status_processing', 'lws_sms_send_mail_payment_processing');
function lws_sms_send_mail_payment_processing($order_id)
{
    $order = wc_get_order($order_id);
    $user = $order->get_user();
    $usage = 'processing';
    if($user) {
        //Only if admin has activated it first
        $admin_check = get_option('lws_checked_options');
        if ($admin_check[$usage]['is_checked']) {
            //Only if client has authorized SMS for this
            $checkboxes = array();
            get_user_meta($user->ID, 'checkboxes', true) ?
            $checkboxes = get_user_meta($user->ID, 'checkboxes', true) : update_user_meta($user->ID, 'checkboxes', $checkboxes);
            
            if (count($checkboxes) == 0 || $checkboxes[$usage]['is_checked']) {
                lws_sms_send_SMS($user, $usage, $user->phone_sms, null, null, $order);
            }
        }
    }
}

//Send a SMS to the order's owner when their order's status is "cancelled"
add_action('woocommerce_order_status_cancelled', 'lws_sms_send_mail_payment_cancelled');
function lws_sms_send_mail_payment_cancelled($order_id)
{
    $order = wc_get_order($order_id);
    $user = $order->get_user();
    $usage = 'cancelled';
    if($user) {
        //Only if admin has activated it first
        $admin_check = get_option('lws_checked_options');
        if ($admin_check[$usage]['is_checked']) {
            //Only if client has authorized SMS for this
            $checkboxes = array();
            get_user_meta($user->ID, 'checkboxes', true) ?
            $checkboxes = get_user_meta($user->ID, 'checkboxes', true) : update_user_meta($user->ID, 'checkboxes', $checkboxes);
            
            if (count($checkboxes) == 0 || $checkboxes[$usage]['is_checked']) {
                lws_sms_send_SMS($user, $usage, $user->phone_sms, null, null, $order);
            }
        }
    }
}

//Send a SMS to the order's owner when their order's status is "completed"
add_action('woocommerce_order_status_completed', 'lws_sms_send_mail_payment_complete');
function lws_sms_send_mail_payment_complete($order_id)
{
    $order = wc_get_order($order_id);
    $user = $order->get_user();
    $usage = 'completed';
    if($user) {
        //Only if admin has activated it first
        $admin_check = get_option('lws_checked_options');
        if ($admin_check[$usage]['is_checked']) {
            //Only if client has authorized SMS for this
            $checkboxes = array();
            get_user_meta($user->ID, 'checkboxes', true) ?
            $checkboxes = get_user_meta($user->ID, 'checkboxes', true) : update_user_meta($user->ID, 'checkboxes', $checkboxes);
            
            if (count($checkboxes) == 0 || $checkboxes[$usage]['is_checked']) {
                lws_sms_send_SMS( $user, $usage, $user->phone_sms, null, null, $order);
            }
        }
    }
}

//Send a SMS to every client getting ads to inform them of a new coupon
add_action('woocommerce_update_coupon', 'lws_sms_after_new_coupon_created', 10, 1);
function lws_sms_after_new_coupon_created($coupon_id)
{
    $users = array();
    if (!$users = get_option('lws_ads_clients')) {
        $users = array(wp_get_current_user() => false);
        update_option('lws_ads_clients', $users);
    }
    
    $usage = 'coupon';
    global $woocommerce;
    $coupon = new WC_Coupon($coupon_id);
    //Only if admin has activated it first
    $admin_check = get_option('lws_checked_options');
    if ($admin_check[$usage]['is_checked']) {
        //Only if client has authorized SMS for this
        foreach ($users as $user_id => $ok) {
            if ($ok) {
                $user = get_user_by('id', $user_id);
                if ($user->phone_sms) {
                    lws_sms_send_SMS($user, $usage, $user->phone_sms, $coupon, null, null);
                }
            }
        }
    }
}

//SMS to the admin if new order
add_action('woocommerce_new_order', 'lws_sms_new_order', 10, 1);
function lws_sms_new_order($order_id)
{
    $users = get_users();
    $users_can = array();
    $order = wc_get_order($order_id);
    $client = $order->get_user();
    $usage = 'admin_new_order';
    $admin_check = get_option('lws_checked_options');
    if ($admin_check[$usage]['is_checked']) {
        foreach ($users as $user) {
            if (user_can($user, 'manage_woocommerce')) {
                if ($user->phone_sms) {
                    lws_sms_send_SMS($client, $usage, $user->phone_sms, null, null, $order);
                }
            }
        }
    }
}

add_action('check_sessions_cron', array('CheckSessions', 'check'));
//END SMS SCENARIOS//

//Add a new menu before Logout in the shop to put LWS SMS config
add_filter('woocommerce_account_menu_items', 'lws_sms_config_woocommerce_new_menu', 40);
function lws_sms_config_woocommerce_new_menu($menu_links)
{
    $menu_links = array_slice($menu_links, 0, 5, true)
    + array( 'lws-sms' => __('Alert SMS Settings', 'lws-sms') )
    + array_slice($menu_links, 5, null, true);
    
    return $menu_links;
}

//Initiate the CSS for our pages
function lws_sms_css_woocommerce_front()
{
    wp_enqueue_style('wordpress_lws_css', plugins_url('/css/woocommerce.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'lws_sms_css_woocommerce_front');

// register permalink endpoint
add_action('init', 'lws_add_endpoint_sms');
function lws_add_endpoint_sms()
{
    //Add the endpoint, AKA the link to our page
    add_rewrite_endpoint('lws-sms', EP_PAGES);
}
// content for the new page in My Account, woocommerce_account_{ENDPOINT NAME}_endpoint
add_action('woocommerce_account_lws-sms_endpoint', 'lws_sms_lws_account_endpoint_content');
function lws_sms_lws_account_endpoint_content()
{
    lws_sms_checklist_model_client();
}

//Add the paramters to send SMS for the client
//add_action('woocommerce_account_dashboard', 'lws_sms_checklist_model_client');
function lws_sms_checklist_model_client()
{
    //List of every variables used
    $user_id = get_current_user_id();
    $checked_options_client = array();
    $checked_options = array();
    $is_sms_active = false;

    //Getting all checkboxes for the client
    //If null, create it
    if (!get_user_meta($user_id, 'checkboxes', true)) {
        foreach (HOOKS as $hook) {
            $checked_options_client[$hook]['is_checked'] = true;
        }
        update_user_meta($user_id, 'checkboxes', $checked_options_client);
    }
    $checked_options_client = get_user_meta($user_id, 'checkboxes', true);
    
    //Getting all checkboxes status, creating it if needed
    if (!$checked_options = get_option('lws_checked_options')) {
        foreach (HOOKS_BOTH_CATEGORIES as $hook) {
            $checked_options[$hook]['is_checked'] = false;
            $checked_options[$hook]['used'] = 'NO';
        }
        update_option('lws_checked_options', $checked_options);
    }
    
    //Check if at least one option is active
    foreach (HOOKS as $hook) {
        if ($checked_options[$hook]['is_checked']) {
            $is_sms_active = true;
            break;
        }
    }
    
    //When submitted, check if checkboxes are checked
    //Update the options
    if (isset($_POST['update_sms_client'])) {
        foreach (HOOKS as $hook) {
            isset($_POST['client_order_' . $hook]) ?
            $checked_options_client[$hook]['is_checked'] = true : $checked_options_client[$hook]['is_checked'] = false;
        }
        update_user_meta($user_id, 'checkboxes', $checked_options_client);
    }

    include __DIR__ . '/view/client_page.php';
}

//WOOCOMMERCE//
?>
