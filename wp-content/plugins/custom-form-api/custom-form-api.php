<?php
/**
 * @package Custom Form API
 */
/*
Plugin Name: Custom Form API
Description: A custom API for handling form submissions.
Version: 1.0
Author: Shahid Hussain
Author URI: https://www.linkedin.com/in/shahid-hussain93/
License: GPL2
*/

defined( 'ABSPATH' ) || die( 'No direct script access allowed.' );

class CustomFormAPI
{
    private $wpdb;
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

public function activate()
{
    $table_name = $this->wpdb->prefix . 'custom_form_submissions';
    $charset_collate = $this->wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT(11) NOT NULL AUTO_INCREMENT,
        name VARCHAR(100) DEFAULT NULL,
        email VARCHAR(100) DEFAULT NULL,
        phone VARCHAR(20) DEFAULT NULL,
        gender VARCHAR(20) DEFAULT NULL,
        country VARCHAR(100) DEFAULT NULL,
        file_path VARCHAR(255) DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}


   
public function init()
    {
        add_shortcode( 'registration_form', array( $this, 'cfapi_display_form' ) );
    }

public function cfapi_display_form()
    {
        ob_start();
        include plugin_dir_path( __FILE__ ) . 'views/form.php';
        return ob_get_clean();
    }
public function enqueue_assets() {
    wp_enqueue_style(
        'bootstrap-css',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css'
    );

    wp_enqueue_script(
        'bootstrap-js',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
        array('jquery'),
        null, // for versioning of bootstrap
        true // true to load in footer  
    );

      wp_enqueue_script(
        'cfapi-form-script',
        plugin_dir_url( __FILE__ ) . 'js/form-script.js',
        array('jquery'), // dependencies    
        null, // version number
        true // load in footer
    );

    
    wp_localize_script('cfapi-form-script', 'cfapi_obj', [
        'ajax_url' => admin_url('admin-ajax.php'),          // sends the AJAX URL
        'nonce'    => wp_create_nonce('cfapi_nonce_action') // generates a secure nonce token
    ]);
}

function cfapi_handle_form_submission() {
    check_ajax_referer('cfapi_nonce_action', 'security');
    $fields = [
        'name'    => 'Name is required.',
        'email'   => 'email is required.',
        'phone'   => 'Phone number is required.',
        'gender'  => 'Gender is required.',
        'country' => 'Country is required.'
    ];

    $errors = [];
    $data = [];

    foreach ($fields as $field => $errorMsg) {
        $value = isset($_POST[$field]) ? sanitize_text_field($_POST[$field]) : '';
        if (empty($value)) {
            $errors[] = $errorMsg;
        } else {
            $data[$field] = $value;
        }

        if (isset($field) && $field === "email" && !is_email($value)) {
            $errors[] = 'Email format is invalid.';
        }
    }

    if (empty($_FILES['image_path']['name'])) {
        $errors[] = 'Profile picture is required.';
    } else {
        $allowed_types = ['image/jpeg', 'image/png'];
        $file_type = $_FILES['image_path']['type'];
        $file_size = $_FILES['image_path']['size'];

        if (!in_array($file_type, $allowed_types)) {
            $errors[] = 'Only JPG and PNG files are allowed.';
        }

        if ($file_size > 2 * 1024 * 1024) { // 2MB limit
            $errors[] = 'Image size should not exceed 2MB.';
        }
    }


        if (!empty($errors)) {
            wp_send_json_error(['message' => implode('<br>', $errors)]);
            wp_die();
        }

        // ✅ Handle file upload
        $file_url = '';
        if (!empty($_FILES['image_path']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            $attachment_id = media_handle_upload('image_path', 0);
            if (is_wp_error($attachment_id)) {
                wp_send_json_error(['message' => 'File upload failed.']);
                wp_die();
            }
            $file_url = wp_get_attachment_url($attachment_id);
        }

        // insert the data into the database
        $data['file_path'] = $file_url; // Add file path to data array
        $this->wpdb->insert(
            $this->wpdb->prefix . 'custom_form_submissions',
            $data,
            array_fill(0, count($data), '%s') // will add here placeholders for each field
        );

        wp_send_json_success(['message' => 'Registration successful!']);
        wp_die();
}

public function admin_menu() {
    add_menu_page(
        'Custom Form Submissions',
        'Form Submissions',
        'manage_options',
        'custom-form-submissions',
        array($this, 'display_custom_form_submissions'),
        'dashicons-list-view',
        99
    );
}
public function display_custom_form_submissions() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_form_submissions';

    // ✅ Check if the table exists
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;

    echo '<div class="wrap container mt-5">';
    echo '<h1 class="mb-4">Form Submissions</h1>';

    if ($table_exists) {
        $submissions = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC");

        if (count($submissions) > 0) {
            include(plugin_dir_path(__FILE__) . 'views/form-data.php');
        } else {
            echo '<p>No submissions found.</p>';
        }
    } else {
        echo '<p class="text-danger"><strong>Database table not found.</strong> Please activate the plugin again to recreate the table.</p>';
    }

    echo '</div>';
}



public function enqueue_admin_assets($hook) {
    // Load only on your custom admin page
    if ($hook !== 'toplevel_page_custom-form-submissions') {
        return;
    }

    wp_enqueue_style(
        'bootstrap-admin-css',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css'
    );
    
  }

}


// check if the class exists
if ( class_exists( 'CustomFormAPI' ) ) 
{
    // create an instance of the class
    $customFormApi = new CustomFormAPI();

    // add the action to initialize the plugin
    add_action( 'init', [ $customFormApi, 'init' ] );
    // add the action to enqueue assets
    add_action( 'wp_enqueue_scripts', [ $customFormApi, 'enqueue_assets']);
    // For logged in users
    add_action('wp_ajax_cfapi_submit_form', [ $customFormApi, 'cfapi_handle_form_submission']);

    // for non logged in users
    add_action('wp_ajax_nopriv_cfapi_submit_form', [ $customFormApi, 'cfapi_handle_form_submission']);

    // add the action to create admin menu
    add_action('admin_menu', [ $customFormApi, 'admin_menu']);
    // add the action to enqueue admin assets
    add_action('admin_enqueue_scripts', [ $customFormApi, 'enqueue_admin_assets']);

}

register_activation_hook( __FILE__, [ $customFormApi, 'activate' ] );