<?php namespace App\Controllers;

use App\Models\Dhcp;
use App\Models\Setting;
use App\Controllers\Controller;

class HomeController extends Controller
{
  /**
   * If you have a lot of AJAX request on your page you might want to set this to true. If true you can't save session variables in this controller]
   * @var boolean
   */
  protected $close_session = true;

  public function __construct()
  {
    $this->middleware = ['auth'];
  }
  
  public function index1()
  {
    // print($_SERVER['SERVER_ADDR']);
    $c = array(
      'slug'=>'home');
    return view('home.haml',$c);
  }
  public function index()
  {
    // $sql = "SELECT key_name,value FROM settings WHERE key_name IN ('external_iface','internal_iface','external_iface_link','internal_iface_link','internal_iface_hostname','internal_iface_domain') ORDER BY key_name;";
    $sql = "SELECT key_name,value FROM settings WHERE key_name IN ('external_iface','internal_iface','external_iface_link','internal_iface_link','external_iface_link_up','internal_iface_link_up','internal_iface_hostname','internal_iface_domain') ORDER BY key_name;";
    $settingModel = new Setting;
    $select = $settingModel->raw($sql);
    // $select = Setting::raw($sql);
    for($i=0;$i<count($select);$i++) {
      $data[] = $select[$i];
    }
    $cpu = $this->getCmd(['cmd'=>'cpudetails']);
    $cpu_count = $this->getCmd(['cmd'=>'cpucount']);

    // dd($cpu_count);
    $cpu_name = substr($cpu[0],13);
    $sql = "SELECT key_name,value FROM settings WHERE key_name LIKE 'disk%' ORDER BY key_name;";
    $select = $settingModel->raw($sql);
    for ($i=0; $i < count($select); $i++) {
      if(is_array($select)) {
        $sel = $select[$i];
      } else {
        $sel = $select;
      }
      $disks[] = $this->getCmd([
        'cmd'=>'getDisk',
        'mount'=>$sel->value
      ]);
    }
    $ram = $this->getMemDetails('array');
    $c = array(
      'slug'=>'home',
      'wan'=> $data[0]->value,
      'lan'=> $data[3]->value,
      'wan_ip'=> $this->getCmd(['cmd'=>'get_ip','iface'=>$data[0]->value]),
      'lan_ip'=> $this->getCmd(['cmd'=>'get_ip','iface'=>$data[3]->value]),
      'wan_speed'=> $data[1]->value,
      'lan_speed'=> $data[6]->value,
      'wan_speed_up'=> $data[2]->value,
      'lan_speed_up'=> $data[6]->value,
      'internal_iface_hostname'=> $data[5]->value,
      'internal_iface_domain'=> $data[4]->value,
      'cpu_count'=> $cpu_count,
      'cpu_count_total'=> $cpu_count[0],
      'cpu_name'=> $cpu_name,
      // 'cpu_utilization'=> $this->getCmd(['cmd'=>'cpu_utilization']),
      'uptime'=> str_replace('up ', '', $this->getCmd(['cmd'=>'get_uptime'])),
      'os'=> $this->getCmd(['cmd'=>'get_os']),
      'uname'=> $this->getCmd(['cmd'=>'get_uname']),
      'used_ram'=> number_format($ram['used']/1024/1024,2,'.',' '),
      'cached_ram'=> number_format($ram['cached']/1024/1024,2,'.',' '),
      'free_ram'=>  number_format($ram['free']/1024/1024,2,'.',' '),
      'ram_percent'=> $ram['percent'],
      'ram_total'=> number_format($ram['total']/1024/1024,2,'.',' '),
      'total_process'=> $this->getCmd(['cmd'=>'processes2']),
      'net_con_cnt'=> $this->getCmd(['cmd'=>'net_con_cnt']),
      'disks'=> $disks,
      // 'core_temps'=> $core_temps,
      'core_temps2'=> $this->getCoreTemps(),
      );
    return view('home.haml',$c);
  }

  public function getCpuUt($args)
  {
    $cmd1 = 'top -b -n 2 | awk \'/Cpu\(s\):/ {print $2}\' | tail -1';
    $handle = popen($cmd1, "r");
    while(!feof($handle)) {
      $buffer = fgets($handle);
      echo $buffer;
    }
    pclose($handle);
  }

