<?php


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