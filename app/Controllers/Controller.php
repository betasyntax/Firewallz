<?php namespace App\Controllers;

use Betasyntax\Router\BaseController;
use Betasyntax\Authentication;

class Controller extends BaseController
{
  public function __construct()
  {
    parent::__construct();
    Authentication::secure($this->domain);
    $this->domain = Authentication::domain($this->domain);
  }
}
