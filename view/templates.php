<div class="lws_sms_templates_button">
<h3 class="bloc_general_titre" style="margin-bottom:0px; padding-left:0px"><?php esc_html_e("Template management", "lws-sms");?>
</h3>
<button type="button" class="button_disconnect" data-open="models-creator">
    <img style="vertical-align:text-top; margin-right:5px" src="<?php echo esc_url(plugins_url('images/plus.svg', __DIR__))?>" alt="LWS Plus (+)" width="15px" height="15px">
    <?php esc_html_e("Add a new template", "lws-sms")?></button>
</div>

<?php if ($model_error) :?>
<div class="error_message">
    <?php echo esc_html($model_error_message) ?>
</div>
<?php endif ?>

<p class="lws_sms_paragraph" style="margin-bottom:0px">
    <?php esc_html_e("This tab lets you create your own templates to send SMS. Click on the button below to open a new window where you will be able to create one. Please note that you will need an active sender ID to use our services.", "lws-sms"); ?>
</p>

<br>

<div> 
    <h3 class="bloc_general_titre lws_sms_title_alt"><?php esc_html_e("Help for the template tags:", "lws-sms")?></h3>
    <ul style="list-style: inside;">
        <li style="font-size:23px; padding-left:30px;"><span class="lws_sms_paragraph lws_sms_p_alt"><?php esc_html_e("'Client' tags are used when your SMS is adressed to at least one client", "lws-sms")?></span>
        </li>
        <li style="font-size:23px; padding-left:30px;"><span class="lws_sms_paragraph lws_sms_p_alt"><?php esc_html_e("'Order' tags are used when your SMS is about an order in general", "lws-sms")?></span>
        </li>
        <li style="font-size:23px; padding-left:30px;"><span class="lws_sms_paragraph lws_sms_p_alt"><?php esc_html_e("'Product' tags are to be used for the <stock alert> SMS or the <lost cart> SMS", "lws-sms")?></span>
        </li>
        <li style="font-size:23px; padding-left:30px;"><span class="lws_sms_paragraph lws_sms_p_alt"><?php esc_html_e("'Coupon' tags are used when your SMS is about a coupon", "lws-sms")?></span>
        </li>
        <li style="font-size:23px; padding-left:30px;"><span class="lws_sms_paragraph lws_sms_p_alt"><?php esc_html_e("'Miscelleanous' tags can be used no matter the SMS", "lws-sms")?></span>
        </li>
        <li style="font-size:23px; padding-left:30px;"><span class="lws_sms_paragraph lws_sms_p_alt"><?php esc_html_e("'Daily Logs' tags can be used when sending daily SMS to yourself", "lws-sms")?></span>
        </li>
    </ul>
</div>
<br>

