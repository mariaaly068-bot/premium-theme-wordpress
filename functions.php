<?php

// Register Custom Post Type for APKs
function register_apk_post_type() {
    $args = array(
        'label' => __('APKs'),
        'public' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'has_archive' => true,
        'rewrite' => array('slug' => 'apks'),
    );
    register_post_type('apk', $args);
}
add_action('init', 'register_apk_post_type');

// Add Meta Boxes
function apk_meta_boxes() {
    add_meta_box('apk_details', 'APK Details', 'apk_meta_box_callback', 'apk', 'normal', 'high');
}
add_action('add_meta_boxes', 'apk_meta_boxes');

function apk_meta_box_callback($post) {
    $apk_version = get_post_meta($post->ID, 'apk_version', true);
    $apk_size = get_post_meta($post->ID, 'apk_size', true);
    echo '<label for="apk_version">Version:</label>';  
    echo '<input type="text" name="apk_version" value="' . esc_attr($apk_version) . '">
';  
    echo '<label for="apk_size">Size:</label>';  
    echo '<input type="text" name="apk_size" value="' . esc_attr($apk_size) . '">
';
}

// Save Meta Box data
function save_apk_meta_box_data($post_id) {
    if (array_key_exists('apk_version', $_POST)) {
        update_post_meta($post_id, 'apk_version', $_POST['apk_version']);
    }
    if (array_key_exists('apk_size', $_POST)) {
        update_post_meta($post_id, 'apk_size', $_POST['apk_size']);
    }
}
add_action('save_post', 'save_apk_meta_box_data');

// AJAX Handler for loading APKs
function load_apks_ajax() {
    $apks = new WP_Query(array(
        'post_type' => 'apk',
        'posts_per_page' => -1,
    ));
    $apk_list = array();
    while ($apks->have_posts()) {
        $apks->the_post();
        $apk_list[] = array(
            'title' => get_the_title(),
            'version' => get_post_meta(get_the_ID(), 'apk_version', true),
            'size' => get_post_meta(get_the_ID(), 'apk_size', true),
            'link' => get_permalink(),
        );
    }
    wp_send_json_success($apk_list);
}
add_action('wp_ajax_load_apks', 'load_apks_ajax');
add_action('wp_ajax_nopriv_load_apks', 'load_apks_ajax');

// Helper Functions
function get_apk_info($apk_id) {
    return array(
        'version' => get_post_meta($apk_id, 'apk_version', true),
        'size' => get_post_meta($apk_id, 'apk_size', true),
    );
}

?>