<?php

namespace App\Controllers;

use App\Models\Dhcp;
use App\Controllers\DhcpController;

class DnsController extends Controller
{
  /**
   * Sets the middleware
   */
  public function __construct()
  {
    $this->middleware = ['auth'];
  }

  public function index() 
  {
    $modelDhcp = new Dhcp;
    $select = $modelDhcp->raw( 'SELECT * FROM dhcps ORDER BY hostname;' );
    $data=array();
    foreach ($select as $row) {
      $data[] = $row;
    }
    $c = array(
      'slug'=>'dns',
      'dhcphost'=>$data,
    );
    return view('Network/dns.haml',$c);
  }
  
  public function getHosts() 
  {
    $model = new Dhcp;
    $select = $model->raw( 'SELECT id,hostname,ip_address,physical_address,CONCAT(UCASE(SUBSTRING(status, 1, 1)),SUBSTRING(status, 2)) as status FROM dhcps ORDER BY hostname;' );
    $x = array();
    for ($i=0;$i<count($select);$i++) {
      $d=$select;
      if(is_array($select)) {
        $d=$select[$i];
      }
      $x[$i][] = $d->id;
      $x[$i][] = $d->hostname;
      $x[$i][] = $d->ip_address;
      $x[$i][] = $d->physical_address;
      $x[$i][] = $d->status;
    }
    echo json_encode($x);
  }

  public function updateHost() 
  {
    $id = $_POST['id'];
    $column = $_POST['field'];
    $value = $_POST['value'];
    $model = new Dhcp;
    $rec = $model->find($id);
    $rec->$column = $value;
    $save = $model->save();
    if ($save) {
      echo 'true';
    } else {
      echo $save;
    }
  }

  public function addHost() 
  {
    $x = json_decode($_POST['data']);
    $model = new Dhcp;
    $rec = $model->create();
    if($x->physical_address==''||$x->ip_address==''||$x->hostname=='') {
      echo "Please make sure all fields are provided";
      return;
    }
    $rec->physical_address = $x->physical_address;
    $rec->ip_address = $x->ip_address;
    $rec->hostname = $x->hostname;
    $rec->status = $x->status;
    if ($rec->save()) {
      echo 'true';
    } else {
      echo "something bad happened";
    }
  }

  public function deleteHost($id)
  {
    $model = new Dhcp;
    if($model->delete($id['id'])) {
      echo 'true';
    } else {
     echo "false"; 
    }
  }

  public function updateDnsDhcp() 
  {
    $model = new Dhcp;
    $settings = $model->raw('SELECT * FROM settings WHERE key_name IN ("internal_iface_hostname","internal_iface_ip");');
    $iface = $settings[0]->value;
    $eface = $settings[1]->value;
    echo $iface;
    echo $eface;
    $txt = "";
    $hosts = $eface." ".$iface."\n";
    $model = new Dhcp;
    $data = $model->all();
    $d='dhcp-host=';
    for($i=0;$i<count($data);$i++) {
      $txt .= $d.$data[$i]->physical_address.",".$data[$i]->ip_address.",".$data[$i]->hostname."\n";
      $hosts .= $data[$i]->ip_address." ".$data[$i]->hostname."\n";
    }
    echo $txt;
    echo $hosts;
    $appDir = dirname(__FILE__).'/../../';
    // echo $appDir;
    $dhcpf = $appDir.'storage/dhcp-hosts.conf';
    $dhcpHosts = fopen($dhcpf, 'w') or die("Unable to open file!");
    fwrite($dhcpHosts, $txt);
    fclose($dhcpHosts);
    $hostf = $appDir.'storage/hosts.conf';
    $hostsFile = fopen($hostf, 'w') or die("Unable to open file!");
    fwrite($hostsFile, $hosts);
    fclose($hostsFile);
    //restart service
    shell_exec('sudo cp '.$hostf.' /etc/dnsmasq.hosts');
    shell_exec('sudo cp '.$dhcpf.' /etc/dnsmasq.dhcp');
    echo shell_exec('sudo -r www-data /usr/sbin/service dnsmasq restart');
  }
}
