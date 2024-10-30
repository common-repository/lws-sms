<div class="notice-error_lws" style="padding:1em; margin: 15px 0px 15px;">
    <?php esc_html_e("The API Key saved on this website does not match with the one in our database. Please update your info to use the plugin again: ", "lws-sms");?>
    <a class="button_connect" style="vertical-align:unset" href="<?php echo esc_url(get_site_url() . '/wp-admin/plugins.php?page=sms-api-settings')?>"><?php esc_html_e("Update", "lws-sms");?></a>
</div>