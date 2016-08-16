<?php namespace App\Controllers\Account;

use App\Controllers\Controller;

Class AccountController extends Controller
{
  /**
   * Sets the middleware
   */
  public function __construct()
  {
    $this->middleware = ['auth'];
  }

  /**
   * Returns the account index page
   * @return string
   */
  public function index()
  {
    $c = array(
      'slug'=> 'account'
    );
    return view('account.haml',$c);
  }
}