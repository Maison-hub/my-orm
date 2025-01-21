<?php
require __DIR__ . '/vendor/autoload.php';

use iutnc\hellokant\Connection\ConnectionFactory;

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