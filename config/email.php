<?php
return [
  'defaults' => [
    'IsHTML'      => true,
    'IsSMTP'      => true,
    'CharSet'     => 'UTF-8',
    'SMTPSecure'  => 'tls',
    'Host'        => 'smtp.gmail.com',
    'Port'        => 587,
    'Username'    => 'email_address',
    'Password'    => 'somepass',
    'SMTPAuth'    => true,
    'SMTPDebug'   => 0,
  ]
];