<div class="modal <?php echo $edit_model ? esc_attr('is-visible') : ""?>"
    id="models-creator">
    <div class="modal-dialog">
        <header class="modal-header">
            <h1> <?php echo $edit_model ? esc_html__("Edit template: ", "lws-sms") . esc_html($edit_model['name']) : esc_html__("Create your own template", "lws-sms")?>
            </h1>
        </header>
        <section>
            <form class="lws_sms_main_modal" method="POST">
                <div class="lws_sms_modal_main">
                    <div class="lws_sms_modal_left_side">
                        <div class="lws_sms_modal_left_up">
                            <label class="lws_sms_label_modal_top" for="model_name">
                                <?php esc_html_e("Template name:", "lws-sms")?>
                            </label>
                            <input class="lws_sms_select_variable" type="text"
                                placeholder="<?php esc_html_e("Enter a name", "lws-sms")?>"
                                name="model_name" required
                                value="<?php echo $edit_model ? esc_attr($edit_model['name']) : ""?>">
                        </div>

                        <div class="lws_sms_modal_right_up">
                            <label class="lws_sms_label_modal_top" for="senders_select">
                                <?php esc_html_e("Sender ID selection:", "lws-sms")?>
                            </label>
                            <select class="lws_sms_select_variable" name="senders_select">
                                <?php foreach ($sender_ids as $key => $value) :?>
                                <?php if ($edit_model && $edit_model['sender'] == $value) : ?>
                                <option selected
                                    value="<?php echo esc_attr($value);?>">
                                    <?php echo esc_html($value);?>
                                </option>
                                <?php else : ?>
                                <option
                                    value="<?php echo esc_attr($value);?>">
                                    <?php echo esc_html($value);?>
                                </option>
                                <?php endif ?>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>

                    <div class="lws_sms_modal_right_side">
                        <div class="lws_sms_modal_left_down">
                            <label class="lws_sms_label_modal" for="tags_select">
                                <?php esc_html_e("Insert a tag: ", "lws-sms")?>
                            </label>                            
                            <select class="lws_sms_select_variable" id="tags_select" name="tags_select">
                                <option selected value="user"><?php esc_html_e("Client", "lws-sms")?>
                                </option>
                                <option value="order"><?php esc_html_e("Order", "lws-sms")?>
                                </option>
                                <option value="product"><?php esc_html_e("Product", "lws-sms")?>
                                </option>
                                <option value="coupon"><?php esc_html_e("Coupon", "lws-sms")?>
                                </option>
                                <option value="base"><?php esc_html_e("Miscellaneous", "lws-sms")?>
                                </option>
                                <option value="daily"><?php esc_html_e("Daily logs", "lws-sms")?>
                                </option>
                            </select>
                            
                            <div class="lws_sms_variable_button_list" id="variables">
                                <span id="user">
                                    <button class="lws_sms_variable_button" type="button" value="[[Nom]]"
                                        onclick="lws_sms_addVar(this)"><?php esc_html_e("Name", "lws-sms");?></button>
                                    <button class="lws_sms_variable_button" type="button" value="[[Prenom]]"
                                        onclick="lws_sms_addVar(this)"><?php esc_html_e("First name", "lws-sms");?></button>
                                    <button class="lws_sms_variable_button" type="button" value="[[Adresse]]"
                                        onclick="lws_sms_addVar(this)"><?php esc_html_e("Address", "lws-sms");?></button>
                                </span>

                                <span id="order" class="hidden_lws">
                                    <button class="lws_sms_variable_button" type="button" value="[[Prix]]"
                                        onclick="lws_sms_addVar(this)"><?php esc_html_e("Price", "lws-sms");?></button>
                                    <button class="lws_sms_variable_button" type="button" value="[[NumCmde]]"
                                        onclick="lws_sms_addVar(this)"><?php esc_html_e("Num Cmd", "lws-sms");?></button>
                                </span>

                                <span id="product" class="hidden_lws">
                                    <button class="lws_sms_variable_button" type="button" value="[[NomProduit]]"
                                        onclick="lws_sms_addVar(this)"><?php esc_html_e("Product Name", "lws-sms");?></button>
                                    <button class="lws_sms_variable_button" type="button" value="[[PrixProduit]]"
                                        onclick="lws_sms_addVar(this)"><?php esc_html_e("Product Price", "lws-sms");?></button>
                                    <button class="lws_sms_variable_button" type="button" value="[[DescriptionProduit]]"
                                        onclick="lws_sms_addVar(this)"><?php esc_html_e("Product Description", "lws-sms");?></button>
                                    <button class="lws_sms_variable_button" type="button" value="[[ListePanier]]"
                                        onclick="lws_sms_addVar(this)"><?php esc_html_e("Cart Content", "lws-sms");?></button>
                                </span>

                                <span id="coupon" class="hidden_lws">
                                    <button class="lws_sms_variable_button" type="button" value="[[CodeCoupon]]"
                                        onclick="lws_sms_addVar(this)"><?php esc_html_e("Coupon Code", "lws-sms");?></button>
                                    <button class="lws_sms_variable_button" type="button" value="[[DescriptionCoupon]]"
                                        onclick="lws_sms_addVar(this)"><?php esc_html_e("Coupon Description", "lws-sms");?></button>
                                    <button class="lws_sms_variable_button" type="button" value="[[ValeurCoupon]]"
                                        onclick="lws_sms_addVar(this)"><?php esc_html_e("Coupon Value", "lws-sms");?></button>
                                </span>

                                <span id="base" class="hidden_lws">
                                    <button class="lws_sms_variable_button" type="button" value="[[Date]]"
                                        onclick="lws_sms_addVar(this)"><?php esc_html_e("Date", "lws-sms");?></button>
                                    <button class="lws_sms_variable_button" type="button" value="[[NomSite]]"
                                        onclick="lws_sms_addVar(this)"><?php esc_html_e("Website Name", "lws-sms");?></button>
                                    <button class="lws_sms_variable_button" type="button" value="[[URLSite]]"
                                        onclick="lws_sms_addVar(this)"><?php esc_html_e("Website URL", "lws-sms");?></button>
                                </span>

                                <span id="daily" class="hidden_lws">
                                    <button class="lws_sms_variable_button" type="button" value="[[PanierAbandonnes]]"
                                        onclick="lws_sms_addVar(this)"><?php esc_html_e("Lost Carts", "lws-sms");?></button>
                                    <button class="lws_sms_variable_button" type="button" value="[[MontantTotal]]"
                                        onclick="lws_sms_addVar(this)"><?php esc_html_e("Amount", "lws-sms");?></button>
                                    <button class="lws_sms_variable_button" type="button" value="[[Commandes]]"
                                        onclick="lws_sms_addVar(this)"><?php esc_html_e("Orders", "lws-sms");?></button>
                                    <button class="lws_sms_variable_button" type="button" value="[[NouveauxClients]]"
                                        onclick="lws_sms_addVar(this)"><?php esc_html_e("New Clients", "lws-sms");?></button>
                                    <button class="lws_sms_variable_button" type="button" value="[[MeilleureVente]]"
                                        onclick="lws_sms_addVar(this)"><?php esc_html_e("Best seller", "lws-sms");?></button>
                                </span>
                            </div>
                        </div>

                        <div class="lws_sms_modal_right_down">
                            <label class="lws_sms_label_modal" for="sms_model"><?php esc_html_e("Write your SMS template:", "lws-sms")?></label>
                            <textarea rows="6" class="lws_sms_select_variable" id="sms_model" name="sms_model" required
                                placeholder="<?php esc_attr_e("Write your SMS template here. Use the variables on the side to further personnalize your SMS.", "lws-sms")?>"><?php echo $edit_model ? esc_html($edit_model['message']) : ""?></textarea>
                            <div class="lws_sms_modal_sms_list">
                                <span class="lws_sms_modal_sms_list_top"><small id="number_char">0/160 </small><small><?php esc_html_e(" character(s)", "lws-sms");?></small></span>
                                <span><small>SMS:</small><small><input class="nb_sms" name="number_message" id="number_message" readonly value=1></small></span>
                            </div>
                            <small class="lws_sms_modal_help">
                                <?php esc_html_e("If you are using tags, please be aware that this value is but an approximation as the real text behind the tags could be longer or shorter.", "lws-sms")?>
                            </small>
                        </div>
                    </div>   
                </div>
                <div class="lws_sms_modal_button">
                    <button class="lws_sms_save_template_button" name="validate_model" type="submit">
                    <img style="vertical-align:sub; margin-right:5px"
                            src="<?php echo esc_url(plugins_url('images/enregistrer.svg', __DIR__))?>"
                            alt="LWS Cache Logo" width="20px" height="20px">
                        <?php esc_attr_e("Save the template", "lws-sms")?>
                    </button>
                    <button type="button" class="lws_sms_close_template_button" data-close="models-creator"><?php esc_html_e("Close", "lws-sms")?></button>
                </div>

                <?php if ($edit_model) :?>
                <input name="editing" id="editing" type="hidden" value=true>
                <input name="model_key" id="model_key" type="hidden"
                    value="<?php echo esc_attr($edit_key)?>">
                <input name="model_id" id="model_id" type="hidden"
                    value="<?php echo esc_attr($edit_id)?>">
                <?php endif ?>

            </form>
        </section>
    </div>
