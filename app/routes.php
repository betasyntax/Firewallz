<?php  
return [
  ['GET',  '/home',                            'index@HomeController',''],
  ['GET',  '/',                                'index@HomeController','home'],
  ['GET',  '/welcome',                         'welcome@HomeController','welcome'],
  ['GET',  '/account',                         'index@Account\AccountController','account'],
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


  ['GET', '/dhcp/getLeases', 'getLeases@DhcpController'],

  ['GET', '/cmd/restartIpTables', 'index@BashCMDController'],

  ['GET', '/net/[a:cmd]/[a:iface]', 'getCmd@HomeController'],
  ['GET', '/cpu/utilization', 'cpuUtlization@HomeController'],
  ['GET', '/dhcp/active-hosts','index@DnsController'],
  ['GET', '/get/cpu_util','getCpuUt@HomeController'],
  ['GET', '/get/mem_details','getMemDetails@HomeController'],

  ['GET', '/apache','index@ApacheController'],

  ['GET', '/proxy','proxy@ApacheController'],

  ['POST', '/proxy/update', 'updateSite@ApacheController'],
  ['GET' , '/proxy/sites','getSites@ApacheController'],
  ['GET', '/proxy/del/[i:id]', 'deleteSite@ApacheController'],
  ['POST', '/proxy/add','addSite@ApacheController'],
  ['GET', '/proxy/update-proxy', 'updateProxyServer@ApacheController'],
  ['GET', '/test', 'test@ApacheController'],

  ['POST', '/settings/update', 'updateSetting@SettingsController'],
  ['GET' , '/settings/all','getSettings@SettingsController'],
  ['GET', '/settings/del/[i:id]', 'deleteSetting@SettingsController'],
  ['POST', '/settings/add','addSetting@SettingsController'],

  ['GET', '/menus','menusHome@SettingsController'],
  ['POST', '/menus/update','update@SettingsController'],
  ['POST', '/menus/add','addMenuItem@SettingsController'],
  ['POST', '/menus/delete','delMenuItem@SettingsController'],

  ['POST', '/settings/menus/update', 'updateMenu@SettingsController'],
  ['GET' , '/settings/menus/all','getMenus@SettingsController'],
  ['GET', '/settings/menus/del/[i:id]', 'deleteMenu@SettingsController'],
  ['POST', '/settings/menus/add','addMenu@SettingsController'],
  ['POST', '/settings/menus/update-item','updateMenuItem@SettingsController'],

  ['GET', '/settings','index@SettingsController'],

  ['GET', '/iptables/panic','panic@IpTablesController'],
  ['GET', '/iptables/start','restart@IpTablesController'],
  ['GET', '/iptables/restart','restart@IpTablesController'],
  ['GET', '/iptables/status','status@IpTablesController'],
  ['GET', '/iptables/forward/all','all@IpTablesController'],
  ['POST', '/iptables/forward/add','add@IpTablesController'],
  ['GET', '/iptables/forward/update','status@IpTablesController'],
  ['GET', '/iptables/forward/delete','status@IpTablesController'],
  ['GET', '/iptables/forward/all_hosts','formAllHosts@IpTablesController'],
  
  ['GET', '/iptables/cmd/[a:cmd]','cmd@IpTablesController'],



  ['GET', '/setup','index@SetupController'],

  ['GET', '/network',               'index@NetworkController'],
  ['GET', '/network/interfaces',    'interfaces@NetworkController'],
  ['GET', '/network/firewall/port-forwarding',      'index@IpTablesController'],
  ['GET', '/network/dhcp',           'index@DhcpController'],
  ['POST', '/network/dns/update',   'updateHost@DnsController'],
  ['GET', '/network/dns/getHosts', 'getHosts@DnsController'],
  ['GET', '/network/dns/del/[i:id]', 'deleteHost@DnsController'],
  ['POST', '/network/dns/add', 'addHost@DnsController'],
  ['GET', '/network/dns/update-hosts', 'updateDnsDhcp@DnsController'],
  ['GET', '/network/dhcp/leases', 'leases@DhcpController'],
  ['GET', '/network/dhcp/getleases', 'getLeases@DhcpController'],
];
