<?php 

namespace App\Controllers;

use App\Models\Setting;
use App\Models\Menu;

class SettingsController extends Controller
{
  /**
   * Sets the middleware
   */

  public $menu_update_sql = '';

  public function __construct()
  {
    $this->middleware = ['auth'];
  }

  public function index($id) 
  {
    $c = array(
      'slug'=>'settings',
    );
    return view('settings_home.haml',$c);
  }

  public function menusHome()
  {
    $c = array(
      'slug'=>'menus',
    );
    return view('settings_menus.haml',$c);
  }
  
  public function getSettings()
  {
    $model = new Setting;
    $select = $model->all();
    foreach ($select as $row) {
      $data[] = $row;
    }
    $x = array();
    for ($i=0;$i<count($data);$i++) {
      $x[$i][] = $data[$i]->id;
      $x[$i][] = $data[$i]->key_name;
      $x[$i][] = $data[$i]->value;
      $x[$i][] = $data[$i]->description;
    }
    echo json_encode($x);
  }

  public function deleteSetting($id)
  {
    $model = new Setting;
    if($model->delete($id['id'])) {
      echo 'true';
    } else {
     echo "false"; 
    }
  }

  public function addSetting() 
  {
    $x = json_decode($_POST['data']);
    $model = new Setting;
    $rec = $model->create();
    if($x->key_name==''||$x->value=='') {
      echo "Please make sure all fields are provided";
      return;
    }
    $rec->key_name = $x->key_name;
    $rec->value = $x->value;
    $rec->description = $x->description;
    $save = $rec->save();
    if ($save) {
      echo 'true';
    } else {
      echo $save;
    }
  }

  public function updateSetting() 
  {
    $id = $_POST['id'];
    $column = $_POST['field'];
    $value = $_POST['value'];
    $model = new Setting;
    $rec = $model->find($id);
    $rec->$column = $value;
    $save = $rec->save(); 
    if ($save) {
      echo 'true';
    } else {
      echo $save;
    }
  }

  public function getMenus()
  {
    $model = new Menu;
    $menu = $model->all();
    foreach ($menu as $row) {
      $data[] = $row;
    }
    $x = array();
    for ($i=0;$i<count($data);$i++) {
      $x[$i][] = $data[$i]->id;
      $x[$i][] = $data[$i]->parent_id;
      $x[$i][] = $data[$i]->title;
      $x[$i][] = $data[$i]->url;
      $x[$i][] = $data[$i]->slug;
      $x[$i][] = $data[$i]->type;
      $x[$i][] = $data[$i]->status;
      $x[$i][] = $data[$i]->site_order;
    }
    echo json_encode($x);
  }

  public function deleteMenu($id)
  {
    $model = new Menu;
    if($model->delete($id['id'])) {
      echo 'true';
    } else {
     echo "false"; 
    }
  }

  public function addMenu() 
  {
    $x = json_decode($_POST['data']);
    $model = new Menu;
    $rec = $model->create();
    if($x->parent_id==''||$x->title==''||$x->url==''||$x->slug=='') {
      echo "Please make sure all fields are provided";
      exit();
    }
    $rec->parent_id = (int) ($x->parent_id);
    $rec->title = $x->title;
    $rec->url = htmlentities($x->url);
    $rec->slug = $x->slug;
    $rec->type = $x->type;
    $rec->status = $x->status;
    $rec->order = $x->order;
    $save = $rec->save();
    if ($save) {
      echo 'true';
    } else {
      echo $save;
    }
  }

  public function iterate(&$array_of_aas)
  {
    foreach ($array_of_aas as $x)
    {   
      $lft = ($x['left']);
      $rgt = ($x['right']);
      $id = $x['id'];
      $title = $x['id'];
      // echo "Found depth of " . $x['depth'] . " with ID " . $id . " lft= ".$lft." rgt= ".$rgt."\n";
      //here is where we create the sql to update the menus in the database on sort/depth change
      $this->menu_update_sql .= 'UPDATE menus SET lft = '.$lft.', rgt='.$rgt.' WHERE id = '.$id.';';
      if(isset($x['children'])) {
        // found some sub-categories! Iterate over them.
        $this->iterate($x['children']);
      }
    }
  }

  public function update() {
    $x= json_decode($_POST['menu'],TRUE);
    print_r($x);
    $this->iterate($x);
    $menu = new Menu;
    $menu->raw($this->menu_update_sql);
  }

  public function updateMenuItem() {
    $x= json_decode($_POST['menu'],TRUE);
    if(is_array($x)) {
      $menu = new Menu;
      $data = $menu->find($x[0]['id']);
      $col = (string)$x[0]['col'];
      $data->$col = $x[0]['value'];
      if($data->save()) {
        echo "success";
      } else {
        echo "error";
      }
    }
    // print_r($x);
    // $this->iterate($x);
    // echo $this->menu_update_sql;
    // $menu = new Menu;
    // $menu->raw($this->menu_update_sql);
    print_r($x);

  }

  public function addMenuItem()
  {
    $x= json_decode($_POST['menu'],TRUE);
    // print_r($x['rgt']);
    // first update all records after left +1
    // then insert our new record in its position
    $sql = '';
    // $sql .= 'LOCK TABLE menus WRITE;';
    // $sql .= 'SELECT @myRight := rgt FROM menus WHERE rgt > '.$x["rgt"].';';
    $sql .= 'UPDATE menus SET rgt = rgt + 2 WHERE rgt >= '.$x["rgt"].';';
    $sql .= 'UPDATE menus SET lft = lft + 2 WHERE lft > '.$x["rgt"].';';
    $sql .= 'INSERT INTO menus(menu_id,title, lft, rgt) VALUES(1,"New Item", '.$x['rgt'].', '.$x['rgt'].'+1);';
    // $sql .= 'UNLOCK TABLES;';
    $menu = new Menu;
    // echo $sql;
    $menu->exec($sql);
    //print_r($menu);
    //echo $sql;
    $lastId = $menu->lastId();
    echo $lastId;
  }

  public function updateMenu() 
  {
    $id = $_POST['id'];
    $column = $_POST['field'];
    $value = $_POST['value'];
    $model = new Menu;
    $rec = $model->find($id);
    $rec->$column = $value;
    $save = $rec->save();
    if ($save) {
      echo 'true';
    } else {
      echo $save;
    }
  }

  public function delMenuItem() 
  {
    $x= json_decode($_POST['menu'],TRUE);
    $sql = '';
    $sql .= 'DELETE FROM menus WHERE lft BETWEEN '.$x[0]['lft'].' AND '.$x[0]['rgt'].';';
    $sql .= 'UPDATE menus SET rgt = rgt - 2 WHERE rgt > '.$x[0]['rgt'].';';
    $sql .= 'UPDATE menus SET lft = lft - 2 WHERE lft > '.$x[0]['rgt'].';';
    $menu = new Menu;
    $menu->raw($sql);
    echo "success";
  }
}
