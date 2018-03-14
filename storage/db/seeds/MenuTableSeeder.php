<?php

use Phinx\Seed\AbstractSeed;

class MenuTableSeeder extends AbstractSeed
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
              'parent_id'         => 0,
              'menu_category_id'  => 0,
              'title'             => 'Home',
              'url'               => '',
              'slug'              => 'home',
              'type'              => 'internal',
              'status'            => 'enabled',
              'site_order'        => '0',
              'updated_at'        => date('Y-m-d H:i:s'),
              'created_at'        => date('Y-m-d H:i:s')
          ),
          array(
              'parent_id'         => 0,
              'menu_category_id'  => 0,
              'title'             => 'Welcome',
              'url'               => 'welcome',
              'slug'              => 'welcome',
              'type'              => 'internal',
              'status'            => 'enabled',
              'site_order'        => '0',
              'updated_at'        => date('Y-m-d H:i:s'),
              'created_at'        => date('Y-m-d H:i:s')
          ),
          array(
              'parent_id'         => 0,
              'menu_category_id'  => 0,
              'title'             => 'Account',
              'url'               => 'account',
              'slug'              => 'account',
              'type'              => 'internal',
              'status'            => 'enabled',
              'site_order'        => '0',
              'updated_at'        => date('Y-m-d H:i:s'),
              'created_at'        => date('Y-m-d H:i:s')
          )
        );

        $posts = $this->table('menus');
        $posts->insert($data)
              ->save();
    }
}
