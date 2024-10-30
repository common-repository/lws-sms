<h3 class="bloc_general_titre automatisation"> <?php esc_html_e("API Settings", "lws-sms")?> </h3>
<!-- If page somehoow is accessed without admin privilege, exit directly -->
<?php if (!current_user_can('administrator')): exit; endif;?>

<!-- Success ! -->
<?php if (isset($update_sms)) :?>
    <div class="success_message"> 
        <?php esc_html_e("Your settings have been updated", "lws-sms")?>
    </div>
<?php endif ?>

<!-- Stop the loading of the page if no templates -->
<?php if (empty($models)) : ?>
    <div class="error_message">
        <?php esc_html_e("You do not have any templates. Please create at least one to use this functionality.", "lws-sms")?>
    </div>

<?php else : ?>
    <!-- Form for the PhoneNumber field -->
    <form method="POST" class="lws_sms_phone_sms_automation_block">
        <label for="phone_sms">
            <?php esc_html_e('Enter your phone number to receive SMS: ', "lws-sms");?>
        </label>
        <div class="lws_sms_phone_sms_automation">
            <?php require plugin_dir_path(__DIR__) . "php/select_country.php";?>
            <input style="margin-top:10px; margin-right:10px" type="tel" name="phone_sms" id="phone_sms" value=<?php echo esc_attr(get_user_meta($user->ID, 'phone_sms_print', true));?>>
            <input style="margin-top:10px" class="button_disconnect" name="update_phone_sms" type="submit"
                id="update_phone_sms"
                value="<?php esc_attr_e("Update", "lws-sms")?>">
        </div>
        <small class="lws_sms_phone_sms_help_automation"><?php esc_html_e('Do not add the country code to your number.', "lws-sms");?></small>
    </form>

    <!-- Form for every SMS options -->
    <fieldset>
        <div class="lws_sms_columns_automation">
            <div class="lws_sms_left_column_automation">
                <legend>
                    <h3 class="lws_sms_column_title"> <?php esc_html_e("Check when are SMS sent to clients: ", "lws-sms");?> </h3>
                </legend>
                <?php foreach (HOOKS as $key => $hook):?>
                    <form method="POST" class="lws_sms_column_block_automation" id="<?php echo esc_attr("form_" . $hook)?>">
                        <div class="lws_sms_automation_column_subblock">
                            <header class="lws_sms_automation_column_header">
                                <span class="lws_sms_automation_column_header_title"> <?php esc_html_e(HOOKS_TRAD[$key], 'lws-sms');?> </span>
                                <?php if (empty($models)) : ?>
                                    <button disabled class="lws_sms_button_update_automation_disabled">
                                        <?php esc_attr_e("Update", "lws-sms")?>
                                    </button>
                                <?php else : ?>
                                    <button class="lws_sms_button_update_automation"
                                    id="<?php echo esc_attr("update_sms_" . $hook)?>"
                                    name="<?php echo esc_attr("update_sms_" . $hook)?>" type="submit">

                                        <span id="<?php echo esc_attr("button_update_" . $hook);?>">
                                            <?php esc_html_e("Update", "lws-sms")?>
                                        </span>

                                        <span style="vertical-align: text-top;" class="hidden"
                                        id="<?php echo esc_attr("button_gif_" . $hook)?>">
                                            <img width="15px" height="15px"
                                            src="<?php echo esc_url(plugin_dir_url(__DIR__) . 'images/loading.svg')?>">
                                        </span>

                                        <span class="hidden" class="lws_sms_button_updated_automation"
                                        id="<?php echo esc_attr("button_success_" . $hook)?>">
                                            <span>
                                                <?php esc_html_e("Saved", "lws-sms")?>
                                            </span>
                                            &nbsp;
                                            <img style="vertical-align: text-bottom;" width="15px" height="15px"
                                            src="<?php echo esc_url(plugin_dir_url(__DIR__) . 'images/check_blanc.svg')?>">
                                        </span>

                                    </button>
                                <?php endif ?>
                            </header>

                            <div class="lws_sms_main_content_block_automation">
                                <input type="hidden"
                                name="<?php echo esc_attr("hidden_hook_" . $hook);?>"
                                id="<?php echo esc_attr("hidden_hook_" . $hook);?>"
                                value="<?php echo esc_attr($hook . "|" . $key);?>">
                                <p class="lws_sms_p_content_automation">
                                    <input type="checkbox" class="lws_sms_checkboxes"
                                    id="<?php echo esc_attr('order_' . $hook);?>"
                                    name="<?php echo esc_attr('order_' . $hook);?>"
                                    <?php echo $checked_options[$hook]['is_checked'] ? "checked" : ""?>>

                                    <label for="<?php echo esc_attr('order_' . $hook);?>">
                                        <?php echo  esc_html(__('Send a SMS when: ', "lws-sms") . '"' . __(HOOKS_TRAD[$key], 'lws-sms') . '"');?>
                                    </label>
                                </p>
                                <p class="lws_sms_p_content_automation">
                                    <label for="<?php echo esc_attr('select_' . $hook);?>">
                                        <?php esc_html_e("Select a template: ", "lws-sms")?>
                                    </label>
                                </p>
                                <select class="lws_sms_automation_select"
                                name="<?php echo esc_attr('select_' . $hook);?>"
                                id="<?php echo esc_attr('select_' . $hook);?>">
                                    <option value="NO">--- ---</option>

                                    <?php foreach (get_option("lws_model_list") as $key => $value) :?>
                                        <?php if ($value['id'] == $checked_options[$hook]['used']):?>
                                            <?php $sender_model = $value['sender'];?>
                                            <option selected value="<?php echo esc_attr($value['name']);?>">
                                                <?php echo esc_html($value['name']);?>
                                            </option>
                                        <?php else:?>
                                            <?php echo esc_html($value['name']);?>
                                            <option value="<?php echo esc_attr($value['name']);?>">
                                                <?php echo esc_html($value['name']);?>
                                            </option>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                </select>

                                <?php if ($hook == 'sessions'): ?>
                                    <?php 
                                        $tab = array(
                                            "1" => "hour",
                                            "3" => "three",
                                            "6" => "six",
                                            "12" => "twelve",
                                            "24" => "twentyfour"
                                        );
                                    ?>
                                    <p class="lws_sms_p_content_automation">
                                        <label for="<?php echo esc_attr('select_time_' . $hook);?>">
                                            <?php esc_html_e("Alert after: ", "lws-sms")?>
                                        </label>
                                    </p>
                                    <select class="lws_sms_automation_select"
                                    name="<?php echo esc_attr('select_time_' . $hook);?>"
                                    id="<?php echo esc_attr('select_time_' . $hook);?>">
                                        <option value="NO">--- ---</option>
                                        <?php foreach ($tab as $key => $time):?>
                                            <?php if ($time == $timer_cron): ?>
                                                <option selected value="<?php echo esc_attr($time);?>">
                                                    <?php echo esc_html($key . __(" hours", "lws-sms"));?>
                                                </option>
                                            <?php else :?>
                                                <option value="<?php echo esc_attr($time);?>">
                                                    <?php echo esc_html($key . __(" hours", "lws-sms"));?>
                                                </option>
                                            <?php endif ?>
                                        <?php endforeach?>
                                    </select>

                                    <?php if ($timer_cron): ?>
                                        <br>
                                        <small>
                                            <label>
                                                <?php esc_html_e("Next check: ", "lws-sms");
                                                echo esc_html(gmdate("[Y-m-d] : H:i:s", wp_next_scheduled('check_sessions_cron')));?>
                                            </label>
                                        </small>
                                    <?php endif ?>
                                <?php endif ?>
                            </div>
                        </div>
                    </form>
                <?php endforeach ?>
            </div>

            <!--For each hooks, create a checkbox and its select -->
            <div class="lws_sms_right_column_automation">
                <legend>
                    <h3 class="lws_sms_column_title"> <?php esc_html_e("Check when are SMS sent to you: ", "lws-sms");?> </h3>
                </legend>
                <?php foreach (ADMIN_HOOKS as $key => $hook):?>
                    <form method="POST" class="lws_sms_column_block_automation" id="<?php echo esc_attr("form_" . $hook)?>">
                        <div class="lws_sms_automation_column_subblock">
                            <header class="lws_sms_automation_column_header">
                                <span class="lws_sms_automation_column_header_title"> <?php esc_html_e(ADMIN_HOOKS_TRAD[$key], 'lws-sms');?> </span>
                                <?php if (empty($models)) : ?>
                                    <button disabled class="lws_sms_button_update_automation_disabled">
                                        <?php esc_attr_e("Update", "lws-sms")?>
                                    </button>
                                <?php else : ?>
                                    <button class="lws_sms_button_update_automation"
                                    id="<?php echo esc_attr("update_sms_" . $hook)?>"
                                    name="<?php echo esc_attr("update_sms_" . $hook)?>" type="submit">

                                        <span id="<?php echo esc_attr("button_update_" . $hook);?>">
                                            <?php esc_html_e("Update", "lws-sms")?>
                                        </span>

                                        <span style="vertical-align: text-top;" class="hidden"
                                        id="<?php echo esc_attr("button_gif_" . $hook)?>">
                                            <img width="15px" height="15px"
                                            src="<?php echo esc_url(plugin_dir_url(__DIR__) . 'images/loading.svg')?>">
                                        </span>

                                        <span class="hidden" class="lws_sms_button_updated_automation"
                                        id="<?php echo esc_attr("button_success_" . $hook)?>">
                                            <span>
                                                <?php esc_html_e("Saved", "lws-sms")?>
                                            </span>
                                            &nbsp;
                                            <img style="vertical-align: text-bottom;" width="15px" height="15px"
                                            src="<?php echo esc_url(plugin_dir_url(__DIR__) . 'images/check_blanc.svg')?>">
                                        </span>

                                    </button>
                                <?php endif ?>
                            </header>

                            <div class="lws_sms_main_content_block_automation">
                                <input type="hidden"
                                name="<?php echo esc_attr("hidden_hook_" . $hook);?>"
                                id="<?php echo esc_attr("hidden_hook_" . $hook);?>"
                                value="<?php echo esc_attr($hook . "|" . $key);?>">
                                <p class="lws_sms_p_content_automation">
                                    <input type="checkbox" class="lws_sms_checkboxes"
                                    id="<?php echo esc_attr('order_' . $hook);?>"
                                    name="<?php echo esc_attr('order_' . $hook);?>"
                                    <?php echo $checked_options[$hook]['is_checked'] ? "checked" : ""?>>

                                    <label for="<?php echo esc_attr('order_' . $hook);?>">
                                        <?php echo  esc_html(__('Send a SMS when: ', "lws-sms") . '"' . __(ADMIN_HOOKS_TRAD[$key], 'lws-sms') . '"');?>
                                    </label>
                                </p>
                                <p class="lws_sms_p_content_automation">
                                    <label for="<?php echo esc_attr('select_' . $hook);?>">
                                        <?php esc_html_e("Select a template: ", "lws-sms")?>
                                    </label>
                                </p>
                                <select class="lws_sms_automation_select"
                                name="<?php echo esc_attr('select_' . $hook);?>"
                                id="<?php echo esc_attr('select_' . $hook);?>">
                                    <option value="NO">--- ---</option>

                                    <?php foreach (get_option("lws_model_list") as $key => $value) :?>
                                        <?php if ($value['id'] == $checked_options[$hook]['used']):?>
                                            <?php $sender_model = $value['sender'];?>
                                            <option selected value="<?php echo esc_attr($value['name']);?>">
                                                <?php echo esc_html($value['name']);?>
                                            </option>
                                        <?php else:?>
                                            <?php echo esc_html($value['name']);?>
                                            <option value="<?php echo esc_attr($value['name']);?>">
                                                <?php echo esc_html($value['name']);?>
                                            </option>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                </select>

                                <?php if ($hook == "dailies" && wp_next_scheduled('daily_logs_cron')): ?>
                                    <p class="lws_sms_p_content_automation">
                                        <label>
                                            <?php esc_html_e("Next log: ", "lws-sms");
                                            echo esc_html(gmdate("[Y-m-d] : H:i:s", wp_next_scheduled('daily_logs_cron')));?>
                                        </label>
                                    </p>
                                <?php endif ?>
                            </div>
                        </div>
                    </form>                
                <?php endforeach ?>
            </div>
        </div>
    </fieldset>

    <script>
        <?php foreach (HOOKS_BOTH_CATEGORIES as $key => $hook):?>
            var <?php echo esc_html("interval_" . $hook) ?>

            jQuery("<?php echo esc_html("#form_" . $hook)?>").submit(function(e) {
                e.preventDefault(); // avoid to execute the actual submit of the form.
                var form = jQuery(this);

                jQuery("<?php echo esc_html("#update_sms_" . $hook);?>").prop('disabled', true);
                jQuery("<?php echo esc_html("#button_success_" . $hook);?>").addClass('hidden');
                jQuery("<?php echo esc_html("#button_update_" . $hook);?>").addClass('hidden');
                jQuery("<?php echo esc_html("#button_gif_" . $hook);?>").removeClass('hidden');

                info_hook = jQuery('#hidden_hook_' + "<?php echo esc_html($hook)?>").val();
                template = jQuery('#select_' + "<?php echo esc_html($hook)?>").val();
                checked = jQuery('#order_' + "<?php echo esc_html($hook)?>").prop('checked');
                sessions = jQuery('#select_time_sessions').val();

                /**
                 * Send information about which hook, template and state was selected for a given form
                 * Whether sessions is set or not, it will be sent
                 */
                var data = {
                    action: "automation_update",
                    ajax_info_hook: info_hook,
                    ajax_template: template,
                    ajax_checked: checked,
                    ajax_sessions: sessions,
                    _ajax_nonce: '<?php echo esc_attr(wp_create_nonce('update_automation_lws_sms')); ?>'
                };

                /**
                 * Once the AJAX done, change the state of the button to "Complete" for 2.5s then revert to normal
                 * If AJAX return false, it means the option is deactivated, the checkbox is modified accordingly
                 */
                jQuery.post(ajaxurl, data, function(response) {
                    jQuery("<?php echo esc_html("#button_gif_" . $hook);?>").addClass('hidden');
                    jQuery("<?php echo esc_html("#button_success_" . $hook);?>").removeClass('hidden');

                    clearInterval(<?php echo esc_html("interval_" . $hook);?>);
                    <?php echo esc_html("interval_" . $hook)?> = setTimeout(function() {
                        jQuery("<?php echo esc_html("#button_success_" . $hook);?>").addClass('hidden');
                        jQuery("<?php echo esc_html("#button_update_" . $hook);?>").removeClass('hidden');
                        jQuery("<?php echo esc_html("#update_sms_" . $hook);?>").prop('disabled', false);
                    }, 2500);

                    if (response == -1) {
                        jQuery("<?php echo esc_html('#order_' . $hook);?>").prop('checked', false);
                    } else if (response == 1) {
                        jQuery("<?php echo esc_html('#order_' . $hook);?>").prop('checked', true);
                    }
                });
            });
        <?php endforeach ?>
    </script>
<?php endif ?>