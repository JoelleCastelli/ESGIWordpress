<?php


class EsgiTmdbWidget extends WP_Widget
{

    private EsgiTmdb $tmdb;

    public function __construct()
    {
        parent::__construct(
            'esgi_tmdb_widget',
            'ESGI TMDB',
            ['description' => 'Widget issu du plugin ESGI TMDB']
        );
        $this->tmdb = new EsgiTmdb();
    }

    // Front
    public function widget($args, $instance)
    {
        $types = ["movie" => (bool)$instance['movieChecked'], "tv" => (bool)$instance['tvChecked']];
        $tmdbRandomWork = $this->tmdb->esgi_get_random_tmdb_item($types);
        $tmdbRandomWorkPreview = $this->tmdb->esgi_get_tmdb_preview($tmdbRandomWork);

        $title = apply_filters('widget_title', $instance['title']);
        echo $before_widget . $before_title;
        echo '<h2 class="widget-title subheading heading-size-3">'.$title.'</h2>';
        echo $after_title;
        echo $tmdbRandomWorkPreview;
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
        <p>
            Média :<br>
            <input class="checkbox" type="checkbox" <?php checked( $instance['movieChecked'], 'on' ); ?> id="<?= $this->get_field_id('movieChecked'); ?>" name="<?= $this->get_field_name('movieChecked'); ?>" />
            <label for="<?= $this->get_field_id('movieChecked'); ?>">Films</label>
            <input class="checkbox" type="checkbox" <?php checked( $instance['tvChecked'], 'on' ); ?> id="<?= $this->get_field_id('tvChecked'); ?>" name="<?= $this->get_field_name('tvChecked'); ?>" />
            <label for="<?= $this->get_field_id('tvChecked'); ?>">Séries</label>
        </p>

        <!--Movie genre selection-->
        <p>
            Genres :<br>
            <?php $this->esgi_generate_movie_genres_checkboxes($instance);?>
        </p>
    <?php }

    public function update($new_instance, $old_instance): array
    {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['movieChecked'] = $new_instance['movieChecked'];
        $instance['tvChecked'] = $new_instance['tvChecked'];
        $instance['tvGenres'][] = $new_instance['tvGenres'];
        return $instance;
    }

    public function esgi_generate_movie_genres_checkboxes($instance) { ?>
        <?php foreach ($this->tmdb->getMovieGenres() as $id => $name) { ?>
            <input class="checkbox" type="checkbox" id="<?= $this->get_field_id('genre_'.$id) ?>" name="tvGenres[]" <?php checked($instance['genre_'.$id], 'on') ?> />
            <label for="<?= $this->get_field_id('genre_'.$id) ?>"><?= $name ?></label>
        <?php }
    }

}