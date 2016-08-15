<?php namespace App\Controllers;

use App\Controllers\Controller;

class HomeController extends Controller
{

  public $domain = 'web';
  protected $app;

  /**
   * [$close_session if you have a lot of AJAX request on your page you might want to set this to true. If true you can't save session variables in this controller]
   * @var boolean
   */
  protected $close_session = true;

  public function __construct()
  {
    parent::__construct();
    $this->middleware = [];
  }

  /**
   * [index returns the homepage]
   * @return [string] [returns twig template]
   */
  public function index()
  {
    app()->logger->log('info','HomeController');
    debugStack("HomeController");
    $c = array(
      'slug'=> 'home'
    );
    // $app = \Betasyntax\Core\Application::getInstance();
    // $app = $app->getInstance();
    // $profile = new \Twig_Profiler_Profile();
    // $twig = app()->twig;
    // app()->twig->addExtension(new \Twig_Extension_Profiler($profile));
    // $dumper = new \Twig_Profiler_Dumper_Html();
    // echo $dumper->dump($profile);
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
