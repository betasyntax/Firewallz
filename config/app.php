<?php
return [
  'providers' => [
    'functions'=>'Betasyntax\Functions',
    'view'=>'Betasyntax\View\ViewHaml',
    'auth'=>'Betasyntax\Authentication',
    'request'=>'GuzzleHttp\Psr7\Request',
    'response'=>'GuzzleHttp\Psr7\Response',
    'router'=>'Betasyntax\Router\Router',
    'middleware'=>'Betasyntax\Router\MiddlewareService',
    'config'=>'Betasyntax\Config',
    'filesystem'=>'League\Filesystem',
    'logger'=>'Betasyntax\Logger\Logger',
    'debugbar'=>'Betasyntax\DebugBar\DebugBar',
    'filesystem'=>'Betasyntax\Core\MountManager\Mounts',
    // 'dbcollector'=>'Betasyntax\DebugBar\DBCollector',
  ],
  'middleware' => [
    'auth'=>'Betasyntax\Authentication',
  ]
];