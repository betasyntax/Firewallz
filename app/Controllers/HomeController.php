<?php namespace App\Controllers;

use App\Controllers\Controller;
use App\Functions as Fn;
use App\Models\User;

class HomeController extends Controller
{
  /**
   * If you have a lot of AJAX request on your page you might want to set this to true. If true you can't save session variables in this controller]
   * @var boolean
   */
  protected $close_session = true;

  public function __construct()
  {
    parent::__construct();
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
    $user = User::find_by(['id'=>[1,59],'email'=>['pynewbie@gmail.com']]);
    debug($user,'user');
    $user1 = User::find_by(1);
    debug($user1,'user1');
    $user2 = User::find_by(['email'=>['admin@admin.com','pynewbie@gmail.com','test@test.com']]);
    debug($user2,'user2');
    $user3 = User::find([1,59]);
    debug($user3,'user3');
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
      'var_one'=> Fn::myFunction(),
      'slug'=> 'welcome'
    );
    return view('welcome.haml',$c);
  }

  public function notFoundError()
  {
    return view('Errors/404.haml');
  }
}
