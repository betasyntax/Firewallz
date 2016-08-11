<?php 
use Betasyntax\Wayfinder;

$view = $app->container->get("Betasyntax\View\View");

//define your views

$wayfinder = new \Twig_SimpleFunction('Wayfinder', function ($slug) {
  Wayfinder::_setSlug($slug);
  $data = Wayfinder::tree(0);
});

$brandingStatus = new Twig_SimpleFunction('brandingStatus', function () {
  $x = Setting::search('key_name','=','show_branding',1);
  for($i=0;$i<count($x);$i++) {
    $s = $x->value;
  }
  return $s;
});

$flash = new \Twig_SimpleFunction('flash', function () {
  echo $app->flash->display(null,false);
});

$dd = new \Twig_SimpleFunction('dd', function ($data) {
  echo $app->util->dd($data);
});

//bind the helpers to your views here

$view->twig->addFunction($wayfinder);
$view->twig->addFunction($flash);