</div>

<script>
    let typingTimer; //timer identifier
    let doneTypingInterval = 200; //time in ms
    var sms = jQuery('#sms_model');

    function lws_sms_charSMS(unicode = false) {
        if (unicode) {
            length = [70, 134, 201, 268, 335, 402, 469, 536, 603];
        } else {
            length = [160, 306, 459, 612, 765, 918, 1071, 1224, 1377];
        }
        if (sms.val().length <= length[0]) {
            jQuery("#number_char").text(sms.val().length + "/" + length[0]);
            jQuery("#number_message").val(1);

        } else if (sms.val().length > length[length.length - 1]) {
            jQuery("#number_char").text(sms.val().length + "/" + length[length.length - 1] + "+");
            jQuery("#number_message").val(length.length + '+');
        } else {
            for (var i = 1; i < length.length; i++) {
                if (sms.val().length > length[i - 1] && sms.val().length <= length[i]) {
                    jQuery("#number_char").text(sms.val().length + "/" + length[i]);
                    jQuery("#number_message").val(i + 1);
                }
            }
        }
    }

    jQuery(function() { // this will be called when writing in the TA
        jQuery("#sms_model").keyup(function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(lws_sms_doneTyping, doneTypingInterval);
        });
    });


    function lws_sms_doneTyping() {
        var txt = jQuery("#sms_model").val();
        var data = {
            _ajax_nonce: '<?php echo esc_attr(wp_create_nonce('check_unicode_lws_sms')); ?>',
            action: "unicodeChecker",
            ajax_text: txt,
        };

        jQuery.post(ajaxurl, data, function(response) {
            lws_sms_charSMS(response);
        });
    }

    //Add variable at position in the textarea
    function lws_sms_addVar(e) {
        var sms = jQuery('#sms_model');
        var smsValue = sms.val();
        var variable = e.value;
        var add_at_position = sms.prop("selectionEnd");
        var nextSelectionEnd = (add_at_position + variable.length);

        sms.val(
            smsValue.slice(0, add_at_position) +
            variable +
            smsValue.slice(add_at_position)
        );

        sms
            .prop("selectionStart", nextSelectionEnd)
            .prop("selectionEnd", nextSelectionEnd)
            .focus();

        var txt = jQuery("#sms_model").val();
        var data = {
            _ajax_nonce: '<?php echo esc_attr(wp_create_nonce('check_unicode_lws_sms')); ?>',
            action: "unicodeChecker",
            ajax_text: txt,
        };

        jQuery.post(ajaxurl, data, function(response) {
            lws_sms_charSMS(response);
        });
    }

    document.querySelector("[data-open]").addEventListener("click", function() {
        document.getElementById(this.dataset.open).classList.add("is-visible");
        jQuery('body').css({
            'overflow': 'hidden'
        });
    });
    document.querySelector("[data-close]").addEventListener("click", function() {
        jQuery('#models-creator').removeClass("is-visible");
        jQuery('body').css({
            'overflow': 'visible'
        });
        <?php if ($edit_model) : ?>
        location.reload();
        <?php endif ?>
    });

    document.addEventListener("click", e => {
        if (e.target == document.querySelector(".modal.is-visible")) {
            document.querySelector(".modal.is-visible").classList.remove("is-visible");
            jQuery('body').css({
                'overflow': 'visible'
            });
            <?php if ($edit_model) : ?>
            location.reload();
            <?php endif ?>
        }
    });

    document.addEventListener("keyup", e => {
        if (e.key == "Escape" && document.querySelector(".modal.is-visible")) {
            document.querySelector(".modal.is-visible").classList.remove("is-visible");
            jQuery('body').css({
                'overflow': 'visible'
            });
            <?php if ($edit_model) : ?>
            location.reload();
            <?php endif ?>
        }
    });

    <?php if ($edit_model) : ?>
    jQuery('body').css({
        'overflow': 'hidden'
    });
    lws_sms_charSMS();
    <?php endif ?>

    var tags = document.querySelector("#tags_select");
    tags.addEventListener('change', function() {
        jQuery("#user").addClass('hidden_lws');
        jQuery("#product").addClass('hidden_lws');
        jQuery("#order").addClass('hidden_lws');
        jQuery("#coupon").addClass('hidden_lws');
        jQuery("#base").addClass('hidden_lws');
        jQuery("#daily").addClass('hidden_lws');
        jQuery("#" + this.value).removeClass('hidden_lws');
    });
