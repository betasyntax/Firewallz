<?php  
return [
  ['GET',  '/',                                 'index@HomeController'],
  ['GET',  '/welcome',                                 'welcome@HomeController'],
  
  ['POST', '/account/password/reset',           'passwordResetFinal@Auth\AuthController'],
  ['POST', '/account/password/reset/view',      'resetPassword@Auth\AuthController'],
  ['POST', '/authenticate',                     'authenticate@Auth\AuthController'],
  ['POST', '/signup',                           'createUser@Auth\AuthController'],
  ['GET',  '/signup',                           'signup@Auth\AuthController'],
  ['GET',  '/account/created',                  'accountCreated@Auth\AuthController'],
  ['GET',  '/account/activate/[*:token]',       'accountActivate@Auth\AuthController'],
  ['GET',  '/account/password/reset',           'getResetPassword@Auth\AuthController'],
  ['GET',  '/account/password/reset/[*:token]', 'passwordReset@Auth\AuthController'],
  ['GET',  '/login',                            'login@Auth\AuthController'],
  ['GET',  '/logout',                           'logout@Auth\AuthController'],
];
