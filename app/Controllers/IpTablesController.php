<?php namespace App\Controllers;

use App\Models\Menu;
use App\Models\PortForwarding;
use App\Controllers\BashCMDController;

class IptablesController extends Controller
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
    $modelPortForwarding = new PortForwarding;
    $select = $modelPortForwarding->raw( 'SELECT * FROM port_forwardings;' );
    $data=array();
    foreach ($select as $row) {
      $data[] = $row;
    }
    $c = array(
      'slug'=>'firewall',
      'dhcphost'=>$data,
    );
    return view('iptables_index.haml',$c);
  }


  public function all() 
  {
    $model = new PortForwarding;
    $select = $model->raw( 'SELECT p.id, dhcps_id, type, port, hostname, concat(ip_address," :: ",hostname," (",physical_address,") ") as ip_address FROM router.port_forwardings as p LEFT OUTER JOIN router.dhcps as d ON p.dhcps_id = d.id ORDER BY dhcps_id, type, port;' );
    $x = array();
    for ($i=0;$i<count($select);$i++) {
      $d=$select;
      if(is_array($select)) {
        $d=$select[$i];
      }
      $x[$i][] = $d->id;
      $x[$i][] = $d->ip_address;
      $x[$i][] = $d->type;
      $x[$i][] = $d->port;
    }
    echo json_encode($x);
  }
  public function formAllHosts()
  {    
    $model = new PortForwarding;
    $select = $model->raw( 'SELECT concat(ip_address," :: ",hostname," (",physical_address,")") as host, id FROM router.dhcps;' );
    $x = array();
    for ($i=0;$i<count($select);$i++) {
      $d=$select;
      if(is_array($select)) {
        $d=$select[$i];
      }
      $x[$i][] = $d->host;
      $x[$i][] = $d->id;
    }
    echo json_encode($x);
  }
  
  public function add()
  {
    $x = json_decode($_POST['data']);
    $model = new PortForwarding;
    $rec = $model->create();
    dd($x);
    $rec->dhcps_id = $x->dhcps_id;
    $rec->type = $x->type;
    $rec->port = $x->port;
    if ($rec->save()) {
      echo 'true';
    } else {
      echo "something bad happened";
    }
  }
  
  public function update()
  {

  }
  
  public function delete()
  {

  }

  public function start()
  {
      return $this->restart();
  }

  public function panic()
  {
    $appDir = dirname(__FILE__).'/../../';
    $fileF = $appDir.'storage/scripts/setup/firewall_stop.sh';
    $config = file_get_contents($fileF);

    $cmd = new BashCMDController;
    $cmd->getCmd($config);
    echo "Firewall stopped. All traffic stopped.";


    // $cmd = new BashCMDController;
    // return $cmd->getCmd('echo "echo 0 > /proc/sys/net/ipv4/ip_forward" | sudo sh && echo "iptables -A INPUT -i lo -j ACCEPT" | sudo sh && echo "iptables -A OUTPUT -o lo -j ACCEPT" | sudo sh && echo "Firewall Stopped! All traffic to and from the internet has stopped!"');
  }
  
  public function restart()
  {
    //get total number of hosts
    $model = new PortForwarding;
    $select = $model->raw( 'SELECT COUNT(DISTINCT dhcps_id) as counter FROM port_forwardings;' );$x = array();
    $counter = null;
    for ($i=0;$i<count($select);$i++) {
      $d=$select;
      if(is_array($select)) {
        $d=$select[$i];
      }
      $counter = $d->counter;
    }
    //get the vars
    $model = new PortForwarding;
    $select = $model->raw( 'SELECT p.id, dhcps_id, type, port, hostname, ip_address, physical_address FROM router.port_forwardings as p LEFT OUTER JOIN router.dhcps as d ON p.dhcps_id = d.id ORDER BY dhcps_id, type, port;' );
    $data = array();
    for ($i=0;$i<count($select);$i++) {
      $d=$select;
      if(is_array($select)) {
        $d=$select[$i];
      }
      $data[$i][] = $d->id;
      $data[$i][] = $d->dhcps_id;
      $data[$i][] = $d->type;
      $data[$i][] = $d->port;
      $data[$i][] = $d->hostname;
      $data[$i][] = $d->ip_address;
      $data[$i][] = $d->physical_address;
    }
    //get the config files
    $appDir = dirname(__FILE__).'/../../';
    $fileF = $appDir.'storage/scripts/setup/firewall_config.sh';
    $config = file_get_contents($fileF);
    //create the text to inject into template
    $content = '';
    if($counter>=1) {
      //get the host first
      $c = 0;
      $cnt = 1;
      for ($i=0; $i < count($data); $i++) { 
        $c++; 
        if($c==1) {
          $content .= "FWHOST[ip".$cnt."]=\"".$data[$i][5]."\"\n"; 
          $content .= "FWHOST[tcp".$cnt."]=\"".$data[$i][3]."\"\n";
        }
        if($c==2) {
          $x = $cnt-1;
          $content .= "FWHOST[udp".$cnt."]=\"".$data[$i][3]."\"\n";
          $c=0;
          $cnt++;
        }
      }
    }
    //replace template vars
    $content .= "service netfilter-persistent save";
    $hostCounter = str_replace('%test%', $counter, $config);
    $test = str_replace('%content%', $content, $hostCounter);
    // echo "$test";
    // echo $test;
    $cmd = new BashCMDController;
    $cmd->getCmd($test);
    echo 'Firewall restarted.';
  }

  
  public function cmd($cmd)
  {
    $test = $this->getLiveCmd($cmd['cmd']);
    $c = array(
      'cmd'=>$test
    );
    return view('iptables_temp.haml',$c);
  }

  public function getLiveCmd($cmd) {
    if($cmd=='ping') {
      // $tester = new BashCMDController;
      return "ping -c 4 google.ca";
    }
    if($cmd=='status') {
      // $tester = new BashCMDController;
      return "sudo bash ../storage/scripts/setup/firewall.sh status";
    }

    if($cmd=='restart') {
      // $tester = new BashCMDController;
      return $this->restart();
    }

    if($cmd=='panic') {
      // $tester = new BashCMDController;
      return $this->panic();
    }
  }
  
}
