<?php

use Phinx\Migration\AbstractMigration;

class CreateMenuCategoriesTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $user = $this->table('menu_categories');
        $user->addColumn('name', 'string', array('limit' => 255))
              ->addColumn('created_at', 'datetime', array('null' => true))
              ->addColumn('updated_at', 'datetime', array('null' => true))
              ->save();
    }
    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('menu_categories');
    }
}
