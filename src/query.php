<?php
class Query{
    private function __construct(){
        $q = Query::table('article')
            ->select(['id', 'nom', 'descr', 'tarif'])
            ->where('tarif', '<', 1000)
            ->get();
    }
}