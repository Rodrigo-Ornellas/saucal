<?php

/**
 * Plugin Name: SAU/CAl Plugin
 * Plugin URI: https://rodster.website/
 * Description: SAU/CAL practical test
 * Version: 1.0
 * Author: Rod Ornellas
 * Author URI: https://rodster.website/
 * soumtestewp1=
 */

// =======================================================
// author:  Rod Ornellas
// info:    separating some support functions
// =======================================================
$dir = plugin_dir_path(__DIR__);
include($dir . 'saucal/include/support_func.php');


// =======================================================
// author:  Rod Ornellas
// info:    code for the widget
// =======================================================
include($dir . 'saucal/include/widget.php');


// =======================================================
// author:  Rod Ornellas
// info:    CHECKS if WooCommerce is activated
// ========================================================
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    $wooisactive = true;
} else {
    $wooisactive = false;
}

// =======================================================
// author:  Rod Ornellas
// info:    FETCHES the data from the user profile
// ========================================================
function get_user_information()
{
    $current_user = wp_get_current_user();

    $data = array(
        "id" => $current_user->ID,
        "name" => $current_user->user_firstname,
        "email" => $current_user->user_email,
        "newdata" =>  $current_user->newdata,
        "alldata" => $current_user->alldata,
        "datasource" => 'unknown'
    );
    return $data;
}

// =======================================================
// author:  Rod Ornellas
// info:    POST DATA to the API
// ========================================================
function post_data_to_api($data)
{
    if (!fsockopen("www.google.com", 80)) {
        // connection issues;
        return 'nointernet';
    } else {

        $url = "http://httpbin.org/anything/" . json_encode($data);
        $curl = curl_init($url);

        // Setup cURL
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($data));
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        // Execute cURL request with all previous settings
        $response = curl_exec($curl);
        // echo $response . PHP_EOL;

        // Close cURL session
        curl_close($curl);

        // Decode the response
        $res = json_decode($response);
        //print_r($res);

        return $res;
    }
}


// =======================================================
// author:  Rod Ornellas
// info:    this function orchastrates all othe functions
// ========================================================
function display_user_data()
{
    global $wooisactive;
    // If user is LOGGED, fetch the user data from WordPress
    if (is_user_logged_in() && $wooisactive) {

        // 1) get data from user
        $data = get_user_information();

        // 2)if data not complete fetch from API
        if ($data['newdata'] == '') {

            $data['datasource'] = 'from API';
            $response = post_data_to_api($data);

            // no internet connect error
            if ($response != 'nointernet') {

                $data['newdata'] = $response->origin;
                $alldata = 'Full Response from the API:    ';
                // Loop through all values of the array
                foreach ($response as $key => $value) {

                    $alldata = $alldata . $key . " : ";
                    if (gettype($value) == 'string') {
                        $alldata =  $alldata . $value;
                    } else {
                        foreach ($value as $k => $v) {
                            $alldata = $alldata . $k . " - " . $v . " | ";
                        }
                    }
                    $alldata = $alldata . '   ';
                }
                $data['alldata'] = $alldata;
                update_user_meta($data['id'], 'newdata', $data['newdata']);
                update_user_meta($data['id'], 'alldata', $data['alldata']);
                update_user_meta($data['id'], 'datasource', $data['datasource']);

                return $data;
            } else {
                // in case of connection error
                $data['datasource'] = 'NO INTERNET CONNECTION';
                update_user_meta($data['id'], 'datasource', $data['datasource']);
                return $data;
            }
        }

        // update data source when data was fetched from database
        $data['datasource'] = 'from DATABASE';
        update_user_meta($data['id'], 'datasource', $data['datasource']);

        return $data;
    }
}



// =======================================================
// author:  Rod Ornellas
// info:    Adding CONTENT to the endpoint
// ========================================================
function rod_stuff_content()
{
    $data = display_user_data();

    wc_get_template(
        'templates/rod-stuff.php',
        $data,
        trailingslashit(__DIR__)
    );
};
add_action('woocommerce_account_rod-stuff_endpoint', 'rod_stuff_content');


// =======================================================
// author:  Rod Ornellas
// info:    modify wc_get_template to search in plugin directory
// ========================================================
add_filter('wc_get_template', 'rod_get_template', 10, 2);

function rod_get_template($located, $template_name)
{
    // if we are searching for a specific file
    if ('templates/rod-stuff.php' === $template_name) {
        // return the path to that file
        return plugin_dir_path(__FILE__) . $template_name;
    }

    // otherwise work as normal
    return $located;
}
