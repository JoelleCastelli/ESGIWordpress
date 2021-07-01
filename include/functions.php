<?php

// TEST
add_action('wp_footer', 'grp1_FooterAddText');
function grp1_FooterAddText() {
    echo "hello";
}

// Add menu in admin list
add_action('admin_menu', 'grp1_addAdminLink');
function grp1_addAdminLink() {
    add_menu_page(
        'ESGI TMDB',
        'ESGI TMDB',
        'manage_options',
        'esgi_tmdb',
        'esgi-tmdb/include/admin-page.php'
    );
}