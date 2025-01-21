<?php

namespace iutnc\hellokant\model;

use Exception;
use iutnc\hellokant\Connection\ConnectionFactory;
use iutnc\hellokant\query\Query;

class Article extends BaseModel{

    private array $data;
    protected static string $table = 'article';

    public function __construct(?array $data = null, string $table = 'article')
    {
        parent::__construct($data, $table);
    }

    /**
     * @return Article[]
     * @throws Exception
     */
    public static function all(): array {
        $results = parent::all();
        $articles = [];
        foreach ($results as $data) {
            $articles[] = new static($data);
        }
        return $articles;
    }

    /**
     * @throws Exception
     */
    public function categorie(): BaseModel|Categorie|null
    {
        return $this->belongs_to('Categorie', 'id_categ');
    }
}