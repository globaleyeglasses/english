<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
 */   
class Amasty_Sorting_Model_Method_Bestselling extends Amasty_Sorting_Model_Method_Abstract
{
    public function getCode()
    {
        return 'bestsellers';
    }    
    
    public function getName()
    {
        return 'Best Sellers';
    }
    
    public function getIndexTable()
    {
        return 'amsorting/' . $this->getCode();
    } 
    
    public function getColumnSelect()
    {
        $sql =' SELECT SUM(order_item.qty_ordered)'
            . ' FROM ' . Mage::getSingleton('core/resource')->getTableName('sales/order_item') . ' AS order_item'
            . ' WHERE order_item.product_id = e.entity_id ' 
            . $this->getPeriodCondition('order_item.created_at', 'best_period') 
            . $this->getStoreCondition('order_item.store_id') 
        ;
        return new Zend_Db_Expr('(' . $sql . ')');         
    }    
     
    public function getIndexSelect() 
    {
        $sql =' SELECT product_id, store_id, SUM(qty_ordered)'
            . ' FROM ' . Mage::getSingleton('core/resource')->getTableName('sales/order_item') . ' AS order_item'
            . ' WHERE 1 ' 
            . $this->getPeriodCondition('order_item.created_at', 'best_period') 
            . ' GROUP BY product_id, store_id'
        ;
        return $sql;
    }    
   
}