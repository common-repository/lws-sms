<?php //If connected
if ((get_option("lws_sender_id"))|| (get_option("lws_user_sms")) ) :?>

    <h3 class="bloc_general_titre"><?php esc_html_e("SMS Campaigns", "lws-sms");?></h3>
    <p class="lws_sms_paragraph_sender">
        <?php esc_html_e("This page allows you to send a SMS to any clients having authorized the reception of advertisements.", "lws-sms");?>
        <?php esc_html_e("Please be aware that it may consume a large amount of credits, which will be indicated at the bottom of the page.", "lws-sms");?>
        <?php esc_html_e("To use this service, you need to possess at least one active sendor ID and at least one client need to be in the list.", "lws-sms");?>
    </p>

    <!-- Print a message when SMS are being sent.  -->
    <?php if ($sms_sent == 1 ) :?>
        <div class="success_message">
            <?php printf(esc_html__("Your SMS has succesfully been sent", "lws-sms"), $sms_sent)?>
        </div>
    <?php elseif ($sms_sent >= 2) : ?>
        <div class="success_message">
            <?php printf(esc_html__("%d SMS have succesfully been sent", "lws-sms"), $sms_sent)?>
        </div>
    <?php elseif ($sms_sent == 0) : ?>
        <div class="error_message">
            <?php esc_html_e("No SMS have been sent", "lws-sms")?>
        </div>
    <?php endif ?>
    
    <?php if ($empty_template) :?>
        <div class="error_message">
            <?php esc_html_e("Please choose a valid template to send a SMS", "lws-sms")?>
        </div>
    <?php endif ?>
    
    <?php if ($no_balance) :?>
        <div class="error_message">
            <?php esc_html_e("You do not have enough balance to send this SMS", "lws-sms")?>
        </div>
    <?php endif ?>
    
    <form class="lws_sms_campaign_form" method="POST">  
            <div class="lws_sms_switch_campaign">
                <input class="lws_sms_checkboxes" type="checkbox" name="model_or_not" id="model_or_not">
                <label for="model_or_not">
                    <?php esc_html_e("Send a SMS from a template", "lws-sms")?>
                </label>
            </div>
        <!-- Block to write & send a SMS -->
        <fieldset class="lws_sms_campaign_main_bloc" id="form_model">
            <div class="lws_sms_campaign_variables" id="variables">
                <button class="lws_sms_variable_button" type="button" value="[[Nom]]" onclick="lws_sms_addVar_campaign(this)"><?php esc_html_e("Name", "lws-sms");?></button>
                <button class="lws_sms_variable_button" type="button" value="[[Prenom]]" onclick="lws_sms_addVar_campaign(this)"><?php esc_html_e("First name", "lws-sms");?></button>
                <button class="lws_sms_variable_button" type="button" value="[[Adresse]]" onclick="lws_sms_addVar_campaign(this)"><?php esc_html_e("Address", "lws-sms");?></button>
                <button class="lws_sms_variable_button" type="button" value="[[Date]]" onclick="lws_sms_addVar_campaign(this)"><?php esc_html_e("Date", "lws-sms");?></button>
                <button class="lws_sms_variable_button" type="button" value="[[NomSite]]" onclick="lws_sms_addVar_campaign(this)"><?php esc_html_e("Website Name", "lws-sms");?></button>
                <button class="lws_sms_variable_button" type="button" value="[[URLSite]]" onclick="lws_sms_addVar_campaign(this)"><?php esc_html_e("Website URL", "lws-sms");?></button>
            </div>

            <div class="lws_sms_textarea_campaign">
                <label id="label_template" name="label_template" for="marketing_model" style="margin-bottom:10px"><?php esc_html_e("Write your SMS: ", "lws-sms");?></label>
                <textarea rows="6" class="lws_sms_textarea_campaign_input" id="marketing_model" name="marketing_model" required
                    placeholder="<?php esc_html_e("Write your SMS here. You can use the tags above to personnalize your SMS.", "lws-sms")?>">
                </textarea>
                <div id="char_count_and_select_and_send">
                    <small id="number_char_campaign">0/160</small>
                    <small><?php esc_html_e("character(s) | ", "lws-sms");?></small><small>SMS: </small>
                    <small><input class="nb_sms" name="number_message_campaign" id="number_message_campaign" readonly value=1></small>                    
                </div>
                <small class="lws_sms_small_tags"><?php esc_html_e("If you are using tags, please be aware that this value is but an approximation as the real text behind the tags could be longer or shorter.", "lws-sms")?> </small>
            </div>

            <div class="lws_sms_filters_campaign" id="bloc_filter_sms">
                <label for="filter"><?php esc_html_e("Filters: ", "lws-sms")?> </label>
                <select class="lws_sms_filter_select_campaign" id="filter" name="filter">
                    <option selected value="NO">--- ---</option>
                    <option value="country"><?php esc_html_e("Only to clients from a specific country", "lws-sms")?></option>
                    <option value="return"><?php esc_html_e("Only to clients already having ordered before", "lws-sms")?></option>
                    <option value="month_old"><?php esc_html_e("Only to new clients", "lws-sms")?></option>
                    <option value="month_ten"><?php esc_html_e("Only to old clients", "lws-sms")?></option>
                </select>

                <?php include __DIR__ . "/../php/select_country_code.php" ?>
            </div>

            <div class="lws_sms_senders_campaign">
                <label for="senders"><?php esc_html_e("Sender: ", "lws-sms")?> </label>
                <select class="" id="senders" name="senders">
                    <?php foreach ($sender_ids as $key => $value) :?>
                        <option value="<?php echo esc_attr($value);?>"><?php echo esc_html($value);?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="lws_sms_infos_campaign">
                <div>
                    <span id="count_clients">
                        <?php echo esc_html(count($clients_ok))?>
                    </span>
                    <span><?php esc_html_e(" person(s) will receive this SMS", "lws-sms")?></span>
                    </div>
                <div>
                    <span id="nb_sms_send"></span>
                    <span id="sms_send_msg">
                        <?php esc_html_e(" SMS will be sent and your balance will go down to: ", "lws-sms")?>
                    </span>
                    <span id="nb_sms_credit"></span>
                </div>
            </div>
            <input id="send_sms_market" name="send_sms_market" class="button_campaign" type="submit" value="<?php esc_attr_e("Send", "lws-sms");?>"/>
        </fieldset>
        <!-- Select & Button to send a SMS with a template -->
        <fieldset class="hidden_lws lws_sms_form_template_campaign" id='select_from_model'>
            <label for="models"><?php esc_html_e("Template: ", "lws-sms")?> </label>
            <select class="lws_sms_filter_select_campaign" id="models" name="models">
                <option selected value="NO">--- ---</option>
                <?php foreach ($models as $key => $model) :?>
                    <option value="<?php echo esc_attr($model['name']);?>"><?php echo esc_html($model['name']);?></option>
                <?php endforeach ?>
            </select>
            <input id="send_sms_market_model" name="send_sms_market_model" class="button_campaign" type="submit" value="<?php esc_attr_e("Send", "lws-sms");?>"/>
        </fieldset>
    </form>
    
    
