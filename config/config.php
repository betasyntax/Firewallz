<?php
return [
  "application" => [
    "name"=>"configuration",
    "secret"=>"s3cr3t"
  ],
  "host"=> "localhost",
  "port"=> "80",
  "default_db"=>"pgsql",
  "mysql"=> [
    "driver"=>"Mysql",
    "host"=>"localhost",
    "user"=>"router",
    "pass"=>"router",
    "schema"=>"router",
    "errormode"=>"PDO::ERRMODE_EXCEPTION"
  ],
  "pgsql"=> [
    "driver"=>"Pgsql",
    "host"=>"localhost",
    "user"=>"router",
    "pass"=>"router",
    "schema"=>"router",
    "errormode"=>"PDO::ERRMODE_EXCEPTION"
  ],
];
