<?php

/**
 * BaseSlStoreType
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $name
 * @property integer $ord
 * @property Doctrine_Collection $SlStore
 * 
 * @method string              getName()    Returns the current record's "name" value
 * @method integer             getOrd()     Returns the current record's "ord" value
 * @method Doctrine_Collection getSlStore() Returns the current record's "SlStore" collection
 * @method SlStoreType         setName()    Sets the current record's "name" value
 * @method SlStoreType         setOrd()     Sets the current record's "ord" value
 * @method SlStoreType         setSlStore() Sets the current record's "SlStore" collection
 * 
 * @package    collections
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseSlStoreType extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('sl_store_type');
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 255,
             ));
        $this->hasColumn('ord', 'integer', null, array(
             'type' => 'integer',
             'notnull' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('SlStore', array(
             'refClass' => 'SlStoreStoreType',
             'local' => 'sl_store_type_id',
             'foreign' => 'sl_store_id',
             'onDelete' => 'CASCADE'));
    }
}