  public function getMemDetails($args='json')
  {
    $data = array();
    $data['used'] = preg_replace('~[\r\n]+~', '', $this->getCmd(['cmd'=>'used_ram']));
    $data['cached'] = preg_replace('~[\r\n]+~', '', $this->getCmd(['cmd'=>'cached_ram']));
    $data['free'] = preg_replace('~[\r\n]+~', '', $this->getCmd(['cmd'=>'free_ram']));
    $data['total'] = preg_replace('~[\r\n]+~', '', $this->getCmd(['cmd'=>'total_ram']));
    $temp1 = (int) $data['total'] - (int) $data['used'];
    $temp2 = $temp1 / $data['total'];
    $data['percent'] = number_format((100 - ($temp2 * 100)),0,'.','');
    if ($args == 'json') {
      echo htmlspecialchars_decode(json_encode($data)); 
    } else {
      return $data;
    }
  }

  public function cpuUtlization() {
    $data = $this->getCmd(['cmd'=>'cpu_utilization_full']);
    $lastbit = array_pop($data);
    for($i=0;count($data)>$i;$i++) {
      $data[$i] = preg_replace('~[\r\n]+~', '', $data[$i]);
    }
    echo json_encode($data);
  }

  public function getMountType($mount) {
    $cmd1 = 'mount | grep " '.$mount.' "';
    $output = shell_exec($cmd1);
    $output = trim(substr($output, strpos($output, 'type ') + 5));
    $output = substr($output, 0, strrpos($output, '('));
    return $output; 
  }

  public function getCoreTemps() 
  {
    $core_temps = $this->getCmd(['cmd'=>'core_temps']);
    // var_dump($core_temps);
    $core_temps2 = explode('Core', $core_temps);
    unset($core_temps2[0]);
    $core_temps2 = array_values($core_temps2);
    $variable = array();
    for($i=0;count($core_temps2)>$i;$i++) {
      $new_str = substr($core_temps2[$i], ($pos = strpos($core_temps2[$i], '+')) !== false ? $pos + 1 : 0);
      $variable1 = substr($new_str, 0, strpos($new_str, " ("));
      $variable[] = $variable1;
    }
    $x = json_encode($variable);
    // echo $x;
    return $x;
  }

