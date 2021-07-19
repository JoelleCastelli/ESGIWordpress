<?php


class EsgiTmdb
{

    protected string $tmdbApiBaseUrl = "https://api.themoviedb.org/3/";
    protected string $tmdbImageUrl = "https://image.tmdb.org/t/p/w200";
    protected string $tmdbBaseUrl = "https://www.themoviedb.org/";
    protected string $language;
    protected string $region;
    protected ?string $tmdbKey = null;
    protected array $tvGenres = [];
    protected array $movieGenres = [];


    public function __construct()
    {
        $this->language = str_replace("_", "-", get_locale());
        $this->region = substr($this->language, 0, 2);
        $this->tmdbKey = get_option('esgi_tmdb_settings')['tmdb-key'] ?? '';
        if($this->tmdbKey) {
            $this->esgi_get_genres('tv');
            $this->esgi_get_genres('movie');
        }
    }

    public function getTvGenres(): array
    {
        return $this->tvGenres;
    }

    public function getMovieGenres(): array
    {
        return $this->movieGenres;
    }

    public function getTmdbKey()
    {
        return $this->tmdbKey;
    }

    public function setTmdbKey($tmdbKey): void
    {
        $this->tmdbKey = $tmdbKey;
    }

    public function esgi_get_random_tmdb_item($types, $genres = null){
        $urlArray = $this->esgi_generate_url_array($types, $genres);
        if($urlArray) {
            $list = [];
            foreach ($urlArray as $type => $urls) {
                foreach ($urls as $page => $url) {
                    $responseBody = wp_remote_retrieve_body(wp_remote_get($url));
                    $results = json_decode($responseBody)->results;
                    foreach ($results as $index => $work) {
                        $work = (array)$work;
                        $work['type'] = $type;
                        $work = (object)$work;
                        $results[$index] = $work;
                    }
                    $list = array_merge($list, $results);
                }
            }
            if(!empty($list)) return $list[rand(0, count($list) - 1)];
        }
        return false;
    }

    public function esgi_get_tmdb_preview($work): string
    {
        $name = $work->title ?? $work->name;
        $poster = $this->tmdbImageUrl.$work->poster_path;
        $type = $work->type == 'tv' ? "Série" : "Film";
        $url = $this->tmdbBaseUrl.$work->type.'/'.$work->id;
        $date = date("Y", strtotime($work->release_date ?? $work->first_air_date));

        $preview = "<a href='$url' target='_blank'><div class='esgi_tmdb_preview' style='text-align: center'>";
        $preview .= "<div class='esgi_tmdb_preview_poster'><img src='$poster'/></div>";
        $preview .= "<div class='esgi_tmdb_preview_name'>$name</div>";
        $preview .= "<div class='esgi_tmdb_preview_type'>$type ($date)</div>";
        $preview .= "</div></a>";

        return $preview;
    }

    public function esgi_get_genres($type) {
        $property = $type."Genres";
        $tmdbGenreUrl = $this->tmdbApiBaseUrl."genre/$type/list?api_key=".$this->tmdbKey."&language=$this->language";
        $responseBody = wp_remote_retrieve_body(wp_remote_get($tmdbGenreUrl));
        $tmdbGenres = json_decode($responseBody)->genres;
        foreach ($tmdbGenres as $key => $genre) {
            $this->$property[$type.'_genre_'.$genre->id] = $genre->name;
        }
    }

    public function esgi_generate_url_array($types, $genres = null): array
    {
        $urlArray = [];
        foreach ($types as $type => $activated) {
            if($activated) {
                // Get first 5 pages of TMDB results (100 results)
                $genresId = '';
                if($genres && $genres[$type])
                    $genresId = "&with_genres=$genres[$type]";

                for ($i = 1 ; $i <= 5 ; $i++) {
                    $urlArray[$type][$i] = $this->tmdbApiBaseUrl."discover/$type?api_key=".$this->tmdbKey."&language=".$this->language."&region=".$this->region."&sort_by=popularity.desc$genresId&include_adult=false&include_video=false&page=$i";
                }
            }
        }
        return $urlArray;
    }

    // Check if at least one type (tv or movie) is checked
    public function esgi_check_types_array($types): bool
    {
        foreach ($types as $type => $activated) {
            if($activated == true) return true;
        }
        return false;
    }

    
}