<?php
app()->config = array(
  'env' => 'dev',
  'title' => 'Router Config | ',
  'auth_domain' => 'web'
);

app()->loginUrl = '/login';
app()->logoutUrl = '/logout';
app()->authUrl = '/authenticate';
app()->domain = 'web';
app()->auth_domain = 'admin';
app()->signupUrl = '/signup';
app()->signupSuccessUrl = '/account/created';
app()->resetPassViewUrl = '/account/password/reset/view';
app()->resetPassUrl = '/account/password/reset';