<?php namespace App;
// use Betasyntax\Wayfinder;
//define your views

class Helpers
{
  protected static $debugbarRender;
  protected static $app;
  protected static $debugbar;
  protected static $render;

  public static function helpers()
  {
    static::$app = app()->getInstance();

    $wayfinder = new \Twig_SimpleFunction('Wayfinder', function ($slug) {
      \Betasyntax\Wayfinder::_setSlug($slug);
      $data = \Betasyntax\Wayfinder::tree(0);
    });

    $brandingStatus = new \Twig_SimpleFunction('brandingStatus', function () {
      $x = Setting::search('key_name','=','show_branding',1);
      for($i=0;$i<count($x);$i++) {
        $s = $x->value;
      }
      return $s;
    });

    $debugBarHead = new \Twig_SimpleFunction('debugBarHead', function () {
      $app = app()->getInstance();
      static::$debugbarRender = $app->debugbar;
      $test = static::$debugbarRender;
      static::$render = $test->getJsRender();
      echo static::$render->renderHead();
    });

    $debugBarBody = new \Twig_SimpleFunction('debugBarBody', function () {
      echo static::$debugbarRender->render();
    });

    $flash = new \Twig_SimpleFunction('flash', function () {
      error_log("test");
      echo flash()->display(null,false);
    });

    $dd = new \Twig_SimpleFunction('dd', function ($data) {
      echo static::$app->util->dd($data);
    });

    return [
      'wayfinder'=>$wayfinder,
      'flash'=>$flash,
      'debugBarHead'=>$debugBarHead,
      'debugBarBody'=>$debugBarBody,
      'dd'=>$dd
    ];
  }
}