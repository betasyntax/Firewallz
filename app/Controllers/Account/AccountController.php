<?php namespace App\Controllers\Account;

use App\Controllers\Controller;

Class AccountController extends Controller
{
  public function __construct()
  {
    $this->middleware = ['auth'];
  }

  public function index()
  {
    $c = array(
      'slug'=> 'account'
    );
    return view('account.haml',$c);
  }
}