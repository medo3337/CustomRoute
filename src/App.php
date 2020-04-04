<?php
/*
 * Application class
 * @author: Mohamed Abowarda
 * 
 */

namespace CustomFramework;

use CustomFramework\Route as Route;

class App
{
    public function run()
    {
        Route::match();
    }
}