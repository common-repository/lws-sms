<?php

class CheckBalance{
    
    public function _construct(){}

    public static function init(){
        add_action('send_mail_balance', array(get_called_class(), 'check'), 10, 2);
    }
    
    public static function check($user, $limit){
        if ( ! class_exists( 'ApiCalls', false ) ) {
            include_once LWS_SMS_DIR . '/src/API/ApiCalls.php';
        }

        if ($limit > ApiCalls::apiGetBalance()) {
            $html  = (get_locale() == 'fr_FR') ? file_get_contents(LWS_SMS_DIR . '/mail/mail-alerte-sms-fr.html') : file_get_contents(LWS_SMS_DIR . '/mail/mail-alerte-sms.html');
            $html = chunk_split(base64_encode($html), 70);
    
            // To send HTML mail, the Content-type header must be set
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-Type: text/html; charset="utf-8"';
            $headers[] = 'Content-Transfer-Encoding: base64';
            // En-têtes additionnels
            $name = get_bloginfo('name', '');
            $site = sanitize_text_field($_SERVER['SERVER_NAME']);
            $headers[] = 'FROM: '. esc_html($name) .' <noreply@'. esc_html($site) . '>';
            $headers[] = 'In-Reply-To: <e08d4521220acc90df005ae3231c8fef20549b62@'.esc_html($site).'>';
    
            return(wp_mail(esc_html($user->user_email), esc_html__("Low SMS Balance Alert", "lws-sms"), $html, implode("\r\n", $headers)));
        }
    }
}