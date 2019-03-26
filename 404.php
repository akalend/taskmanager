<?php
require_once 'vendor/autoload.php';

$loader = new Twig_Loader_Filesystem('template');
$twig = new Twig_Environment($loader, array(
    'cache' => 'cache',
));

echo $twig->render('404.htm');
