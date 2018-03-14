<?php 

namespace App\Controllers;


class DhcpController extends Controller
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
      'slug'=>'dhcp-leases',
    );
    return view('Network/dhcp.haml',$c);
  }
  
  public static function getLeases() {

    ini_set("auto_detect_line_endings", true);
    $file = '/var/lib/misc/dnsmasq.leases';
    $array1[] = null;
    $array2[] = null;
    foreach (file($file) as $name) {
      $a = explode(' ', $name);
      $array2[0] = $a[1];
      $array2[1] = $a[2];
      $array2[2] = $a[3];
      $array2[3] = '';
      $array[] = $array2;
    }
    echo json_encode($array);
  }
} 
