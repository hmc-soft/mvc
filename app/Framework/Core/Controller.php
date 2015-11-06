<?php
namespace Core;

use Core\Language;

abstract class Controller
{
    /**
     * on run make an instance of the config class and view class
     */
    public function __construct()
    {
        Language::load(str_replace('Controllers\\','',get_class($this)));
    }
}
