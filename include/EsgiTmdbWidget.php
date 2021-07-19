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
        extract($args);
        if ($this->tmdb->getTmdbKey()) {
            $types = ["movie" => (bool)$instance['movieChecked'], "tv" => (bool)$instance['tvChecked']];
            $tmdbRandomWork = $this->tmdb->esgi_get_random_tmdb_item($types);
            if ($tmdbRandomWork) {
                $tmdbRandomWorkPreview = $this->tmdb->esgi_get_tmdb_preview($tmdbRandomWork);
                $title = apply_filters('widget_title', $instance['title']);
                echo $before_widget . $before_title;
                echo $title;
                echo $after_title;
                echo $tmdbRandomWorkPreview;
                echo $after_widget;
            }
        }
    }

    // Back
    public function form($instance)
    {
        if($this->tmdb->getTmdbKey()) {
            // Title
            $title = $instance['title'] ?? '';
            echo '<p>
                  <label for="' . $this->get_field_name('title') . '"><b>Titre du widget&nbsp;:</b></label>
                  <input class="widefat" id="' . $this->get_field_name('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '">
              </p>'; ?>

            <!--Media selection (Movie and/or TV shows)-->
            <p>
                <b>Média :</b><br>
                <input class="checkbox" type="checkbox" <?php checked($instance['movieChecked'], 'on'); ?> id="<?= $this->get_field_id('movieChecked'); ?>" name="<?= $this->get_field_name('movieChecked'); ?>" />
                <label for="<?= $this->get_field_id('movieChecked'); ?>">Films</label>
                <input class="checkbox" type="checkbox" <?php checked($instance['tvChecked'], 'on'); ?> id="<?= $this->get_field_id('tvChecked'); ?>" name="<?= $this->get_field_name('tvChecked'); ?>" />
                <label for="<?= $this->get_field_id('tvChecked'); ?>">Séries</label>
            </p>

            <!--Movie genre selection-->
            <p>
                <b>Genres de Films :</b><br>
                <?php $this->esgi_generate_genres_checkboxes($instance, 'movie'); ?>
                <br><br><b>Genres de Séries :</b><br>
                <?php $this->esgi_generate_genres_checkboxes($instance, 'tv'); ?>
            </p>

        <?php } else {
            echo "<p>Rendez-vous sur <a href='".admin_url('/admin.php?page=esgi-tmdb')."'>la page d'administration du plugin ESGI TMDB</a> pour ajouter votre clé API TMDB</p>";
        }
    }

    public function update($new_instance, $old_instance): array
    {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['movieChecked'] = $new_instance['movieChecked'];
        $instance['movieGenres'][] = $new_instance['movieGenres'];
        $instance['tvChecked'] = $new_instance['tvChecked'];
        $instance['tvGenres'][] = $new_instance['tvGenres'];
        return $instance;
    }

    public function esgi_generate_genres_checkboxes($instance, $type)
    { ?>
        <?php $genres = $type === "movie" ? $this->tmdb->getMovieGenres() : $this->tmdb->getTvGenres();
        foreach ($genres as $id => $name) { ?>
            <input class="checkbox" type="checkbox"
                   id="<?= $this->get_field_id($id) ?>"
                   value="<?php echo $name; ?>"
                   name="<?= $type ?>Genres[]" <?php checked($instance[$id], 'on') ?> />
            <label for="<?= $this->get_field_id($id) ?>"><?= $name ?></label>
    <?php }
    }
}
