<?php

// TEST
add_action('wp_footer', 'esgi_tmdb_FooterAddText');
function esgi_tmdb_FooterAddText() {
    echo "hello";
}

// Add menu in admin list
add_action('admin_menu', 'esgi_tmdb_addAdminLink');
function esgi_tmdb_addAdminLink() {
    add_menu_page(
        'Configuration du plugin ESGI TMDB',  // Page title
        'ESGI TMDB',                         // Menu title
        'manage_options',                    // Capability
        'esgi-tmdb',                        // Slug
        'esgi_tmdb_config_page',             // Display callback
        'dashicons-pets'                     // Icon
    );
}

// Config page layout
function esgi_tmdb_config_page() {
    ?>
    <div class="wrap">
        <h1><?= esc_html(get_admin_page_title()) ?></h1>
        <div class="wrap">
            Pour obtenir votre clé API, consultez la <a href="https://developers.themoviedb.org/3/getting-started/introduction" target="_blank">documentation TMDB</a>.
            <form action="options.php" method="POST">
                <?php
                settings_fields('esgi_tmdb_settings');
                do_settings_sections('esgi_tmdb_settings_page');
                submit_button();
                ?>
            </form>
        </div>
    </div>
    <?php
}

// Register a setting
add_action('admin_init', 'esgi_tmdb_settings');
function esgi_tmdb_settings(){
    register_setting(
        'esgi_tmdb_settings',  // Group name
        'esgi_tmdb_settings',  // Setting name
        'esgi_tmdb_sanitize'                   // Callback function
    );

    add_settings_section(
        'esgi_tmdb_config_section',  // ID
        '',                         // Title
        '',                      // Callback
        'esgi_tmdb_settings_page'  // Page
    );

    add_settings_field(
        'public-tmdb-api-key',         // ID
        'Clé publique API TMDB',      // Title
        'esgi_tmdb_display_field',           // Callback
        'esgi_tmdb_settings_page',   // Page
        'esgi_tmdb_config_section'  // Section ID
    );
}

// Display the field
function esgi_tmdb_display_field(){
    $setting = get_option('esgi_tmdb_settings');
    $value = !empty($setting['text']) ? $setting['text'] : '';
    echo '<input class="regular-text" type="text" name="esgi_tmdb_settings[text]" value="'.esc_attr($value).'">';
}

function esgi_tmdb_sanitize( $settings ){
    $settings['text'] = ! empty( $settings['text'] ) ? sanitize_text_field( $settings['text'] ) : '';
    return $settings;
}

class EsgiTmdbWidget extends WP_Widget
{

    public function __construct()
    {
        parent::__construct(
            'esgi_tmdb_widget',
            'ESGI TMDB Widget',
            ['description' => 'Widget issu du plugin ESGI TMDB']
        );
    }

    // Front
    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);
        echo $before_widget . $before_title . $title . $after_title . '<form action="" method="post">
        <label for="email_user">Votre email :</label>
        <input id="email_user" name="email_user" type="email">
        <input type="submit">
        </form>' . $after_widget;
    }

    // Back
    public function form($instance)
    {
        $title = $instance['title'] ?? '';
        echo '<label for="' . $this->get_field_name('title') . '"> Titre : </label>
        <input id="' . $this->get_field_name('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '">';
    }

    public function update($new_instance, $old_instance)
    {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
}

// Activate widget
add_action('widgets_init', 'esgi_tmdb_widgets');
function esgi_tmdb_widgets(){
    register_widget('EsgiTmdbWidget');
}

// Shortcodes
add_shortcode('esgi-tmdb', 'esgi_tmdb_shortcode');
function esgi_tmdb_shortcode($attributes) {
    $array = shortcode_atts(
        [
            'x' => "un truc",
            'y' => "un autre truc",
        ]
        , $attributes);

    return "<p> x est {$array['x']} et y est {$array['y']}</p>";
}