<?php 

namespace App\Controllers;

use App\Models\ProxySite;
use App\Models\Setting;

class ApacheController extends Controller
{
  /**
   * Sets the middleware
   */
  public function __construct()
  {
    $this->middleware = ['auth'];
  }

  public function index($id) 
  {
    $c = array(
      'slug'=>'apache',
    );

    return view('apache_home.haml',$c);
  }

  public function proxy() 
  {
    $c = array(
      'slug'=>'proxy',
    );
    
    return view('proxy_home.haml',$c);
  }

  public function getSites()
  {
    $model = new ProxySite;
    $select = $model->raw("SELECT id,domain,alias,port,proxy_url,priority,CONCAT(UCASE(SUBSTRING(status, 1, 1)),SUBSTRING(status, 2)) as status FROM proxy_sites;");
    $x = array();
    for ($i=0;$i<count($select);$i++) {
      $d=$select;
      if(is_array($select)) {
        $d=$select[$i];
      }
      $x[$i][] = $d->id;
      $x[$i][] = $d->domain;
      $x[$i][] = $d->alias;
      $x[$i][] = $d->port;
      $x[$i][] = $d->proxy_url;
      $x[$i][] = $d->priority;
      $x[$i][] = $d->status;
    }
    echo json_encode($x);
  }
  
  public function deleteSite($id)
  { 
    $model = new ProxySite;
    $proxy = $model->find($id['id']);
    $file = $proxy->priority.'-'.$proxy->domain.'.conf';
    if($model->delete($id['id'])) {
      if(shell_exec("sudo -r www-data a2dissite ".$file)) 
        echo 'true';
      shell_exec('sudo -r www-data /usr/sbin/service apache2 reload');
    } else {
      echo "false";  
    }
  }

  public function test()
  {
    $model = new ProxySite;
    $rec = $model->create();
    $rec->domain = 'test';
    $rec->alias = 'test';
    $rec->port = 'test';
    $rec->priority = 'test';
    $rec->proxy_url = 'test';
    $rec->status = 'test';
    dd($rec);

  }

  public function addSite() 
  {
    $x = json_decode($_POST['data']);
    $model = new ProxySite;
    $rec = $model->create();
    if($x->domain==''||$x->alias==''||$x->port==''||$x->status=='') {
      echo "Please make sure all fields are provided";
      return;
    }
    $rec->domain = $x->domain;
    $rec->alias = $x->alias;
    $rec->port = $x->port;
    $rec->priority = $x->priority;
    $rec->proxy_url = $x->proxy_url;
    $rec->status = $x->status;
    $save = $model->save();
    // dd($save);
    if ($save) {
      echo 'true';
    } else {
      echo $save;
      echo 'false';
    }
  }

  public function updateSite() 
  {
    $id = $_POST['id'];
    $column = $_POST['field'];
    $value = $_POST['value'];
    $model = new ProxySite;
    $rec = $model->find($id);
    $rec->$column = $value;
    if ($model->save()) {
      echo 'true';
    } else {
      echo $save;
    }
  }

  public function objectToArray($object) {
      if( !is_object($object) && !is_array($object)){
        return $object;
      }
      if( is_object($object)) {
        $object = (array) $object;
      }
      return array_map('objectToArray', $object);
  }

  public function updateProxyServer() 
  {
    $str = 'Listen {{port}}
<VirtualHost *:{{port}}>
  ServerName  {{domain}}
  ServerAlias {{alias}}

  ProxyRequests On
  ProxyPreserveHost On

  <Proxy *>
    Order deny,allow
    Allow from all
  </Proxy>

  ProxyPass / {{proxy_url}} retry=0 timeout=5
  ProxyPassReverse / {{proxy_url}}
  SetEnv force-proxy-request-1.0 1
  SetEnv proxy-nokeepalive 1
</VirtualHost>';
    $model = new Setting;
    $sitesAvailable = $model->search('key_name','=','apache_sites_available',1);
    $sitesAvailablePath = $sitesAvailable->value;
    $sitesEnabled = $model->search('key_name','=','apache_sites_enabled',1);
    $sitesEnabledPath = $sitesEnabled->value;
    $modelPS = new ProxySite;
    $data = $modelPS->all();
    dd($data);
    for($i=0;$i<count($data);$i++) {
      $site = $data[$i];
      $domain=$site->domain;
      $alias=$site->alias;
      $port=$site->port;
      $proxy_url=$site->proxy_url;
      $priority=$site->priority;
      $status=$site->status;
      $txt = "";
      $str_cp = $str;
      $str_cp = str_replace('{{port}}', $port, $str_cp);
      $str_cp = str_replace('{{domain}}', $domain, $str_cp);
      $str_cp = str_replace('{{alias}}', $alias, $str_cp);
      $str_cp = str_replace('{{proxy_url}}', $proxy_url, $str_cp);

      $filename = $sitesAvailablePath;
      $filename .= $priority.'-'.$domain.'.conf';
      if(!file_exists($filename)) {
        touch($filename);
      }
      $proxySiteFile = fopen($filename, 'w') or die("Unable to open file!");
      fwrite($proxySiteFile, $str_cp);
      fclose($proxySiteFile);
      if($status=='active') {
        shell_exec("sudo -r www-data a2ensite {$priority}-{$domain}.conf");
      } else {
        shell_exec("sudo -r www-data a2dissite {$priority}-{$domain}.conf");
      }
      echo "sudo -r www-data {$priority}-{$domain}.conf";
    }
    //restart service
    echo shell_exec('sudo -r www-data /usr/sbin/service apache2 reload');
  }
}
