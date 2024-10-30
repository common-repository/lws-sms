<?php if (isset($_POST['update_sms_client'])) :?>
<div class="success_message"> <?php esc_html_e("Your settings have been updated", "lws-sms")?>
</div>
<?php endif ?>

<?php //When going normally (as client)
if ($is_sms_active): ?>
<fieldset>
    <?php if (!wp_get_current_user()->phone_sms): ?>
    <legend><?php esc_html_e("Please fill in your phone number to access the SMS Marketing options", "lws-sms");?>
    </legend>
    <?php else :?>
    <legend><?php esc_html_e("Check when you want to receive SMS: ", "lws-sms");?>
    </legend>
    <form method="POST">
        <?php foreach (HOOKS as $key => $hook_client):?>
        <?php if($checked_options[$hook_client]['is_checked']):?>
        <ul style="list-style: inside;">
            <li>
                <label
                    for="<?php echo esc_attr('client_order_' . $hook_client);?>"><?php echo esc_html('"' . __(HOOKS_TRAD[$key], 'lws-sms') . '" ');?></label>
                <?php if($checked_options_client[$hook_client]['is_checked']):?>
                <input class="checkbox_sms_client" type="checkbox"
                    id="<?php echo esc_attr('client_order_' . $hook_client);?>"
                    name="<?php echo esc_attr('client_order_' . $hook_client);?>"
                    checked>
                <?php else:?>
                <input class="checkbox_sms_client" type="checkbox"
                    id="<?php echo esc_attr('client_order_' . $hook_client);?>"
                    name="<?php echo esc_attr('client_order_' . $hook_client);?>">
                <?php endif?>
            </li>
        </ul>
        <?php endif ?>
        <?php endforeach ?>
        <input class="button_connect" name="update_sms_client" type="submit" id="update_sms_client"
            value="<?php esc_attr_e("Update", "lws-sms")?>">
    </form>
    <?php endif ?>
</fieldset>
<?php else :?>
<fieldset>
    <span>
        <?php esc_html_e('This option has not been activated on this website. Please try again later.', 'lws-sms');?>
    </span>
</fieldset>
<?php endif ?>
<!-- END Checkboxes -->