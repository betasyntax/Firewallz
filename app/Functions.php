<?php

if ( ! function_exists('myFunction'))
{
  /**
   * My function prints hello world
   *
   * @return application main instance
   */
  function myFunction($text = 'Betasyntax')
  {
    echo "Hello World from " . $text;
  }
}
