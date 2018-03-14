<?php

use Phinx\Seed\AbstractSeed;

class UserTableSeeder extends AbstractSeed
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
              'email'           => 'admin@admin.com',
              'password'        => '$2y$10$tgIenSu5WNCkQ4kJQC1rgus1cueQ7pF6i4lfLxuS8IkUhv/.UW49C',
              'status'          => 'enabled',
              'activation_code' => '',
              'updated_at'      => date('Y-m-d H:i:s'),
              'created_at'      => date('Y-m-d H:i:s')
          )
        );

        $posts = $this->table('users');
        $posts->insert($data)
              ->save();
    }
}
