<?php
return [
  "application" => [
    "name"=>"configuration",
    "secret"=>"s3cr3t"
  ],
  "host"=> "localhost",
  "port"=> "80",
  "default_db"=>"mysql",
  "mysql"=> [
    "driver"=>"Pdo",
    "host"=>"localhost",
    "user"=>"router",
    "pass"=>"router",
    "schema"=>"router",
    "errormode"=>"pd::ERRMODE_EXCEPTION"
  ]
];
