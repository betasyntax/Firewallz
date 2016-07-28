<?php
use Betasyntax\Wayfinder;
use Betasyntax\Registry;
// use App\Models\Setting;

$wayfinder = new Twig_SimpleFunction('Wayfinder', function ($slug) {
  Wayfinder::_setSlug($slug);
  $data = Wayfinder::tree(0);
});
// $brandingStatus = new Twig_SimpleFunction('brandingStatus', function () {
//   $x = Setting::search('key_name','=','show_branding',1);
//   for($i=0;$i<count($x);$i++) {
//     $s = $x->value;
//   }
//   return $s;
// });
$flash = new Twig_SimpleFunction('flash', function () {
  echo app()->flash->display(null,false);
});
$dd = new Twig_SimpleFunction('dd', function ($data) {
  echo app()->util->dd($data);
});
app()->twig->addFunction($wayfinder);
// app()->twig->addFunction($brandingStatus);
app()->twig->addFunction($flash);
app()->twig->addFunction($dd);