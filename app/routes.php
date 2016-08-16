<?php  
return [
  ['GET',  '/',                                 'index@HomeController','home'],
  ['GET',  '/welcome',                          'welcome@HomeController','welcome'],
  ['GET',  '/account',                          'index@Account\AccountController','account'],
  ['GET',  '/notfound',                        'notFoundError@HomeController',''],
  
  ['POST', '/account/password/reset',           'passwordResetFinal@Auth\AuthController','_auth_reset_post'],
  ['POST', '/account/password/reset/view',      'resetPassword@Auth\AuthController','_auth_reset_view_post'],
  ['POST', '/authenticate',                     'authenticate@Auth\AuthController','_auth_authenticate_post'],
  ['POST', '/signup',                           'createUser@Auth\AuthController','_auth_signup_post'],
  ['GET',  '/signup',                           'signup@Auth\AuthController','_auth_signup'],
  ['GET',  '/account/created',                  'accountCreated@Auth\AuthController','_auth_user_created'],
  ['GET',  '/account/activate/[a:token]',       'accountActivate@Auth\AuthController','_auth_activate_token'],
  ['GET',  '/account/password/reset',           'getResetPassword@Auth\AuthController','_auth_reset'],
  ['GET',  '/account/password/reset/[a:token]', 'passwordReset@Auth\AuthController','_auth_reset_token'],
  ['GET',  '/login',                            'login@Auth\AuthController','_auth_login'],
  ['GET',  '/logout',                           'logout@Auth\AuthController','_auth_logout'],
];
