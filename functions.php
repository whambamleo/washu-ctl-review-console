<?php
function review_console_register_styles(): void
{

    wp_enqueue_style("review_console_style_css", get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'review_console_register_styles');

// Initializing custom REST-API endpoints for washu-ctl-review -console
include(get_template_directory() . '/backend-endpoints.php');
initCustomEndpoints();

?>