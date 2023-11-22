<?php

function enqueue_custom_styles() {
    // Check if the current page is using the "Dashboard" template
    if (is_page_template('dashboard.php')) {
        // Enqueue the main stylesheet
        wp_enqueue_style('dashboard-styles', get_template_directory_uri() . '/styles/dashboard.css', array(), '1.0', 'all');
    }
    // enqueue more stylesheets or add additional conditions here if needed
}

// Hook into the 'wp_enqueue_scripts' action
add_action('wp_enqueue_scripts', 'enqueue_custom_styles');

// Initializing custom REST-API endpoints for washu-ctl-review -console
include(get_template_directory() . '/backend-endpoints.php');
initCustomEndpoints();

?>