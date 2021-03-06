<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Addslproductline extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createTable('sl_product_line', array(
             'id' => 
             array(
              'type' => 'integer',
              'length' => 8,
              'autoincrement' => true,
              'primary' => true,
             ),
             'name' => 
             array(
              'type' => 'string',
              'notnull' => true,
              'length' => 255,
             ),
             'ord' => 
             array(
              'type' => 'integer',
              'notnull' => false,
              'length' => 8,
             ),
             ), array(
             'indexes' => 
             array(
             ),
             'primary' => 
             array(
              0 => 'id',
             ),
             ));
    }

    public function down()
    {
        $this->dropTable('sl_product_line');
    }
}