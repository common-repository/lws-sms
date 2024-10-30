<h3 class="bloc_general_titre">
    <?php esc_html_e("Senders", "lws-sms");?>
</h3>
<?php $arr = array('a' => array('href' => array(), 'target' => array()));?>

<div class="lws_sms_paragraph_sender">
    <?php esc_html_e("Find on this page every SenderID that have been accepted on your account.", "lws-sms");?>
    <br>
    <?php esc_html_e("By filling up the form, you can request another ID, but please be aware of the rules below: ", "lws-sms");?>
    <br>
    <ul style="list-style: inside;">
        <li><?php esc_html_e("Validation of your ID may take up to a few hours as it is manually checked by an operator", "lws-sms");?>
        </li>
        <li><?php esc_html_e("Your ID must be in the alphanumeric format and 11 characters maximum", "lws-sms");?>
        </li>
        <li><?php esc_html_e("Your ID must contains at least 1 letter", "lws-sms");?>
        </li>
    </ul>
    <?php esc_html_e("You are a professional and want to use our service to send messages related to banks, finance, pension, state-related administration, postal delivery, ... ?", "lws-sms");?>
    <?php echo wp_kses(__("Please contact us via a <a href='https://sms.lws.fr/user/tickets/create-new' target='_blank'>ticket</a> on our website indicating your line of business, your company's name as well as any document proving the veracity of your claims.", "lws-sms"), $arr);?>
    <?php esc_html_e("Otherwise, such requests will be refused.", "lws-sms");?>
</div>

<?php if(isset($is_added)) : ?>
<?php if($is_added) : ?>
<div class='success_message'><?php echo esc_html($add_message)?>
</div>
<?php else : ?>
<div class='error_message'><?php echo esc_html($add_message)?>
</div>
<?php endif ?>
<?php endif ?>

<div class="lws_sms_senders_blocks">
    <div class="lws_sms_senders_list">
        <h3 style="padding-left:0px; margin-bottom:25px" class="bloc_general_titre">
            <?php esc_html_e("Senders List", "lws-sms");?>
        </h3>
        <table id="list_senders" class="lws_sms_table_senders">
            <tbody>
                <?php foreach ($sender_ids as $key => $value) :?>
                <tr>
                    <td>
                        <?php echo esc_html($value) ?>
                    </td>
                </tr>
                <?php endforeach ?>
        </table>
    </div>

    <div class="lws_sms_senders_form">
        <h3 class="bloc_general_titre">
            <?php esc_html_e("Add a sender", "lws-sms");?>
        </h3>
        <div class="lws_sms_senders_bloc_form">
            <?php if (count($sender_ids) > 3) : ?>
            <p class="lws_sms_senders_limit_reached">
                <?php esc_html_e('You reached the maximum amount of senders available. Please delete one from your account to add a new one.', 'lws-sms');?>
            </p>
            <?php endif ?>
            <form class=" lws_sms_form_add_sender" method="POST">
                <input class="lws_sms_input_form_sender" type="text"
                    placeholder="<?php esc_attr_e("Sender ID", "lws-sms")?>"
                    name="sender_name" maxlength="11" required>

                <input class="button_disconnect" name="new_sender" type="submit"
                    <?php echo count($sender_ids) > 3 ? esc_attr('disabled') : ''; ?>
                value="<?php esc_attr_e("Add", "lws-sms")?>">
            </form>

            <p class="lws_sms_senders_form_text">
                <?php esc_html_e("Please read the rules before adding a new SenderID.", "lws-sms");?>
                <?php esc_html_e("Too much rejected requests may result in a ban.", "lws-sms")?>
            </p>
        </div>
    </div>
</div>

<!-- <script>
    jQuery(document).ready(function($) {
        jQuery('#list_senders').DataTable({
            lengthMenu: [
                [5, 10, 25, 50],
                [5, 10, 25, 50],
            ],
            <?php //if(get_locale() == 'fr_FR') :?>
language: {
searchPlaceholder:
"<?php esc_html_e('Search', 'lws-sms');?>",
search: "",
url:
"<?php //echo esc_url(plugin_dir_url(__DIR__) . 'languages/fr-FR.json')?>"
}
<?php //endif?>
});
});
</script> -->