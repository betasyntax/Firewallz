<?php

namespace App\Models;

Class User extends Model {

  protected $activation_code;

  public function validate() {
    return true;
  }

  public function createUser($req) 
  {
    self::$activation_code = bin2hex(random_bytes(32));
    $userModel = new User;
    $rec = $userModel->create();
    $rec->email = $req['email'];
    $rec->password = password_hash($req['password'], PASSWORD_DEFAULT);
    $rec->status = 'disabled';
    $rec->activation_code = self::$activation_code;
    $save = $rec->save();
    if ($save) {
      return true;
    } else {
      return false;
    }
  }
}