</script>

<?php if ($models) : ?>
<h3 class="bloc_templates_titre"><?php esc_html_e("Templates", "lws-sms");?>
</h3>

<table id="list_model" class="lws_sms_table_templates" style="width:100%">
    <thead>
        <tr class="active-row">
            <th class="lws_sms_template_table_th">
                <?php esc_html_e("Name", "lws-sms");?>
            </th>
            <th class="lws_sms_template_table_th">
                <?php esc_html_e("Sender", "lws-sms");?>
            </th>
            <th class="lws_sms_template_table_th_25">
                <?php esc_html_e("Used", "lws-sms");?>
            </th>
            <th class="lws_sms_template_table_th_100">
                <?php esc_html_e("Actions", "lws-sms");?>
            </th>
    </thead>
    <tbody>
        <?php foreach ($models as $model) :?>
        <tr>
            <td>
                <?php echo esc_html($model['name']); ?>
            </td>
            <td>
                <?php echo esc_html($model['sender']); ?>
            </td>
            <td class="model-in-use">
                <?php foreach ($model['in_use'] as $id => $used): ?>
                <?php if($used == "NO"): ?>
                <span><?php esc_html_e("Not used", "lws-sms");?></span><br>
                <?php elseif (substr($used, 0, 5) === "admin") : ?>
                <?php echo "<span>" . esc_html__(ADMIN_HOOKS_TRAD[explode("|", $id)[1]], 'lws-sms') . "</span><br>"?>
                <?php else : ?>
                <?php echo "<span>" . esc_html__(HOOKS_TRAD[$id], 'lws-sms') . "</span><br>"?>
                <?php endif ?>
                <?php endforeach ?>
            </td>
            <td>
                <form class='form_table' method="POST">                    
                    <input id="model_name" name="model_name" type="hidden"
                        value="<?php echo esc_attr($model['name'])?>">
                    <span>
                        <button id="edit_model" name="edit_model" class="lws_sms_model_edit" type="submit">
                            <img style="padding-right:5px; vertical-align:text-top"
                                src="<?php echo esc_url(plugins_url('images/editer.svg', __DIR__))?>"
                                alt="Error" width="15px" height="15px">
                            <?php esc_attr_e("Edit", "lws-sms");?>
                        </button>
                    </span>
                        <button id="delete_model" name="delete_model"
                            onclick="return confirm('<?php esc_html_e('Are you sure you want to delete ?', 'lws-sms');?>')"
                            class="lws_sms_model_delete" type="submit">                        
                            <img style="padding-right:5px; vertical-align:sub"
                                src="<?php echo esc_url(plugins_url('images/supprimer.svg', __DIR__))?>"
                                alt="Error" width="20px" height="20px">
                            <?php esc_attr_e("Delete", "lws-sms");?>
                        </button>
                </form>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>

<script>

    jQuery(document).ready(function($) {
        jQuery('#list_model').DataTable({
            columnDefs: [
                { orderable: false, targets: -1 }
            ],
            scrollY: "600px",
            scrollX: true,
            scrollCollapse: true,
            paging: false,
            fixedColumns: true,

            <?php if(get_locale() == 'fr_FR') : ?>
            language: {
                url: "<?php echo esc_url(plugin_dir_url(__DIR__) . 'languages/fr-FR.json')?>"
            }
            <?php endif ?>
        });
    });
</script>
<?php endif ?>
