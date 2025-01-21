<?php

namespace iutnc\hellokant\model;

class Categorie extends BaseModel
{
    private array $data;
    protected static string $table = 'categorie';

    public function articles() {
        return $this->has_many('Article', 'id_categ');
    }

}