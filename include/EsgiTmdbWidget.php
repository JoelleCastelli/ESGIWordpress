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
        $movieChecked = $instance[ 'movieChecked' ] ? 'true' : 'false';
        $title = apply_filters('widget_title', $instance['title']);
        echo $before_widget . $before_title;
        echo '<h2 class="widget-title subheading heading-size-3">'.$title.'</h2>';
        echo $after_title;
        echo $after_widget;
    }

    // Back
    public function form($instance)
    {
        // Title
        $title = $instance['title'] ?? '';
        echo '
        <p>
		    <label for="'.$this->get_field_name('title').'">Titre&nbsp;:</label>
			<input class="widefat" id="'.$this->get_field_name('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'">
		</p>';?>


        <!--Media selection (Movie and/or TV shows)-->
        Média :
        <p>
            <input class="checkbox" type="checkbox" <?php checked( $instance['movieChecked'], 'on' ); ?> id="<?= $this->get_field_id('movieChecked'); ?>" name="<?= $this->get_field_name('movieChecked'); ?>" />
            <label for="<?= $this->get_field_id('movieChecked'); ?>">Films</label>
            <input class="checkbox" type="checkbox" <?php checked( $instance['tvChecked'], 'on' ); ?> id="<?= $this->get_field_id('tvChecked'); ?>" name="<?= $this->get_field_name('tvChecked'); ?>" />
            <label for="<?= $this->get_field_id('tvChecked'); ?>">Séries</label>
        </p>
    <?php }

    public function update($new_instance, $old_instance)
    {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['movieChecked'] = $new_instance['movieChecked'];
        $instance['tvChecked'] = $new_instance['tvChecked'];
        return $instance;
    }
}