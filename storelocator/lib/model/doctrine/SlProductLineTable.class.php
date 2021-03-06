<?php

/**
 * SlProductLineTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class SlProductLineTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object SlProductLineTable
     */
    public static function getInstance()
    { 
        return Doctrine_Core::getTable('SlProductLine');
    }
    
    public function getProdLineArray(){
      $types = $this->findAll();
      $res = array('' => 'All');
      foreach ($types as $t) {
        if ($t->getVisible()) {
          $res[$t->getId()] = $t->getName();
        }//if
      }//foreach
      return $res;
    }//getStoreTypesArray
    
}