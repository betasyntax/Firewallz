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