<script>
    var clients = <?php echo esc_html(count($clients_ok))?>;
    var balance = <?php echo esc_html($SMSBalance)?>;
    var clients_country = <?php echo (json_encode($client_filter_country))?>;
    var select = document.querySelector("#models");
    var checkbox = document.querySelector("#model_or_not");
    let typingTimerCampaign;                //timer identifier
    let doneTypingIntervalCampaign = 500;  //time in ms
    var sms_campaign = jQuery('#marketing_model');
    var select_filter = document.querySelector("#filter");
    var select_country = document.querySelector("#country_value");

    jQuery(document).ready(function(){
        jQuery('#marketing_model').val('');
        lws_sms_charSMS_campaign();
    });
    

    
    //Check the checkbox and show or not the desired options
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            jQuery('.lws_sms_textarea_campaign').addClass('hidden_lws');
            jQuery('#send_sms_market').addClass('hidden_lws');
            jQuery('.lws_sms_campaign_variables').addClass('hidden_lws');            
            jQuery("#select_from_model").removeClass("hidden_lws");
            jQuery("#bloc_ta_filtre").removeClass("bloc_ta_filtre");
            jQuery("#bloc_filter_sms").addClass("lws_sms_filter_template_mode");
            
            sms_campaign.prop('required', false);
        }
        //If unchecked, return the number of SMS to the value of before
        else {
            jQuery('.lws_sms_textarea_campaign').removeClass('hidden_lws');
            jQuery('.lws_sms_campaign_variables').removeClass('hidden_lws');
            jQuery('#send_sms_market').removeClass('hidden_lws');
            jQuery("#select_from_model").addClass("hidden_lws");
            jQuery("#bloc_ta_filtre").addClass("bloc_ta_filtre");
            jQuery("#bloc_filter_sms").removeClass("lws_sms_filter_template_mode");
            sms_campaign.prop('required', true);
            lws_sms_charSMS_campaign()
            select.value = "";
        }
    });

    //When changing which filter to apply
    select_filter.addEventListener('change', function() {
        switch(this.value){
            case "country":
                jQuery("#country_value").removeClass("hidden_lws");
                if(jQuery("#country_value").val() != ''){
                    var count = 0;
                    for (var key in clients_country) {
                        if (clients_country[key] == jQuery("#country_value").val()) {
                        count++;
                        }
                    }
                    clients = count;
                    jQuery("#count_clients").text(parseInt(clients));
                    jQuery("#nb_sms_send").text(num_sms * parseInt(clients));
                    jQuery("#nb_sms_credit").text(balance - num_sms * parseInt(clients));
                    if (balance - num_sms * parseInt(clients) < 0){
                        jQuery("#send_sms_market").prop('disabled', true);
                        jQuery("#send_sms_market_model").prop('disabled', true);
                        jQuery("#nb_sms_credit").text("<?php esc_html_e("[Not enough credits]", "lws-sms")?>");
                    }
                    else{
                        jQuery("#send_sms_market").prop('disabled', false);
                        jQuery("#send_sms_market_model").prop('disabled', false);
                    }
                }
                if (!checkbox.checked){
                    lws_sms_doneTyping_campaign()
                }
                break;
            case "return":
                clients = <?php echo esc_html(count($client_filter_returning))?>;
                jQuery("#count_clients").text(parseInt(clients));
                jQuery("#country_value").addClass("hidden_lws");
                jQuery("#nb_sms_send").text(num_sms * parseInt(clients));
                jQuery("#nb_sms_credit").text(balance - num_sms * parseInt(clients));
                if (balance - num_sms * parseInt(clients) < 0){
                    jQuery("#send_sms_market").prop('disabled', true);
                    jQuery("#send_sms_market_model").prop('disabled', true);
                    jQuery("#nb_sms_credit").text("<?php esc_html_e("[Not enough credits]", "lws-sms")?>");
                }
                else{
                    jQuery("#send_sms_market").prop('disabled', false);
                    jQuery("#send_sms_market_model").prop('disabled', false);
                }
                if (!checkbox.checked){
                    lws_sms_doneTyping_campaign()
                }
                break;
            case "month_old":
                clients = <?php echo esc_html(count($client_filter_registered_1_month))?>;
                jQuery("#count_clients").text(parseInt(clients));
                jQuery("#country_value").addClass("hidden_lws");
                jQuery("#nb_sms_send").text(num_sms * parseInt(clients));
                jQuery("#nb_sms_credit").text(balance - num_sms * parseInt(clients));
                if (balance - num_sms * parseInt(clients) < 0){
                    jQuery("#send_sms_market").prop('disabled', true);
                    jQuery("#send_sms_market_model").prop('disabled', true);
                    jQuery("#nb_sms_credit").text("<?php esc_html_e("[Not enough credits]", "lws-sms")?>");
                }
                else{
                    jQuery("#send_sms_market").prop('disabled', false);
                    jQuery("#send_sms_market_model").prop('disabled', false);
                }
                if (!checkbox.checked){
                    lws_sms_doneTyping_campaign()
                }
                break;
            case "month_ten":
                clients = <?php echo esc_html(count($client_filter_registered_10_months))?>;
                jQuery("#count_clients").text(parseInt(clients));
                jQuery("#country_value").addClass("hidden_lws");
                jQuery("#nb_sms_send").text(num_sms * parseInt(clients));
                jQuery("#nb_sms_credit").text(balance - num_sms * parseInt(clients));
                if (balance - num_sms * parseInt(clients) < 0){
                    jQuery("#send_sms_market").prop('disabled', true);
                    jQuery("#send_sms_market_model").prop('disabled', true);
                    jQuery("#nb_sms_credit").text("<?php esc_html_e("[Not enough credits]", "lws-sms")?>");
                }
                else{
                    jQuery("#send_sms_market").prop('disabled', false);
                    jQuery("#send_sms_market_model").prop('disabled', false);
                }
                if (!checkbox.checked){
                    lws_sms_doneTyping_campaign()
                }
                break;
            default:
                jQuery("#count_clients").text(parseInt(<?php echo esc_html(count($clients_ok));?>));
                jQuery("#country_value").addClass("hidden_lws");
                jQuery("#nb_sms_send").text(num_sms * parseInt(<?php echo esc_html(count($clients_ok));?>));
                jQuery("#nb_sms_credit").text(balance - num_sms * parseInt(<?php echo esc_html(count($clients_ok));?>));
                clients = parseInt(<?php echo esc_html(count($clients_ok));?>);
                if (balance - num_sms * parseInt(<?php echo esc_html(count($clients_ok));?>) < 0){
                    jQuery("#send_sms_market").prop('disabled', true);
                    jQuery("#send_sms_market_model").prop('disabled', true);
                    jQuery("#nb_sms_credit").text("<?php esc_html_e("[Not enough credits]", "lws-sms")?>");
                }
                else{
                    jQuery("#send_sms_market").prop('disabled', false);
                    jQuery("#send_sms_market_model").prop('disabled', false);
                }
                if (!checkbox.checked){
                    lws_sms_doneTyping_campaign()
                }       
                break;
        }
    });
    
    //When choosing which country to filter with
    var count = 0;
    select_country.addEventListener('change', function() {
        for (var key in clients_country) {
            if (clients_country[key] == this.value) {
                count++;
            }
        }
        clients = count;
        jQuery("#count_clients").text(parseInt(clients));
        jQuery("#nb_sms_send").text(num_sms * parseInt(clients));
        jQuery("#nb_sms_credit").text(balance - num_sms * parseInt(clients));
        if (balance - num_sms * parseInt(clients) < 0){
            jQuery("#send_sms_market").prop('disabled', true);
            jQuery("#send_sms_market_model").prop('disabled', true);
            jQuery("#nb_sms_credit").text("<?php esc_html_e("[Not enough credits]", "lws-sms")?>");
        }
        else{
            jQuery("#send_sms_market").prop('disabled', false);
            jQuery("#send_sms_market_model").prop('disabled', false);
        }
    });

    //Select to choose a model
    select.addEventListener('change', function() {
        if (this.value == ""){
            jQuery("#nb_sms_credit").text(balance);
            jQuery("#nb_sms_send").text(0);
        }
        else{
            for (const [key, value] of Object.entries(<?php echo wp_kses_data($model_list)?>)){
                if (value['name'] == this.value){
                    jQuery("#nb_sms_send").text(((value['nb_sms']) * parseInt(clients)));
                    num_sms = value['nb_sms'];
                    jQuery("#nb_sms_credit").text(balance - num_sms * parseInt(clients));
                    if (balance - num_sms * parseInt(clients) < 0){
                        jQuery("#send_sms_market").prop('disabled', true);
                        jQuery("#send_sms_market_model").prop('disabled', true);
                        jQuery("#nb_sms_credit").text("<?php esc_html_e("[Not enough credits]", "lws-sms")?>");
                    }
                    else{
                        jQuery("#send_sms_market").prop('disabled', false);
                        jQuery("#send_sms_market_model").prop('disabled', false);
                    }
                }
            };
        }
        
    });

    jQuery("#nb_sms_credit").text(balance - parseInt(clients));
    jQuery("#nb_sms_send").text(parseInt(clients));

    var num_sms = 1;
    //Check charaters (basic, no UNICODE checking) + number of messages
    function lws_sms_charSMS_campaign(unicode = null){
        if (unicode){
            length = [70, 134, 201, 268, 335, 402, 469, 536, 603];
        }
        else{
            length = [160, 306, 459, 612, 765, 918, 1071, 1224, 1377];
        }

        var num_sms = 1;
        if (sms_campaign.val().length <= length[0]){
            jQuery("#number_char_campaign").text(sms_campaign.val().length + "/" + length[0]);
            jQuery("#number_message_campaign").val(1);
            jQuery("#nb_sms_send").text(parseInt(clients))
            num_sms = parseInt(clients);
            
        }
        else if (sms_campaign.val().length > length[length.length -1]){
            jQuery("#number_char_campaign").text(sms_campaign.val().length + "/" + length[length.length -1] + "+");
            jQuery("#number_message_campaign").val(length.length + '+');
            jQuery("#nb_sms_send").text(((length.length) * parseInt(clients)) + "+")
            num_sms = (length.length) * parseInt(clients);
        }
        else{
            for (var i = 1; i < length.length; i++) {
                if (sms_campaign.val().length > length[i-1] && sms_campaign.val().length <= length[i]){
                    jQuery("#number_char_campaign").text(sms_campaign.val().length + "/" + length[i]);
                    jQuery("#number_message_campaign").val(i+1);
                    jQuery("#nb_sms_send").text((i+1) * parseInt(clients))
                    num_sms = (i+1) * parseInt(clients);
                }
            };
        }
        
        jQuery("#nb_sms_credit").text(balance - num_sms);
        if (balance - num_sms < 0){
            jQuery("#send_sms_market").prop('disabled', true);
            jQuery("#send_sms_market_model").prop('disabled', true);
            jQuery("#nb_sms_credit").text("<?php esc_html_e("[Not enough credits]", "lws-sms")?>");
        }
        else{
            jQuery("#send_sms_market").prop('disabled', false);
            jQuery("#send_sms_market_model").prop('disabled', false);
        }

        return num_sms;
    }
    
    lws_sms_charSMS_campaign(false);
    
    jQuery(function(){ // this will be called when writing in the TA
        jQuery("#marketing_model").keyup(function(){
            clearTimeout(typingTimerCampaign);
            typingTimerCampaign = setTimeout(lws_sms_doneTyping_campaign, doneTypingIntervalCampaign);
        });
    });

    function lws_sms_doneTyping_campaign(){
        var txt = sms_campaign.val();
        var data = {
            _ajax_nonce: '<?php echo esc_attr(wp_create_nonce('check_unicode_lws_sms')); ?>',
            action: "unicodeChecker",
            ajax_text : txt,
        };

        jQuery.post(ajaxurl, data, function(response) {
            lws_sms_charSMS_campaign(response);
        });
    }

    //Add variable at position in the textarea
    function lws_sms_addVar_campaign(e){
        var sms_campaign = jQuery('#marketing_model');
        var smsValue = sms_campaign.val();
        var variable = e.value;
        var add_at_position = sms_campaign.prop( "selectionEnd" );
        var nextSelectionEnd = ( add_at_position + variable.length );

        sms_campaign.val(
            smsValue.slice( 0, add_at_position ) +
            variable +
            smsValue.slice( add_at_position )
        );

        sms_campaign
            .prop( "selectionStart", nextSelectionEnd )
            .prop( "selectionEnd", nextSelectionEnd )
            .focus()
        ;

        var txt = sms_campaign.val();
        var data = {
            _ajax_nonce: '<?php echo esc_attr(wp_create_nonce('check_unicode_lws_sms')); ?>',
            action: "unicodeChecker",
            ajax_text : txt,
        };

        jQuery.post(ajaxurl, data, function(response) {
            lws_sms_charSMS_campaign(response);
        });
        
    }
</script>
<?php endif ?>
