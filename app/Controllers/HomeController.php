<?php

namespace App\Controllers;

use Betasyntax\BaseController;

class HomeController extends Controller
{

  public $domain = 'web';

  /**
   * [$close_session if you have a lot of AJAX request on your page you might want to set this to true. If true you can't save session variables in this controller]
   * @var boolean
   */
  protected $close_session = true;

  /**
   * [index returns the homepage]
   * @return [string] [returns twig template]
   */
  public function index()
  {
    $c = array(
      'slug'=> 'home'
    );
    return view('home.haml',$c);
  }
  
  /**
   * [index returns the welcome page]
   * @return [string] [returns twig template]
   */
  public function welcome()
  {
    $c = array(
      'var_one'=> ['Hello','World'],
      'slug'=> 'welcome'
    );
    return view('welcome.haml',$c);
  }
}
