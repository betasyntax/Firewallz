<?php namespace App\Controllers;

use App\Controllers\Controller;

class HomeController extends Controller
{
  /**
   * If you have a lot of AJAX request on your page you might want to set this to true. If true you can't save session variables in this controller]
   * @var boolean
   */
  protected $close_session = true;

  public function __construct()
  {
    $this->middleware = [];
  }

  /**
   * returns the homepage
   * @return [string] returns twig template
   */
  public function index()
  {
    app()->logger->log('info','HomeController');
    debugStack("HomeController");
    $c = array(
      'slug'=> 'home'
    );
    return view('home.haml',$c);
  }
  
  /**
   * returns the welcome page
   * @return [string] returns twig template
   */
  public function welcome()
  {
    $c = array(
      'var_one'=> myFunction(),
      'slug'=> 'welcome'
    );
    return view('welcome.haml',$c);
  }

  public function notFoundError()
  {
    return view('404.haml');
  }
}
