<?php

use Phinx\Seed\AbstractSeed;

class MenuCategoriesTableSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = array(
          array(
              'name'              => 'root',
              'updated_at'        => date('Y-m-d H:i:s'),
              'created_at'        => date('Y-m-d H:i:s')
          )
        );
        $posts = $this->table('menu_categories');
        $posts->insert($data)
              ->save();
    }
}
