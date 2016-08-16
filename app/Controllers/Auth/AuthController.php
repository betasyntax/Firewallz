<?php namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Models\User;
use Betasyntax\Core\Application;
use Betasyntax\Mailer;

class AuthController extends Controller
{
  public $app;
  protected $token = '';
  protected static $activation_code;
  protected $authUrl = '/authenticate';
  protected $resetPassUrl = '/account/password/reset';
  protected $loginUrl  = '/login';
  protected $signupUrl = '/signup';
  protected $signupSuccessUrl = '/account/created';
  protected $resetPassViewUrl = '/account/password/reset/view';
  protected $auth;

  public function __construct() 
  {
    $this->middleware = ['logger'];
    $this->app = app();
    $this->app->auth = $this->app->container->get('Betasyntax\Authentication');
  }

  public function csrf_token() 
  {
    if (empty($this->session->token)) {
      $this->app->session->token = bin2hex(random_bytes(32));
    }
    $this->token = $this->app->session->token;
    // dd($this->token);
  }

  public function login()
  {
    $this->csrf_token();
    $c = array(
      'slug'=>'login',
      'token'=>$this->token,
      'action' =>$this->authUrl,
      'resetPassUrl'=>$this->resetPassUrl
    );
    return view('Auth/login.haml',$c);
  }

  public function logout()
  {
    session_unset();
    session_destroy();
    return redirect('/');
  }
  
  public function signup()
  { 
    $this->csrf_token();
    $c = array(
      'slug'=>'signup',
      'token'=>$this->token,
      'email'=>isset($_GET['email']) ? $_GET['email'] : '',
      'action' => $this->signupUrl
    );
    return view('Auth/signup.haml',$c);
  }

  public function accountCreated() 
  {
    $c = array(
      'slug'=>'login'
    );
    return view('Auth/new_account.haml',$c);
  }

  public function accountActivate($token) {
    $c = array(
      'slug'=>'activated'
    );
    // var_dump($token['token']);
    $user = User::search('activation_code','=',$token['token'],1);
    if(isset($user->activation_code)) {
      $user->activation_code='';
      $user->status = 'enabled';
      if(User::save()) {
        flash()->success('Account activated. Login below.');
        return redirect($this->loginUrl);
      } 
    } else {
      return redirect($this->loginUrl);
    }
  }

  public function email_activation_code($req,$reset=false)
  {
    $user = User::search('email','=',$req['email'],1);

    $action='account/activate/';
    $sub = 'Please activate your account.';
    $cnt1  = 'activate your account';
    if($reset) {
      $action = 'account/password/reset/';
      $sub = 'Password Reset Request';
      $cnt1  = 'reset your password';
    }
    dd($user->activation_code);
    // exit();
    $Subject = $sub;
    $AltBody    = 'Please click on this link to '.$cnt1.': <a href="http://httpserver:8081/'.$action.$user->activation_code.'">http://httpserver:8081/'.$action.$user->activation_code.'</a>';
    $Body    = 'Please click on this link to '.$cnt1.': <a href="http://httpserver:8081/'.$action.$user->activation_code.'">http://httpserver:8081/'.$action.$user->activation_code.'</a>';

    $data = [
      'from'          =>'pynewbie@gmail.com',
      'FromName'      =>'tester',
      'replyToAddress'=>['pynewbie@gmail.com', 'Information'],
      'to'            =>$user->email,
      'subject'       =>$Subject,
      'AltBody'       =>$AltBody,
      'Body'          =>$Body
    ];
    $mail = new Mailer($data);
    if(!$mail->send()) {
      echo 'Mailer Error: ' . $mail->getMailer()->ErrorInfo;
    } else {
      return true;
    }
  }

