<?php
namespace Controllers;

use Core\View;
use Core\Controller;
use Core\Language;

/*
 * Welcome controller
 *
 * @author David Carr - dave@simplemvcframework.com
 * @version 2.2
 * @date June 27, 2014
 * @date updated May 18 2015
 */
class Welcome extends Controller
{

    /**
     * Define Index page title and load template files
     */
    public function index()
    {
        $data['title'] = Language::tr('welcome_text');
        $data['welcome_message'] = Language::tr('welcome_message');

        View::renderTemplate('header', $data);
        View::render('welcome/welcome', $data);
        View::renderTemplate('footer', $data);
    }

    /**
     * Define Subpage page title and load template files
     */
    public function subPage()
    {
        $data['title'] = Language::tr('subpage_text');
        $data['welcome_message'] = Language::tr('subpage_message');

        View::renderTemplate('header', $data);
        View::render('welcome/subpage', $data);
        View::renderTemplate('footer', $data);
    }
}
