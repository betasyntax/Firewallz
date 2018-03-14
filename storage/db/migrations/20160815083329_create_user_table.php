<?php

use Phinx\Migration\AbstractMigration;

class CreateUserTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $user = $this->table('users');
        $user->addColumn('email', 'string', array('limit' => 255))
              ->addColumn('password', 'string', array('limit' => 255))
              ->addColumn('status', 'string', array('limit' => 45,'null' => true))
              ->addColumn('activation_code', 'string', array('limit' => 255))
              ->addColumn('updated_at', 'datetime', array('null' => true))
              ->addColumn('created_at', 'datetime', array('null' => true))
              ->addIndex(array('email'), array('unique' => true))
              ->save();
    }
    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('users');
    }
}
