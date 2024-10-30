<h3 class="lws_sms_title"> <?php esc_html_e("About this plugin", "lws-sms") ?> </h3>
<?php $arr = array('a' => array('href' => array(), 'target' => array(), ), 'strong' => array(), 'q' => array() );?>
<p class="lws_sms_paragraph">
    <?php esc_html_e("Here is a preview of what you can do with your new plugin: ", "lws-sms");?>
    <ul class="lws_sms_paragraph" style="list-style: square inside;">
        <li> <?php esc_html_e("Create your own sender IDs that will be shown in your SMS", "lws-sms");?> </li>
        <li> <?php esc_html_e("Create personnalized SMS templates with tags to make sure all your SMS are unique and tailored for your clients", "lws-sms");?> </li>
        <li> <?php esc_html_e("Send SMS to your clients at precise moment such as when an order is validated or whenever you want in not time", "lws-sms");?> </li>
        <li> <?php esc_html_e("Send a massive amount of SMS at the same time to all your client, be it for advertisement or announcement, with no limitations whatsoever as long as you have credits", "lws-sms");?> </li>
        <li> <?php esc_html_e("Keep an eye on your account, such as your balance or history at all time with dedicated tabs", "lws-sms");?> </li>
        <li> <?php esc_html_e("SMS History, Sender List, Low Balance Alert, ... Everything you need to manage your account are found here directly on your site", "lws-sms");?> </li>
    </ul>
</p>

<div id="banner_lws"></div>

<h3 class="lws_sms_title"> <?php esc_html_e("Why choose LWS?", "lws-sms");?> </h3>
<p class="lws_sms_paragraph">
    <?php esc_html_e("We offer premium quality for our SMS, making sure that your SMS will be sent with utmost care and priority.", "lws-sms");?>
    <?php echo wp_kses(__("There is no registration cost and our staff will always help you if needed. Just go on our <a href='https://www.lws.fr/contact_formulaire.php' target='_blank'>contact page</a> to fill in the form or ask a question on our LiveChat.", "lws-sms"), $arr);?>
    <?php esc_html_e("With a very low cost of 8 cents per SMS in more than 180 countries with not additional cost, LWS is the best way to take your company to the next level.", "lws-sms");?>
</p>
<h3 class="lws_sms_title"> <?php esc_html_e("Advantages of doing SMS Marketing", "lws-sms");?> </h3>
<p class="lws_sms_paragraph">
    <?php esc_html_e("In contrast to older method such as e-mail or flyers, SMS are more modern and efficient: everyone has a phone, nowadays, so it is way more impactful.", "lws-sms")?>
    <?php esc_html_e("Furthermore, it is not intrusive because people have to agree to receive these ads.", "lws-sms")?>
</p>

<?php if (!get_option("lws_sender_id") || !get_option("lws_user_sms")) :?>
<h3 class="lws_sms_title"> <?php esc_html_e("Getting started", "lws-sms");?> </h3>
<p class="lws_sms_paragraph">
    <?php echo wp_kses(__("No cost, no subscription, pay only what you consume: Start using LWS SMS now and get your first credits on our website <a href='https://www.lws.fr/envoyer-sms-par-internet.php' target='_blank'>here</a>!", "lws-sms"), $arr)?>
    <?php echo wp_kses(__("To use this plugin, you need to have a <q>LWS SMS account</q> linked to your website. Buying your first credits will automatically create one for you.", "lws-sms"), $arr);?>
    <br>
</p>

<form method="POST">
    <div class="lws_sms_form_connect_user">

        <?php if (isset($formError)) : ?>
        <div class='error_message'>
            <?php echo esc_html($formErrorMessage)?>
        </div>
        <?php endif ?>
        <span class="lws_sms_connexion_bloc">
            <input class="lws_sms_input_connect" type="text"
                placeholder="<?php esc_attr_e("Enter your username", "lws-sms")?>"
                name="username" required>
            <input class="lws_sms_input_connect" type="text"
                placeholder="<?php esc_attr_e("Enter your API Key", "lws-sms")?>"
                name="APIKey" required>
            <input class="button_disconnect" style="margin-right:initial" name="validate_connexion" type="submit"
                id='submit'
                value="<?php esc_attr_e("Log in", "lws-sms")?>">
        </span>
        <small
            class="lws_sms_connect_form_help"><?php echo wp_kses(__("You can find your API Key in the <q>SMS Api</q> section on your <a href='https://sms.lws.fr/' target='_blank'>dashboard</a>.", "lws-sms"), $arr);?></small>
    </div>
</form>


<p style="padding-left:30px">
    <?php echo wp_kses(__("If you need help setting up this plugin, check out the <a href='https://aide.lws.fr/base/E-commerce/Outils-Marketing-Referencement-web/Comment-installer-le-plugin-LWS-SMS-sur-votre-boutique-en-ligne-PrestaShop' target='_blank'>documentation</a> online.", "lws-sms"), $arr);?>
</p>
<?php else : ?>
<h3 class="lws_sms_title lws_sms_form_bloc_connect">
    <?php esc_html_e("Disconnexion", "lws-sms");?>
</h3>
<form method="POST" class="lws_sms_form_connect">
    <p class="paragraph_disconnect">
        <?php esc_html_e("To get disconnected or change accounts, click here: ", "lws-sms");?>
        </php>
    </p>
    <button class="button_disconnect" name="disconnect_user" type="submit" id='submit'>
        <img src="<?php echo esc_url(plugins_url('/images/deconnexion_blanc.svg', __DIR__))?>"
            alt="Logo Disconnect" width="15px" height="15px"
            style="vertical-align: text-bottom; margin-right: 5px;"></img>
        <span
            style="line-height:23px;"><?php esc_html_e("Disconnect", "lws-sms");?></span>
    </button>
</form>
<?php endif ?>