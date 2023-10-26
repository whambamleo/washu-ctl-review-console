<?php
function review_console_register_styles(): void
{

    wp_enqueue_style("review_console_style_css", get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'review_console_register_styles');
?>