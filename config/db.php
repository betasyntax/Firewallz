<?php
return [
  "default"     =>"mysql",
  "staging"     =>"",
  "production"  =>"mysql",
  "mysql"=> [
    "driver"    =>"Mysql",
    "host"      =>"localhost",
    "user"      =>"router",
    "pass"      =>"router",
    "schema"    =>"router",
    "port"      => "3030",
    "errormode" =>"PDO::ERRMODE_EXCEPTION"
  ],
  "pgsql"=> [
    "driver"    =>"Pgsql",
    "host"      =>"localhost",
    "user"      =>"neo",
    "pass"      =>"heros",
    "schema"    =>"router",
    "port"      => "5432",
    "errormode" =>"PDO::ERRMODE_EXCEPTION"
  ]
];
