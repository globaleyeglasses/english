<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
 */
class Amasty_Sorting_Helper_Data extends Mage_Core_Helper_Abstract 
{
    public function getMethods()
    {
        // class names. order defines the position in the dropdown
        $methods = array(
            'new',    
            'saving',
            'bestselling',    
            'mostviewed',    
            'toprated',    
            'commented',    
            'wished',    
        ); 

        return $methods;
    }
}