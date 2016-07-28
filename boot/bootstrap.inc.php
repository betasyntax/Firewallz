<?php
// namespace Lib;
use Betasyntax\Wayfinder;
use Betasyntax\Registry;
use Betasyntax\Router;
use Betasyntax\Config;
use Betasyntax\Response;
use Betasyntax\Session;
use Betasyntax\Authentication;
use Betasyntax\Functions;
use Betasyntax\MetaTrait;

function app(){
  global $registry;
  return $registry;
}

$registry = new Registry();
$route = new Router();
$config = new Config();

$haml = new MtHaml\Environment('twig');

$twig = new Twig_Loader_Filesystem(array(APP_ROOT.'app/Views/'));
$hamll = new MtHaml\Support\Twig\Loader($haml, $twig);

app()->twig = new Twig_Environment($hamll);
app()->twig->addExtension(new MtHaml\Support\Twig\Extension());


app()->session = Session::getInstance();
app()->flash = new Plasticbrain\FlashMessages\FlashMessages();
app()->util = new Functions();
app()->auth = new Authentication();
app()->response = new Response();