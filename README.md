# Betasyntax Framework
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/betasyntax/framework/master.svg?style=flat-square)](https://travis-ci.org/betasyntax/framework)

This framework is highly experimental. Until I remove this notice please use at your own risk. You have been warned!

The Betasyntax Framework is an easy to use framework based on PHP. It implements the MVC design pattern with easy to read code and a really simple learning curve. I built this taking inspiration from Rails, Laravel and all with mostly drop in components like league/container, Twig, Haml, flash messages, super simple ORM all while trying to keep the main goal of slim, featured and easy to use. 

#### Current features:

* Custom Database Abstraction Layer (Currently only MySQL and PostgreSQL are supported)
* Full support for database migrations and seeding thanks to [Phinx](https://github.com/robmorgan/phinx)
* Utilizes [Twig](http://twig.sensiolabs.org/), [Haml](http://haml.info/) and [Less](http://lesscss.org/) for easy front end development. Don't like twig or haml? You can implement your own!.
* Uses [League Container](https://github.com/thephpleague/container) for super simple IoC Container Dependency Injection for the entire framework.
* Use Service Providers to manage what components are loaded and what gets injected into your app.
* Easy to use Middleware via [Relay](http://relayphp.com/)
* Modular Authentication system for quick setups. Don't want to use the built in auth system, you can build your own and inject it into your app.
* Easyily create menus from a database table with a simple command called WayFinder. Allows for active css tags to be applied to active menu items.
* Custom PHP Debugbar implementation for even faster development. Easily see all generated sql statements, request variables, exceptions, as well as developer generated errors and messages.

## Installation:
```bash
cd /web/server/root
composer create-project betasyntax/betasyntax ./
```
Or  you can install the development version:
```bash
git clone https://github.com/betasyntax/betasyntax.git ./
composer install
```
Next reate a database, edit /conf/config.php to your liking and run the migration to get your dynamic menus working.
```bash
vendor/bin/phinx migrate -e development
```
#### You can use migrations to quickly seed your database.
```bash
vendor/bin/phinx seed:run -e development
```
#### Easily rollback a migration.
```bash
vendor/bin/phinx rollback
```
## Basic Usage:
###Routes (/app/routes.php)
```php
  ['GET','/','index@HomeController','home'] //loads /app/Controllers/HomeController->index()
  ['GET','/user/[i:id]','getUser@AccountController'] //loads /app/Controllers/AccountController->getUser($id)
  ['GET','/account/[*:string1]/[*:string2]','getUser@AccountController'] //loads /app/Controllers/AccountController->accountMethod($string1,$string2)
```
###Controllers (/app/Controllers/HomeController.php)
```php
<?php 
namespace App\Controllers;

use Betasyntax\BaseController;

class HomeController extends Controller
{  
  public function __construct()
  {
    // this controller now requires a logged in session to continue otherwise it redirects to the login page
    $this->middleware = ['auth']; 
  }
  
  public function index()
  {
    // set some variables
    $data = array(
      'slug'=> 'home'
    );
    // load our view
    return view('home.haml',$data);
  }
  
  public function welcome()
  {
    $data = array(
      'var_one'=> ['Hello','World'],
      'slug'=> 'welcome'
    );
    return view('welcome.haml',$data);
  }
}
```
###Models (/app/Models/User.php)

Create a table users, add some data and add this file which corresponds to your table name. So user_settings becomes UserSetting, users becomes Users etc.
```php
<?php
namespace App\Models;

use Betasyntax\Model;

Class User extends Model {
  public function getUser($id) {
    return User::find($id); // returns a PDP object
  }
}
```
and in your Controller or Model you can do this:
```php
$user = getUser($id);
$user->title = 'Johny';
$user::save(); //update record

$user = User::create();
$user->title = 'Sally';
$user->save(); //insert record
```
The built in ORM also supports has many, has one joins. More on that to come later!

###Views 
/app/Views/layout.haml
```haml
!!!
%html
  %head
    %title
      Welcome &vert;
      - block title 
      - endblock
    %meta{:charset=>"utf-8"}
    %meta{'http-equiv'=>"X-UA-Compatible",:content=>"IE=edge"}
    %meta{:name=>"viewport", :content=>"width=device-width, initial-scale=1"}
    %link{:rel=>"stylesheet", :type=>"text/css",:href=>"/css/bootstrap.min.css"}
    %link{:rel=>"stylesheet", :type=>"text/css",:href=>"/css/main.css"}
    - block head
    - endblock
  %body{:class=>"#{slug}"}
    %nav.navbar.navbar-inverse.navbar-fixed-top
      .container
        .navbar-header
          %button.navbar-toggle.collapsed{:type=>'button','data-toggle'=>'collapse','data-target'=>'#navbar','aria-expanded'=>'false','aria-controls'=>"navbar"}
            %span.sr-only
              Toggle navigation
            %span.icon-bar
            %span.icon-bar
            %span.icon-bar
        #navbar.collapse.navbar-collapse
          // use an include to add snippets of code that are tied to helpers and your controllers
          // - include 'Helpers/mainmenu.haml' 
          // or you can call the helper right from the view
          = Wayfinder(slug)
          // this will create a link <a href="/welcome" class="test" id="texter">Welcome</a>
          // the first arg is the text and second arg is the named route specified for the welcome route in app/routes.php
          = link_to('Welcome','welcome','',[['class','test'],['id','texter']])
    .container
      - block body
      - endblock
    %footer.footer
      .container
        %p.text-muted
          Copyright 2016 Betasyntax PHP Web Framework
    %script{:src=>"/js/jquery.min.js"}
    %script{:src=>"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js",:integrity=>"sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS",:crossorigin=>"anonymous"}
    %script{:src=>"/js/main.js"}
    - block jsFooter
    - endblock
```
/app/Views/home.haml
```haml
- extends 'Layouts/layout.haml'
- block title
Home
- endblock
- block body
%p
  Welcome to the the Betasyntax PHP Web Framework. Try it out!
- endblock
- block jsFooter
- endblock

```
This framework uses haml and twig to render all the layouts and parse your views. If you want to add helpers to your app you need to edit /app/helpers.php and add your helpers to your app. Check out the [Twig](http://twig.sensiolabs.org/documentation) and [Haml](http://haml.info/docs.html) docs for full documentation.

/app/helpers.php
```php
  // all helpers should be placed in this function and the variables of the twig function needs to be included in the return array
  public static function helpers()
  {
    $brandingStatus = new \Twig_SimpleFunction('brandingStatus', function () {
      $x = Setting::search('key_name','=','show_branding',1);
      for($i=0;$i<count($x);$i++) {
        $s = $x->value;
      }
      return $s;
    });
    return [
      'brandingStatus'=>$brandingStatus
    ];
  }
```
### Service Providers and Middlware
If you want to use one of the many php packages out there simply composer.json file and then edit the conf/app.php file. The package will be automatically injected into your application.

/conf/app.php
```php
<?php
return [
  'providers' => [
    // default classes
    'functions' =>'Betasyntax\Functions',
    // dont like haml change ViewHaml to View
    'view'      =>'Betasyntax\View\ViewHaml',
    'auth'      =>'Betasyntax\Authentication',
    'request'   =>'GuzzleHttp\Psr7\Request',
    'response'  =>'GuzzleHttp\Psr7\Response',
    'router'    =>'Betasyntax\Router\Router',
    'config'    =>'Betasyntax\Config',
    // add this
    'myCoolApp' =>'MyCoolApp\CoolApp'
  ],
```
You can also specify your middleware in this array like so:
```php
  'middleware' => [
    'auth'      => 'Betasyntax\Authentication',
  ]
];
```

This is useful if you want to create your own twig extensions and integrate them into your app. You need to do something like this:
/app/helper.php
```php
<?php 
use Betasyntax\Wayfinder;

$view = $app->container->get($app->getViewObjectStr());

$wayfinder = new \Twig_SimpleFunction('Wayfinder', function ($slug) {
  Wayfinder::_setSlug($slug);
  $data = Wayfinder::tree(0);
});
// now you can use wayfinder any where in your views.
$view->twig->addFunction($wayfinder);
```
## License

The Betasyntax framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
