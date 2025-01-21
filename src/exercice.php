<?php
require_once __DIR__ . '/vendor/autoload.php';

use iutnc\hellokant\Connection\ConnectionFactory;
use iutnc\hellokant\model\Article;
use iutnc\hellokant\model\Categorie;

$conf = parse_ini_file('conf/db.conf.ini');


try {
    ConnectionFactory::makeConnection($conf);
} catch (Exception $e) {
    echo $e->getMessage();
    die();
}

try {
    $myPdo = ConnectionFactory::getConnection();
} catch (Exception $e) {
    echo $e->getMessage();
    die();
}

$article = new Article();
$article->nom = 'My first article';
$article->descr = 'This is the content of my first article';
$article->tarif = 10;
$article->id_categ=1;
$article->save();

echo "First Article saved !!!\n";
echo "$article->nom\n";
echo "$article->descr\n";
echo "$article->tarif\n";

$articleTwo = new Article();
$articleTwo->nom = "My second article";
$articleTwo->descr = "This is the content of my second article";
$articleTwo->tarif = 20;
$articleTwo->id_categ=2;
$articleTwo->save();


echo "Second Article saved\n";
echo "$articleTwo->nom\n";
echo "$articleTwo->descr\n";
echo "$articleTwo->tarif\n";

$articleTwo->delete();

echo "Second Article deleted !\n";

echo "-----------------------\n";
echo "Articles\n";

$articles = Article::all();

foreach ($articles as $article) {
    echo "$article->nom\n";

    echo "$article->descr\n";
    echo "$article->tarif\n";
}

echo "-----------------------\n";
echo "Find\n";

$articlesFind = Article::find([['tarif', '<=', 100]], ['nom', 'tarif']);
foreach ($articlesFind as $article) {
    echo "$article->nom\n";
    echo "$article->tarif\n";
}


echo "-----------------------\n";
echo "Relations\n";
echo "     Belong to\n";
try {
    $article = Article::find(64);
    $categorie = $article->categorie();
    echo "Article ".$article->nom." a pour catégorie:  ".$categorie->nom."\n";
} catch (Exception $e) {
    echo $e->getMessage();
    die();
}
echo "     Has Many\n";
try {
    $categorie = Categorie::find(1);
    $articles = $categorie->articles();
    echo "La catégorie ".$categorie->nom." a pour article:  \n";
    foreach ($articles as $article) {
        echo $article->nom."\n";
    }
} catch (Exception $e) {
    echo $e->getMessage();
    die();
}


echo "-----------------------\n";

