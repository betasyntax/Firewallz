<?php
return [
  'core_providers' => [
    'functions'   => 'Betasyntax\Functions',
    'debugbar'    => 'Betasyntax\DebugBar\DebugBar',
    'router'      => 'Betasyntax\Router\Router',
    'mountManager'=> 'Betasyntax\Core\MountManager\Mounts',
    'logger'      => 'Betasyntax\Logger\Logger',
    'view'        => 'Betasyntax\View\ViewHaml',
  ],
  'providers' => [
    'helpers'     => 'App\Functions',
    'middleware'  => 'Betasyntax\Router\MiddlewareService',
    'request'     => 'GuzzleHttp\Psr7\Request',
    'response'    => 'GuzzleHttp\Psr7\Response',
    'filesystem'  => 'League\Filesystem',
  ],
  'middleware' => [
    'auth'        => 'App\Middleware\Authentication',
  ]
];