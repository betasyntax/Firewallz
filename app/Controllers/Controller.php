<?php namespace App\Controllers;

use Betasyntax\BaseController;
use Betasyntax\Authentication;

class Controller extends BaseController
{
  public function __construct()
  {
    Authentication::secure($this->domain);
    $this->domain = Authentication::domain($this->domain);
  }
}
