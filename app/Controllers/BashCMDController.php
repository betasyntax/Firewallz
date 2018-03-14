<?php namespace App\Controllers;

// use App\Models\Menu;
use App\Models\PortForwarding;

class BashCMDController extends Controller
{

  /**
   * Sets the middleware
   */
  public function __construct()
  {
    $this->middleware = ['auth'];
  }


  public function getCmd($args)
  {
    $tmpfname = tempnam(sys_get_temp_dir(), "");

    $handle = fopen($tmpfname, "w");
    fwrite($handle, $args);
    fclose($handle);
    $cmd1 = 'sudo bash ' . $tmpfname . ' stop; sudo bash '. $tmpfname .' start;sudo netfilter-persistent save' ;
    $old_path = getcwd();
    chdir(__dir__);
    $output = shell_exec($cmd1);
    dd($output);
    unlink($tmpfname);

  }

  /**
   * Execute the given command by displaying console output live to the user.
   *  @param  string  cmd          :  command to be executed
   *  @return array   exit_status  :  exit status of the executed command
   *                  output       :  console output of the executed command
   */
  
  static function getLiveCmd($cmd)
  {

    while (@ ob_end_flush()); // end all output buffers if any

    $proc = popen("$cmd 2>&1 ; echo Exit status : $?", 'r');

    $live_output     = "";
    $complete_output = "";

    while (!feof($proc))
    {
      $live_output     = fread($proc, 4096);
      $complete_output = $complete_output . $live_output;
      echo "</br>"."$live_output";
      @ flush();
    }

    pclose($proc);

    // get exit status
    preg_match('/[0-9]+$/', $complete_output, $matches);

    // return exit status and intended output
    return array (
                    'exit_status'  => intval($matches[0]),
                    'output'       => str_replace("Exit status : " . $matches[0], '', $complete_output)
                 );
  }

  public function getLiveCmd2($cmd)
  {
     $descriptorspec = array(
         0 => array("pipe", "r"),   // stdin is a pipe that the child will read from
         1 => array("pipe", "w"),   // stdout is a pipe that the child will write to
         2 => array("pipe", "w")    // stderr is a pipe that the child will write to
     );
    flush();
    $process = proc_open($cmd, $descriptorspec, $pipes, realpath('./'), array());
    echo "<pre>";
    if (is_resource($process)) {
      while ($s = fgets($pipes[1])) {
        print $s;
        flush();
      }
    }
    echo "</pre>";
  }

  public function getLiveCmd3($cmd)
  {
    ob_clean();
    $x=1;
    while($x<1000){
        $x++;
        echo str_repeat(' ',10*10);
        echo 'hello, world ... '.$x.'<br />';
        ob_flush();
        flush();
        usleep(1000);
    }

  }

}