  public function getCmd($args)
  {
    if($args['cmd']=='getDisk') {
      $space = number_format((disk_total_space($args['mount'])/1024/1024/1024),0,'.','');
      $free = number_format((disk_free_space($args['mount'])/1024/1024/1024),0,'.','');
      $type = $this->getMountType($args['mount']);
      $used_percent1 = ((float)$space - (float)$free);
      $used_percent2 = ((float)$used_percent1 / (float)$space) * 100;
      $used_percent = number_format($used_percent2,0).'%';
      return array('mount'=>$args['mount'],'type'=>$type,'used'=>$used_percent,'free'=>$free,'space'=>$space);
    }
    if($args['cmd']=='processes') {
      $cmd1 = 'ps aux | sort -k 3,3 | tail -n 25 | sed \$d';
      $cmd1 = 'ps -eo pcpu,pid,user,args | sort -r -k1 | less';
      
      $output = shell_exec($cmd1);
      return $output;
    }
    if($args['cmd']=='processes2') {
      $cmd1 = 'ps -A | wc -l';
      // $cmd1 = 'ps -eo pcpu,pid,user,args | sort -r -k1 | less';
      
      $output = shell_exec($cmd1);
      return $output;
    }
    if($args['cmd']=='net_con_cnt') {
      $cmd1 = 'netstat -ant | grep ESTABLISHED | wc -l';
      $output = shell_exec($cmd1);
      return $output;
    }
    if($args['cmd']=='core_temps') {
      $cmd1 = 'sensors | grep "Core"';
      $output = shell_exec($cmd1);
      return $output;
    }
    if($args['cmd']=='total_ram') {
      $cmd1 = 'free | grep Mem | awk \'{print $2}\'';
      $handle = popen($cmd1, "r");
      while(!feof($handle)) {
        $buffer = fgets($handle);
        return $buffer;
      }
      pclose($handle);
    }
    if($args['cmd']=='used_ram') {
      $cmd1 = 'free | grep Mem | awk \'{print $3}\'';
      $handle = popen($cmd1, "r");
      while(!feof($handle)) {
        $buffer = fgets($handle);
        return $buffer;
      }
      pclose($handle);
    }
    if($args['cmd']=='cached_ram') {
      $cmd1 = 'free | grep Mem | awk \'{print $6}\'';
      $handle = popen($cmd1, "r");
      while(!feof($handle)) {
        $buffer = fgets($handle);
        return $buffer;
      }
      pclose($handle);
    }
    if($args['cmd']=='free_ram') {
      $cmd1 = 'free | grep Mem | awk \'{print $7}\'';
      $handle = popen($cmd1, "r");
      while(!feof($handle)) {
        $buffer = fgets($handle);
        return $buffer;
      }
      pclose($handle);
    }
    if($args['cmd']=='speeds') {
      $cmd1 = 'bash '.dirname(__FILE__).'/../../storage/scripts/commands.sh net_rate '.$args['iface'];
      $handle = popen($cmd1, "r");
      while(!feof($handle)) {
        $buffer = fgets($handle);
        echo $buffer;
      }
      pclose($handle);
    }
    if($args['cmd']=='cpudetails') {
      $cmd1 = 'cat /proc/cpuinfo | grep \'model name\'';
      $handle = popen($cmd1, "r");
      $data=array();
      while(!feof($handle)) {
        $buffer = fgets($handle);
        if($buffer && $buffer != null)
          $data[] = $buffer;
      }
      pclose($handle);
      return $data;
    }

    if($args['cmd']=='cpucount') {
      $cmd1 = 'lscpu | egrep \'^Thread|^Core|^Socket|^CPU\(\'';
      $handle = popen($cmd1, "r");
      $x = array();
      $c = 0;
      while(!feof($handle)) {
        $buffer = fgets($handle);
        $x[$c] = rtrim(ltrim($buffer));
        $c++;
      }

      pclose($handle);
      return $x;
    }
    if($args['cmd']=='cpu_utilization') {

      $data=array();
      $cmd1 = "mpstat -P ALL 1 1 | awk '/Average:/ && $2 ~ /[0-9]/ {print $3}'";
      $handle = popen($cmd1, "r");
      while(!feof($handle)) {
        $buffer = fgets($handle);
        $data[] = $buffer;
      }
      pclose($handle);
      return $data;
    }
    if($args['cmd']=='cpu_utilization_full') {

      $data=array();
      $cmd1 = "mpstat -P ALL 1 1 | awk '/Average:/ && $2 ~ /[0-9]/ {print $3}'";
      $handle = popen($cmd1, "r");
      while(!feof($handle)) {
        $buffer = fgets($handle);
        $data[] = $buffer;
      }
      pclose($handle);
      return $data;
    }
    if($args['cmd']=='get_ip') {
      $cmd1 = 'ifconfig '.$args['iface'].' | awk \'/inet addr/{print substr($2,6)}\'';
      $handle = popen($cmd1, "r");
      while(!feof($handle)) {
        $buffer = fgets($handle);
        return trim($buffer);
      }
      pclose($handle);
    }
    if($args['cmd']=='get_os') {
      $cmd1 = 'lsb_release -d | grep -o \'Description:.*\' | cut -f2-';
      $handle = popen($cmd1, "r");
      while(!feof($handle)) {
        $buffer = fgets($handle);
        return trim($buffer);
      }
      pclose($handle);
    }
    if($args['cmd']=='get_uname') {
      $cmd1 = 'uname -srvm';
      $handle = popen($cmd1, "r");
      while(!feof($handle)) {
        $buffer = fgets($handle);
        return trim($buffer);
      }
      pclose($handle);
    }    
    if($args['cmd']=='get_uptime') {
      $cmd1 = 'uptime -p';
      $handle = popen($cmd1, "r");
      while(!feof($handle)) {
        $buffer = fgets($handle);
        return trim($buffer);
      }
      pclose($handle);
    }    
  }
}
