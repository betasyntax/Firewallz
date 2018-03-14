<?php namespace App;

use App\Models\Menu;
use App\Controllers\BashCMDController;
use Betasyntax\Wayfinder;

class ViewHelpers
{
  protected static $app;

  public static function helpers()
  {
    static::$app = app();

    $brandingStatus = new \Twig_SimpleFunction('brandingStatus', function () {
      $x = Setting::search('key_name','=','show_branding',1);
      for($i=0;$i<count($x);$i++) {
        $s = $x->value;
      }
      return $s;
    });

    $isLoggedIn = new \Twig_SimpleFunction('isLoggedIn', function () {
      return app()->session->isLoggedIn;
    });

    $wayfinder = new \Twig_SimpleFunction( 'Wayfinder', function ($slug='na',$menu_id = 0, $ul_class='') {
        Wayfinder::buildHtmlTree($slug,$menu_id,$ul_class);
      }
    );

    $wayfinderAdmin = new \Twig_SimpleFunction('WayfinderAdmin', function ( $slug='na',$menu_id = 0, $ul_class='') {
        Wayfinder::buildAdminHtmlTree($slug,$menu_id,$ul_class);
      }
    );

    $consoleCmd = new \Twig_SimpleFunction('consoleCmd', function ($cmd) {
        // Wayfinder::buildAdminHtmlTree($slug,$menu_id,$ul_class);
        BashCMDController::getLiveCmd($cmd);
      }
    );

    $pathFinder = new \Twig_SimpleFunction('pathFinder', function ($slug) {
        // Wayfinder::buildAdminHtmlTree($slug,$menu_id,$ul_class);
        Wayfinder::pathFinder($slug);
      }
    );

    return [
      'brandingStatus'=>$brandingStatus,
      'isLoggedIn'=>$isLoggedIn,
      'Wayfinder'=>$wayfinder,
      'WayfinderAdmin'=>$wayfinderAdmin,
      'pathFinder'=>$pathFinder,
      'consoleCmd'=>$consoleCmd
    ];
  }
}
