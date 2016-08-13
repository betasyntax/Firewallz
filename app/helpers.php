<?php namespace App;
// use Betasyntax\Wayfinder;
//define your views

class Helpers
{
  public function __construct()
  {
    $wayfinder = new \Twig_SimpleFunction('Wayfinder', function ($slug) {
      Wayfinder::_setSlug($slug);
      $data = \Betasyntax\Wayfinder::tree(0);
    });

    $brandingStatus = new \Twig_SimpleFunction('brandingStatus', function () {
      $x = Setting::search('key_name','=','show_branding',1);
      for($i=0;$i<count($x);$i++) {
        $s = $x->value;
      }
      return $s;
    });

    $debug = new \Twig_SimpleFunction('debugger', function () {
      $debugbar = new \DebugBar\StandardDebugBar();  
      return $debugbar->getJavascriptRenderer();
    });

    $flash = new \Twig_SimpleFunction('flash', function () {
      error_log("test");
      echo $app->flash->display(null,false);
    });

    $dd = new \Twig_SimpleFunction('dd', function ($data) {
      echo $app->util->dd($data);
    });

    // $apper = new \Betasyntax\Core\Application();
    // $app = $apper->getInstance();

    // $view = $app->container->get($app->getViewObjectStr());
    //bind the helpers to your views here
    return [
      'wayfinder'=>$wayfinder,
      'flash'=>$flash,
      'debug'=>$debug,
      'dd'=>$dd
    ];
  }
}