<?php
/*
 * 
 * @author: Mohamed Abowarda
 * 
 */
require('vendor/autoload.php');
require('app/routes.php');

use CustomFramework\App as App;

$app = new App();
$app->run();

?>