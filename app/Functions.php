<?php namespace App;

/**
 * All these functions should be static but its really up to use. To use these functions in your app just do:
 * use App/Functions as Fn;
 *
 * $x = Fn::myFunction();
 */
Class Functions
{
  /**
   * Returns a simple hello world function 
   * @param  string $text Provide the text you want to be displayed
   * @return string
   */
  public static function myFunction($text = 'Betasyntax')
  {
    return "Hello World from " . $text;
  }

  public static function isLoggedIn()
  {
    if(app()->session->isLoggedIn==1) {
        return true;
    } else {
        return false;
    }
  }
}
