<?php


// =======================================================
// author:  Rod Ornellas
// info:    This function adds a NEW FIELD to user profile
// ========================================================
function new_user_profile_fields($user)
{ ?>
    <h3><?php _e("SAU/CAL Additional Info", "blank"); ?></h3>

    <table class="form-table">
        <tr>
            <th><label for="newdata"><?php _e("SAUCAL > New Data"); ?></label></th>
            <td>
                <input type="text" name="newdata" id="newdata" value="<?php echo esc_attr(get_the_author_meta('newdata', $user->ID)); ?>" class="regular-text" /><br />
            </td>
        </tr>
        <tr>
            <th><label for="alldata"><?php _e("SAUCAL > All Data"); ?></label></th>
            <td>
                <textarea name="alldata" id="alldata" rows="10" cols="30" class="regular-text">
                    <?php echo esc_attr(get_the_author_meta('alldata', $user->ID)); ?>
                </textarea>

            </td>
        </tr>
        <tr>
            <th><label for="datasource"><?php _e("SAUCAL > Data Source"); ?></label></th>
            <td>
                <input type="text" name="datasource" id="datasource" value="<?php echo esc_attr(get_the_author_meta('datasource', $user->ID)); ?>" class="regular-text" /><br />
            </td>
        </tr>

    </table>
<?php }
add_action('show_user_profile', 'new_user_profile_fields');
add_action('edit_user_profile', 'new_user_profile_fields');

function save_new_user_profile_fields($user_id)
{
    if (empty($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'update-user_' . $user_id)) {
        return;
    }

    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    update_user_meta($user_id, 'newdata', $_POST['newdata']);
    update_user_meta($user_id, 'alldata', $_POST['alldata']);
    update_user_meta($user_id, 'datasource', $_POST['datasource']);
}
add_action('personal_options_update', 'save_new_user_profile_fields');
add_action('edit_user_profile_update', 'save_new_user_profile_fields');


// =======================================================
// author:  Rod Ornellas
// info:    Adding and ordering new endpoint
// ========================================================
add_action('init', function () {
    add_rewrite_endpoint('rod-stuff', EP_ROOT | EP_PAGES);
});

function woo_my_account_order()
{
    $myorder = array(
        'dashboard'          => __('Dashboard', 'woocommerce'),
        'rod-stuff'          => __("Rod's My Account Stuff", 'woocommerce'),
        'orders'             => __('Orders', 'woocommerce'),
        'downloads'          => __('Downloads', 'woocommerce'),
        'edit-address'       => __('Addresses', 'woocommerce'),
        'edit-account'       => __('Account Details', 'woocommerce'),
        'customer-logout'    => __('Logout', 'woocommerce'),
    );

    return $myorder;
}
add_filter('woocommerce_account_menu_items', 'woo_my_account_order');


// =======================================================
// author:  Rod Ornellas
// info:    Change TITLE of Endpoint Page
// ========================================================
function rod_woo_endpoint_title($title, $id)
{
    $title = __(
        "Rod's My Account Stuff",
        "woocommerce"
    );
    return $title;
}
add_filter('the_title', 'rod_woo_endpoint_title', 10, 2);
