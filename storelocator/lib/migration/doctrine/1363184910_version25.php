<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version25 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('sl_product_line', 'visible', 'boolean', '25', array(
             'default' => '1',
             ));
    }

    public function down()
    {
        $this->removeColumn('sl_product_line', 'visible');
    }
}