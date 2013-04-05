<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
 */   
class Amasty_Sorting_Block_Catalog_Product_List_Toolbar extends Amasty_Sorting_Block_Catalog_Product_List_Toolbar_Pure
{
    protected $methods = null;
    
    public function getMethods()
    {
        if (is_null($this->methods)){
            $this->methods = array();
            foreach (Mage::helper('amsorting')->getMethods() as $className){
                $method = Mage::getSingleton('amsorting/method_' . $className);  
                $this->methods[$method->getCode()] = $method;
            }   
        }
        
        return $this->methods;
    }    
    
    protected function _construct() 
    {
        parent::_construct();
        
        if ($this->reverse($this->_orderField))
            $this->_direction = 'desc';
    }

    public function getOrderUrl($order, $direction)
    {
        if ($order && $this->reverse($order)) {
            $direction =  'desc'; 
        }
        return parent::getOrderUrl($order, $direction);
    }
    
    public function getCurrentDirection()
    {
        $dir = parent::getCurrentDirection();
        $url = strtolower($this->getRequest()->getParam($this->getDirectionVarName()));   
        if (!$url && $this->reverse($this->getCurrentOrder())){
            $dir = 'desc';
        }    
        return $dir;
    }  

    public function setCollection($collection)
    {
        parent::setCollection($collection);        
        
        $inStock = Mage::getSingleton('amsorting/method_instock');
        $inStock->apply($collection, '');
        
        $methods = $this->getMethods();
        if (isset($methods[$this->getCurrentOrder()])){
            $methods[$this->getCurrentOrder()]->apply($collection, $this->getCurrentDirection());
        }

        if ($this->getRequest()->getParam('debug')){
            echo $collection->getSelect();
        }
        
        return $this;
    }
    
    protected function reverse($order)
    {
        $methods = $this->getMethods();
        if (isset($methods[$order])){
            return true;
        }
        
        $attr = Mage::getStoreConfig('amsorting/general/desc_attributes');
        if ($attr){
            return in_array($order, explode(',', $attr));
            
        }
        
        return false;
    }
}