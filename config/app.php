<?php
return [
  'providers' => [
    'functions'=>'Betasyntax\Functions',
    'view'=>'Betasyntax\View\ViewHaml',
    'auth'=>'Betasyntax\Authentication',
    'request'=>'GuzzleHttp\Psr7\Request',
    'response'=>'GuzzleHttp\Psr7\Response',
    'router'=>'Betasyntax\Router',
    'config'=>'Betasyntax\Config'
  ],
];
// app()->config = array(
//   'env' => 'dev',
//   'title' => 'Router Config | ',
//   'auth_domain' => 'web'
// );

// app()->loginUrl = '/login';
// app()->logoutUrl = '/logout';
// app()->authUrl = '/authenticate';
// app()->domain = 'web';
// app()->auth_domain = 'admin';
// app()->signupUrl = '/signup';
// app()->signupSuccessUrl = '/account/created';
// app()->resetPassViewUrl = '/account/password/reset/view';
// app()->resetPassUrl = '/account/password/reset';