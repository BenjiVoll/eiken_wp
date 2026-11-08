<?php
get_header();

if (function_exists('is_woocommerce') && is_woocommerce()) {
    woocommerce_content();
} else {
    if (have_posts()) :
        while (have_posts()) : the_post();
            the_content();
        endwhile;
    endif;
}

get_footer();
