<h3 class="bloc_general_titre"> <?php esc_html_e("SMS History", "lws-sms");?> </h3>

<?php $arr = array('b' => array(), );?>

<p class="lws_sms_paragraph_history">
    <?php echo wp_kses(__("Here is your SMS History, where <b>up to 5000 SMS</b> are recorded. Please be aware that older SMS will <b>not</b> be shown.", "lws-sms"), $arr);?>
    <?php esc_html_e("Everything about your SMS is visible from here: Sender, Recipient, Content, Quantity, Status and Date.", "lws-sms");?>
    <?php esc_html_e("The integrated search bar will allows you to look for any SMS matching your query.", "lws-sms");?>
</p>

<div>
    <table id="list_history" class="lws_sms_table_history" style="min-width=1000px">
        <thead>
            <tr>
                <th>
                    <?php esc_html_e("Created", "lws-sms");?>
                </th>
                <th>
                    <?php esc_html_e("Recipient", "lws-sms");?>
                </th>
                <th>
                    <?php esc_html_e("Message", "lws-sms");?>
                </th>
                <th>
                    <?php esc_html_e("Sender", "lws-sms");?>
                </th>
                <th>
                    <?php esc_html_e("SMS", "lws-sms");?>
                </th>
                <th>
                    <?php esc_html_e("Status", "lws-sms");?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list_history as $history) :?>
                <tr>
                    <td>
                        <?php echo esc_html($history['created_at']); ?>
                    </td>
                    <td>
                        <?php if (empty($users)) : ?>
                            <?php echo esc_html($history['receiver']); ?>
                        <?php else : ?>      
                            <?php $tmp = array_search($history['receiver'], $users);
                                echo $tmp === false ? '' : esc_html($tmp) ?>
                            <br>
                            <?php echo esc_html($history['receiver']); ?>
                        <?php endif ?>
                    </td>
                    <td>
                        <?php echo(wp_kses_post($history['message'])); ?>
                    </td>
                    <td>
                        <?php echo esc_html($history['sender']); ?>
                    </td>
                    <td>
                        <?php echo esc_html($history['amount']); ?>
                    </td>
                    <td>
                        <?php if (explode("|", $history['status'])[0] == 'Success') : ?>
                            <span class="lws_sms_history_success_general">
                                <img style="padding-right:10px"
                                    src="<?php echo esc_url(plugins_url('images/check_vert.svg', __DIR__))?>"
                                    alt="Success" width="15px" height="12px">
                                <?php esc_html_e('Success', 'lws-sms'); ?>
                            </span>
                        <?php else : ?>
                            <span class="lws_sms_history_error_general">
                                <img style="padding-right:10px"
                                    src="<?php echo esc_url(plugins_url('images/croix_rouge.svg', __DIR__))?>"
                                    alt="Error" width="15px" height="12px">
                                <?php esc_html_e('Error', 'lws-sms'); ?>
                            </span>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<script>
    jQuery(document).ready(function() {
        var table = jQuery('#list_history').DataTable({
            order: [
                [0, 'desc']
            ],
            scrollY: "600px",
            scrollX: false,
            scrollCollapse: true,
            paging: false,
            fixedColumns: true,
            <?php if(get_locale() == 'fr_FR') : ?>
            language: {
                url: "<?php echo(esc_url(plugin_dir_url(__DIR__) . 'languages/fr-FR.json'))?>"
            }
            <?php endif ?>
        });
    });
</script>