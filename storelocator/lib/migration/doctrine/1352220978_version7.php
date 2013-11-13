<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Addslcountry extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createTable('sl_country', array(
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
              'unique' => true,
              'length' => 255,
             ),
             'iso' => 
             array(
              'type' => 'string',
              'notnull' => false,
              'length' => 255,
             ),
             'world_area_id' => 
             array(
              'type' => 'integer',
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
        $this->dropTable('sl_country');
    }
}