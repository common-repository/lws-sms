<h3 class="bloc_general_titre">
    <?php esc_html_e("Plugin Overview", "lws-sms");?>
</h3>
<?php $arr = array('a' => array('href' => array(), 'target' => array(), ), 'strong' => array(), 'q' => array() );?>

<p class="lws_sms_paragraph">
    <?php esc_html_e("With LWS SMS, you can create your own SMS templates and configure them to be sent to your clients on specific occasions.", "lws-sms");?>
    <?php esc_html_e("This plugin allow you to launch SMS Campaigns. Be it sending advertisments, proposing special offers or giving updates about an order, you can do it with this plugin.", "lws-sms");?>
</p>
<p class="lws_sms_paragraph">
    <?php echo wp_kses(__("To use said plugin, you need SMS credits, recheargable at <a href='https://www.lws.fr/envoyer-sms-par-internet.php' target='_blank'>our website</a>.", "lws-sms"), $arr);?>
    <?php esc_html_e("Please note that LWS SMS is intended to be used with WooCommerce. Please install it to access the whole functionalities of this extension.", "lws-sms");?>
</p>

<div>
    <h3 class="bloc_general_titre">
        <?php esc_html_e("SMS Balance", "lws-sms");?>
    </h3>
    <form method="POST">
        <div class="bloc_sms_general">
            <div>
                <span class="lws_sms_amount">
                    <?php echo esc_html($SMSBalance . " SMS")?>
                </span>
                <span class="lws_sms_mail_alert">
                    <span>
                        <?php esc_html_e("Mail alert at: ", "lws-sms")?>
                    </span>
                    <input class="lws_sms_mail_alert_input" type="number" name="alert_sms" min="1"
                        value="<?php echo esc_attr($alert_sms)?>">
                    <span>SMS</span>
                </span>
            </div>
            <input class="button_disconnect" name="change_alert_sms" type="submit"
                value="<?php esc_attr_e("Update alert", "lws-sms")?>">
        </div>
    </form>
</div>

<h3 class="bloc_general_titre">
    <?php esc_html_e("Senders", "lws-sms");?>
</h3>
<div class="bloc_sender_general">
    <div>
        <?php foreach ($sender_ids as $sender) : ?>
        <span class="lws_sms_senders_general">
            <?php echo esc_html($sender)?>
        </span>
        <?php endforeach ?>
    </div>
</div>

<div class="bloc_history_general">
    <h3 class="bloc_general_titre">
        <?php esc_html_e("Most recent SMS", "lws-sms");?>
    </h3>
    <div>
        <table id="table_history" class="lws_sms_history_table_general">
            <thead>
                <tr>
                    <th style="width:20%"> <?php esc_html_e("Sender", "lws-sms")?> </th>
                    <th style="width:80%"> <?php esc_html_e("Message", "lws-sms")?> </th>
                    <th style="width:5%"> <?php esc_html_e("Status", "lws-sms")?> </th>
                </tr>
            </thead>
            <tbody>
                <?php $limit = 5; $i = 0?>
                <?php foreach ($list_history as $history) : ?>
                    <?php if ($i < $limit) : ?>
                        <tr>
                            <td> <?php echo esc_html($history['sender']);?> </td>
                            <td> <?php echo esc_html($history['message']); ?> </td>
                            <td style="vertical-align: middle;">
                                <?php $status = explode("|", $history['status'])[0]; ?>
                                <?php if ($status == 'Success') : ?>
                                    <span class="lws_sms_history_success_general">
                                        <img style="padding-right:10px"
                                            src="<?php echo esc_url(plugins_url('images/check_vert.svg', __DIR__))?>"
                                            alt="Success" width="15px" height="12px">
                                        <?php esc_html_e('Success', 'lws-sms'); ?>
                                    </span>
                                <?php else : ?>
                                    <span class="lws_sms_history_error_general">
                                        <img style="padding-right:10px"
                                            src="<?php echo esc_url(plugins_url('images/croix.svg', __DIR__))?>"
                                            alt="Error" width="15px" height="12px">
                                        <?php esc_html_e('Error', 'lws-sms'); ?>
                                    </span>
                                <?php endif ?>
                            </td>
                        </tr>
                        <?php $i++; ?>
                    <?php endif ?>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<div style="text-align: center;margin-top: 20px;">
    <input class="button_disconnect" type="button"
        value="<?php esc_attr_e("Display the entire SMS History", "lws-sms");?>"
        onclick="lws_sms_change_to_history()">
</div>

<script>
    <?php if ($SMSBalance <= $alert_sms) : ?>
        jQuery('.lws_sms_amount').addClass('text_red');
    <?php endif ?>

    function lws_sms_change_to_sender() {
        lws_sms_changeTabs(document.getElementById('nav-sender'));
    }

    function lws_sms_change_to_history() {
        lws_sms_changeTabs(document.getElementById('nav-history'));
    }
</script>

<script>
    jQuery(document).ready(function() {
        var table = jQuery('#table_history').DataTable({
            searching: false,
            paging: false,
            info: false,
            sort: false,
            responsive: true,
            <?php if(get_locale() == 'fr_FR') : ?>
            language: {
                url: "<?php echo(esc_url(plugin_dir_url(__DIR__) . 'languages/fr-FR.json'))?>"
            }
            <?php endif ?>
        });
    });
</script>