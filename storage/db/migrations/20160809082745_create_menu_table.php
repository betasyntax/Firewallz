<?php

use Phinx\Migration\AbstractMigration;

class CreateMenuTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $menus = $this->table('menus');
        $menus->addColumn('parent_id', 'integer', array('limit' => 11,'default' => 0))
              ->addColumn('menu_category_id', 'integer', array('limit' => 11,'default' => 0))
              ->addColumn('title', 'string', array('limit' => 45))
              ->addColumn('url', 'string', array('limit' => 255,'null' => true))
              ->addColumn('slug', 'string', array('limit' => 45))
              ->addColumn('type', 'string', array('limit' => 45,'default' => 'internal'))
              ->addColumn('status', 'string', array('limit' => 45,'default' => 'enabled'))
              ->addColumn('site_order', 'string', array('limit' => 45,'default' => '0'))
              ->addColumn('updated_at', 'datetime', array('null' => true))
              ->addColumn('created_at', 'datetime', array('null' => true))
              ->addIndex(array('title', 'url', 'slug'), array('unique' => true))
              ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('menus');
    }

}