  public function passwordReset($token) 
  {
    // first check the token with the one in the db
    $user = User::search('activation_code','=',$token['token'],1);
    if(isset($user->email)) {

      $this->csrf_token();

      $c = array(
        'slug'=>'reset-password',
        'token'=>$this->token,
        'user_id'=>$user->id,
        'reset_token'=>$token,
        'action' => app()->resetPassUrl
      );
      view('Auth/resetting_password.haml', $c); 
    } else {
      return redirect('/');       
    }
  }
  public function passwordResetFinal(){
    $req = $_REQUEST;
    $pass = $req['password'];
    $pass_repeat = $req['password1'];
    $error_text = '';
    if($req['password']!=''&&$req['password1']!='') {
      if($pass == $pass_repeat) {
        $user = User::find($req['user_id']);
        if(isset($user->email)) {
          $user->password = password_hash($req['password'], PASSWORD_DEFAULT);
          $user->activation_code = '';
          $user->status = 'enabled';
          if(User::save()) {
            $c = array(
              'slug'=>'login',
            );
            flash()->success('Successfully updated your password.');
            return view('Auth/login.haml', $c);
          }
        } 
      } else {
        $error_text .= 'Passwords dont\'t match please try again.';
      } 
    } else {
      $error_text .= 'Both fiels are required.';
    }
    if($error_text!='') {
      flash()->error($error_text);
      return redirect('/account/password/reset/'.$req['reset_token']);
    }
  }
  public function resetPassword()
  {
    $this->csrf_token();
    $req = $_REQUEST;
    $new_user = $req['email'];
    $token = $req['csrf_token'];
    $error_text = '';
    if(isset($req['email'])) {
      if (hash_equals($token, $this->app->session->token)) {
        $user = User::search('email','=',$new_user,1);
        if(isset($user->email)) {
          $user->status = 'disabled';
          $user->activation_code =bin2hex(random_bytes(32));
          if(User::save()) {
            if($this->email_activation_code($req,true)) {
              $this->csrf_token();
              $c = array(
                'slug'=>'login',
                'token'=>$this->token,
                'email'=>isset($_GET['email']) ? $_GET['email'] : '',
                'action' => $this->signupUrl
              );
              return view('Auth/password_reset_email.haml', $c);
            }
          } else {
            $error_text .= 'The email provided is not on file.';
          }
        } else {
          $error_text .= 'All fields are required.';
        }
      } else {
        $error_text .= 'You must provide appropiate credentials.';
      }
      if($error_text!='') {
        flash()->error($error_text);
        redirect('/account/password/reset?email='.$new_user);
      }
    }
  }

  public function createUser() 
  {
    $req = $_REQUEST;
    $new_user = $req['email'];
    $pass = $req['password'];
    $pass_repeat = $req['password1'];
    $token = $req['csrf_token'];
    $error_text = '';
    if($req['email']!=''&&$req['password']!=''&&$req['password1']!='') {
      if (hash_equals($token, app()->session->token)) {   
        if(filter_var($new_user, FILTER_VALIDATE_EMAIL)) {
          $user = User::search('email','=',$new_user,1);    
          if(!$user) {
            if($pass == $pass_repeat) {
              if(strlen($pass)>=5) {
                if(User::createUser($req)) {
                  if($this->email_activation_code($req)) {
                    return redirect($this->signupSuccessUrl);
                  }
                } else {
                  $error_text .= 'The there was an error creating your account.';
                }
              } else {
                $error_text .= 'The password you provided is too short.';
              }
            } else {
              $error_text .= 'Your password don\'t match.';
            }
          } else {
            $error_text .= 'Sorry but that email is already taken.';
            $new_user = '';
          }
        } else {
          $error_text .= 'Sorry but we cannot validate your email. Please try another.';
          $new_user = '';
        }
      } else {
          $error_text .= 'You must provide appropiate credentials.';
      }
    } else {
      $error_text .= 'All fields are required.';
    }
    if($error_text!='') {
      flash()->error($error_text);
      return redirect($this->signupUrl.'?email='.$new_user);
    }
  }

  public function getResetPassword() 
  {
    $this->csrf_token();
    $req = $_REQUEST;
    $c = array(
      'slug'=>'reset-password',
      'token'=>$this->token,
      'action' =>$this->resetPassViewUrl
    );
    return view('Auth/reset_password.haml',$c);
  }

  public function authenticate()
  {
    $req = $_REQUEST;
    $user = $req['email'];
    $pass = $req['password'];
    $token = $req['csrf_token'];
    if(!empty($req['email'])&&!empty($req['password'])) {
      if (hash_equals($token, $this->app->session->token)) {

        if($this->app->auth->authenticate($req)) {
          $this->app->session->isLoggedIn = 1;
          flash()->success('Logged in successfully');
          return redirect('/');
        } else {
          flash()->error('No account exists with those credentials or your account hasn\'t been activated yet.');
          return redirect($this->loginUrl);
        }
      } else {
        flash()->error('You must login from this form please try again.');
        return redirect($this->loginUrl);
      }    
    } else {
      flash()->error('You must provide appropiate credentials.');
      return redirect($this->loginUrl);
    }     
  } 
}
