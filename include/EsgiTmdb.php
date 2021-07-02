<?php


class EsgiTmdb
{

    protected string $tmdbApiBaseUrl = "https://api.themoviedb.org/3/";
    protected string $tmdbImageUrl = "https://image.tmdb.org/t/p/w200";
    protected string $tmdbBaseUrl = "https://www.themoviedb.org/";
    protected string $language;
    protected string $region;
    protected string $tmdbKey;
    protected array $tvGenres = [];
    protected array $movieGenres = [];


    public function __construct()
    {
        $this->language = str_replace("_", "-", get_locale());
        $this->region = substr($this->language, 0, 2);
        $this->tmdbKey = get_option('esgi_tmdb_settings')['tmdb-key'];
        $this->esgi_get_genres('tv');
        $this->esgi_get_genres('movie');
    }

    public function esgi_get_random_tmdb_item($types){
        $urlArray = $this->esgi_generate_url_array($types);
        if($urlArray) {
            $list = [];
            foreach ($urlArray as $type => $url) {
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
            if(!empty($list)) return $list[rand(0, count($list) - 1)];
        }
        return false;
    }

    public function esgi_get_tmdb_preview($work): string
    {
        $name = $work->title ?? $work->name;
        $poster = $this->tmdbImageUrl.$work->poster_path;
        $type = $work->type == 'tv' ? "Série" : "Film";
        $url = $this->tmdbBaseUrl.$type.'/'.$work->id;

        $preview = "<a href='$url' target='_blank'><div class='esgi_tmdb_preview'>";
        $preview .= "<div class='esgi_tmdb_preview_poster'><img src='$poster'/></div>";
        $preview .= "<div class='esgi_tmdb_preview_name'>$name</div>";
        $preview .= "<div class='esgi_tmdb_preview_type'>$type</div>";
        $preview .= "</div></a>";

        return $preview;
    }

    public function esgi_get_genres($type) {
        $property = $type."Genres";
        $tmdbGenreUrl = $this->tmdbApiBaseUrl."genre/$type/list?api_key=".$this->tmdbKey."&language=$this->language";
        $responseBody = wp_remote_retrieve_body(wp_remote_get($tmdbGenreUrl));
        $tmdbGenres = json_decode($responseBody)->genres;
        foreach ($tmdbGenres as $key => $genre) {
            $this->$property[$genre->id] = $genre->name;
        }
    }

    public function esgi_generate_url_array($types): array
    {
        $urlArray = [];
        foreach ($types as $type => $activated) {
            if($activated)
                $urlArray[$type] = $this->tmdbApiBaseUrl."discover/$type?api_key=".$this->tmdbKey."&language=".$this->language."&region=".$this->region."&sort_by=popularity.desc&include_adult=false&include_video=false&page=1";
        }
        return $urlArray;
    }

    public function getTvGenres(): array
    {
        return $this->tvGenres;
    }

    public function getMovieGenres(): array
    {
        return $this->movieGenres;
    }

}