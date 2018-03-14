<?php namespace App\Models;

use App\Models\Model;

Class User extends Model {

  protected static $activation_code;

  public static function createUser($req) 
  {
    self::$activation_code = bin2hex(random_bytes(32));
    $rec = User::create();
    $rec->email = $req['email'];
    $rec->password = password_hash($req['password'], PASSWORD_DEFAULT);
    $rec->status = 'disabled';
    $rec->activation_code = self::$activation_code;
    $save = User::save();
    if ($save) {
      return true;
    } else {
      return false;
    }
  }
}