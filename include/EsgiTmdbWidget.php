<?php


class EsgiTmdbWidget extends WP_Widget
{

    private $tmdb;

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
            $genres = $this->esgi_get_formated_genres($types, $instance);
            if ($this->tmdb->esgi_check_types_array($types)) {
                $tmdbRandomWork = $this->tmdb->esgi_get_random_tmdb_item($types, $genres);
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
    }

    // Back
    public function form($instance)
    {
        if ($this->tmdb->getTmdbKey()) {
            // Title
            $title = isset($instance['title']) ? $instance['title'] : '';
            echo '<p>
                  <label for="' . $this->get_field_name('title') . '"><b>Titre du widget&nbsp;:</b></label>
                  <input class="widefat" id="' . $this->get_field_name('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '">
              </p>'; ?>

            <!--Media selection (Movie and/or TV shows)-->
            <p>
                <b>Média :</b><br>
                <input class="checkbox itemType" type="checkbox" <?php if (isset($instance['movieChecked'])) checked($instance['movieChecked'], 'on'); ?> id="<?= $this->get_field_id('movieChecked'); ?>" name="<?= $this->get_field_name('movieChecked'); ?>" />
                <label for="<?= $this->get_field_id('movieChecked'); ?>">Films</label>

                <input class="checkbox itemType" type="checkbox" <?php if (isset($instance['tvChecked'])) checked($instance['tvChecked'], 'on'); ?> id="<?= $this->get_field_id('tvChecked'); ?>" name="<?= $this->get_field_name('tvChecked'); ?>" />
                <label for="<?= $this->get_field_id('tvChecked'); ?>">Séries</label>
            </p>

            <p style="font-size: small;">
                ⚠ <span style="color: red; font-weight: bold">Attention</span> ⚠<br>
                Les genres ne sont pas séparés mais <b>cumulés</b> : si, pour les films, vous sélectionnez les genres "Comédie" et "Horreur",
                votre page affichera les films qui correspondent <b>au moins </b> à ces deux genres, en l'occurrence des comédies d'horreur.
                Attention à ne pas sélectionner trop de genres sous peine de ne trouver aucun résultat.
            </p>

            <!--Movie genre selection-->
            <p>
                <b>Genres de Films :</b><br>
                <?php $this->esgi_generate_genres_checkboxes($instance, 'movie') ?>
            </p>

            <!--TV genre selection-->
            <p>
                <b>Genres de Séries :</b><br>
                <?php $this->esgi_generate_genres_checkboxes($instance, 'tv') ?>
            </p>
        <?php } else {
            echo "<p>Rendez-vous sur <a href='" . admin_url('/admin.php?page=esgi-tmdb') . "'>la page d'administration du plugin ESGI TMDB</a> pour ajouter votre clé API TMDB</p>";
        }
    }

    public function update($new_instance, $old_instance)
    {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['movieChecked'] = isset($new_instance['movieChecked']) ? $new_instance['movieChecked'] : '';
        $instance['tvChecked'] = isset($new_instance['tvChecked']) ? $new_instance['tvChecked'] : '';
        // Movie genres
        foreach ($this->tmdb->getMovieGenres() as $id => $name) {
            $instance['movie-' . $id] = isset($new_instance['movie-' . $id]) ? $new_instance['movie-' . $id] : '';
        }
        // TV genres
        foreach ($this->tmdb->getTvGenres() as $id => $name) {
            $instance['tv-' . $id] = isset($new_instance['tv-' . $id]) ? $new_instance['tv-' . $id] : '';
        }
        return $instance;
    }

    public function esgi_get_formated_genres($types, $instance)
    {
        $genres = [];
        foreach ($types as $type => $typeActivated) {
            if ($typeActivated == true) {
                foreach ($instance as $key => $value) {
                    $exp_key = explode('-', $key);
                    if ($exp_key[0] == $type && $value != '') {
                        $genres[$type][] = $exp_key[1];
                    }
                }
                // Turn genres arrays into strings
                if (isset($genres[$type]))
                    $genres[$type] = implode(',', $genres[$type]);
            }
        }
        return $genres;
    }

    public function esgi_generate_genres_checkboxes($instance, $type)
    { ?>
        <?php $genres = $type === "movie" ? $this->tmdb->getMovieGenres() : $this->tmdb->getTvGenres();
        foreach ($genres as $id => $name) { ?>
            <input class="checkbox" type="checkbox" <?php if (isset($instance[$type . '-' . $id])) checked($instance[$type . '-' . $id], 'on'); ?> id="<?= $this->get_field_id($type . '-' . $id); ?>" name="<?= $this->get_field_name($type . '-' . $id); ?>" />
            <label for="<?= $this->get_field_id($type . '-' . $id); ?>"><?= $name ?></label>
<?php }
    }
}
