<?php namespace App\Middleware;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use App\Models\User;

Class Authentication
{
  /*
  **
  *** The __constructor method will run when the class is first created
  *** Please not that in the constructor it should take 0 args if posible
  **
  */
  public function __construct(){
    if(!isset(app()->session->isLoggedIn))
      app()->session->isLoggedIn=0;
  }

  public function __invoke(Request $request, Response $response, callable $next)
  {
    if(!$this->isLoggedIn()) {
      if(isset($_SERVER['REQUEST_URI'])) {
        app()->session->requestPath = $_SERVER['REQUEST_URI'];
      }
      redirect('/login');
    }
    return $next($request, $response);
  }

  public function isLoggedIn() {
    return app()->session->isLoggedIn;
  }

  public function authenticate($req) {
    $userModel = new User;
    $user = $userModel->find_by(['email'=>$req['email'],'status'=>'enabled'],1);

    if(count($user->properties)>0) {
        if (password_verify($req['password'], $user->password)) {
          return true;
        }
    } else {
      return false;
    }
  }

  public static function domain($domain)
  { 
    return (empty($domain))?:'web';
  }

  public static function secure($domain)
  {
    if($domain=='admin') {
      if(app()->session->isLoggedIn==0) {
        header('Location: '.app()->loginUrl);
      }
    } 
  }
}
