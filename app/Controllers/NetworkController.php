<?php 

namespace App\Controllers;

class NetworkController extends Controller
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
    $c = array(
      'slug'=>'network'
    );
    return view('Network/network_index.haml',$c);
  }
  public function interfaces()
  {
    $c = array(
      'slug'=>'network-interfaces',
      'networks'=>$this->getCmd(['cmd'=>'getNetworks'])
    );

    return view('Network/interfaces.haml',$c);
  }

  public function getCmd($args)
  {
    if($args['cmd']=='getNetworks') {
      $cmd1 = 'netstat -i | sed 1,2d | awk \'{print $1}\'';
      $output = shell_exec($cmd1);
      return $output;
    }
  }
}
