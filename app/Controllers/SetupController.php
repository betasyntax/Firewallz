<?php 

namespace App\Controllers;

use App\Models\User;
use App\Models\Setting;

class SetupController extends Controller
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
    $model = new Setting;
    $settings = $model->all();
    for($i=0;$i<count($settings);$i++) {
      switch ($settings[$i]->key_name) {
        case 'internal_iface':
          $internal_iface = $settings[$i]->value;
          break;
        case 'external_iface':
          $external_iface = $settings[$i]->value;
          break;
        case 'external_iface_gateway':
          $external_iface_gateway = $settings[$i]->value;
          break;
        case 'external_iface_broadcast':
          $external_iface_broadcast = $settings[$i]->value;
          break;
        case 'external_iface_ip':
          $external_iface_ip = $settings[$i]->value;
          break;
        case 'external_iface_link':
          $external_iface_link = $settings[$i]->value;
          break;
        case 'external_iface_netmask':
          $external_iface_netmask = $settings[$i]->value;
          break;
        case 'external_iface_network':
          $external_iface_network = $settings[$i]->value;
          break;
        case 'external_iface_type':
          $external_iface_type = $settings[$i]->value;
          break;
        case 'internal_iface':
          $internal_iface = $settings[$i]->value;
          break;
        case 'internal_iface_domain':
          $internal_iface_domain = $settings[$i]->value;
          break;
        case 'internal_iface_hostname':
          $internal_iface_hostname = $settings[$i]->value;
          break;
        case 'internal_iface_ip':
          $internal_iface_ip = $settings[$i]->value;
          break;
        case 'internal_iface_link':
          $internal_iface_link = $settings[$i]->value;
          break;
        case 'internal_iface_type':
          $internal_iface_type = $settings[$i]->value;
          break;
        case 'internal_iface_broadcast':
          $internal_iface_broadcast = $settings[$i]->value;
          break;
        case 'internal_iface_network':
          $internal_iface_network = $settings[$i]->value;
          break;
        case 'internal_iface_netmask':
          $internal_iface_netmask = $settings[$i]->value;
          break;
        case 'setup_first_run':
          $setup_first_run = $settings[$i]->value;
          break;
        case 'external_iface_dns1':
          $external_iface_dns1 = $settings[$i]->value;
          break;
        case 'external_iface_dns2':
          $external_iface_dns2 = $settings[$i]->value;
          break;
        case 'internal_iface_domain':
          $internal_iface_domain = $settings[$i]->value;
          break;


        default:
      }
    }

    $error_cnt = '';
    $error = array();
    $ping = $this->ping('google.ca');
    if($ping) {
      $this->flash->success("Everything seems to be working as accpected.");
    } else {
        $error_cnt .= 'external_iface no_connect<br/>';      
    }

    // check for errors
    if ($external_iface_ip == '') {
      $error[] = 'external_iface no_ip';
    }
    if ($internal_iface == '') {
      $error[] = 'internal_iface no_iface';
    }
    if ($external_iface == '') {
      $error[] = 'external_iface no_iface';
    }

    if ($external_iface_broadcast=='') {
      $external_iface_broadcast = $this->cmd(['cmd'=>'getBcast','iface'=>$external_iface]);
    }
    if ($external_iface_netmask=='') {
      $external_iface_netmask = $this->cmd(['cmd'=>'getNetmask','iface'=>$external_iface]);
    }

    if (count($error)!=0) {
      for ($i=0;$i<count($error);$i++) {
        $error_cnt .= $error[$i].'<br/>';
      }
      $this->flash->error($error_cnt);
    }
    $c = array(
      'slug'=>'setup-wizard',
      'nics'=> $this->cmd(['cmd'=>'getNics']),
      'external_iface_type'=> $external_iface_type,
      'external_iface'=> $external_iface,
      'external_iface_gateway'=> $external_iface_gateway,
      'external_iface_broadcast'=> $external_iface_broadcast,
      'external_iface_ip'=> $external_iface_ip,
      'external_iface_link'=> $external_iface_link,
      'external_iface_netmask'=> $external_iface_netmask,
      'external_iface_network'=> $external_iface_network,
      'external_iface_type'=> $external_iface_type,
      'internal_iface'=> $internal_iface,
      'internal_iface_domain'=> $internal_iface_domain,
      'internal_iface_hostname'=> $internal_iface_hostname,
      'internal_iface_ip'=> $internal_iface_ip,
      'internal_iface_link'=> $internal_iface_link,
      'internal_iface_link'=> $internal_iface_link,
      'internal_iface_broadcast'=> $internal_iface_broadcast,
      'internal_iface_netmask'=> $internal_iface_netmask,
      'internal_iface_network'=> $internal_iface_network,
      'internal_iface_type'=> $internal_iface_type,
      'setup_first_run'=> $setup_first_run,
      'external_iface_dns1'=> $external_iface_dns1,
      'external_iface_dns2'=> $external_iface_dns2,
      'internal_iface_domain'=> $internal_iface_domain,
      'ping'=> $ping
    );

    return view('Setup/index.haml',$c);
  }

  private function cmd($args)
  {
    if($args['cmd']=='getNetmask') {
      $cmd = 'ifconfig '.$args['iface'].' | grep Mask | awk \'/Mask:/ {print $4}\'';
      $handle = popen($cmd, "r");
      $read = fread($handle, 2096);
      return str_replace('Mask:', '', $read);
      pclose($handle);
    }
    if($args['cmd']=='getBcast') {
      $cmd = 'ifconfig '.$args['iface'].' | grep Bcast | awk \'/Bcast:/ {print $3}\'';
      $handle = popen($cmd, "r");
      $read = fread($handle, 2096);
      return str_replace('Bcast:', '', $read);
      pclose($handle);
    }
    if($args['cmd']=='getNics') {
      $cmd = 'ls /sys/class/net | grep eth';
      $handle = popen($cmd, "r");
      $data = array();
      $read = fread($handle, 2096);
      $nics2 = preg_split('/\s+/', $read);
      $num=count($nics2);
      $num=$num-1;
      unset($nics2[$num]);
      return $nics2;
      pclose($handle);
    }
  }

  private function ping($host,$port=80,$timeout=6)
  {
    $fsock = fsockopen($host, $port, $errno, $errstr, $timeout);
    if ($fsock) {
      return TRUE;
    } else {
      return FALSE;
    }
  }
}
