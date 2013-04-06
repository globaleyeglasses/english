<?php

/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 * 
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Customsmtp
 * @copyright  Copyright (c) 2010-2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */
class AW_Customsmtp_Model_Source_Status extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Retrive all attribute options
     *
     * @return array
     */

    public function getAllOptions()
    {
    	return $this->toOptionArray();
	}
	
	public function toOptionArray(){
		return array(
			array('value' => 'processed', 'label' => Mage::helper('customsmtp')->__('Processed')),
			//array('value' => 'pending', 'label' => Mage::helper('customsmtp')->__('Pending')),
			array('value' => 'failed', 'label' => Mage::helper('customsmtp')->__('Failed')),
			//array('value' => 'in_progress', 'label' => Mage::helper('customsmtp')->__('In progress'))
		);
	}
	
	public function toGridOptions(){
		$arr = array();
		foreach($this->toOptionArray() as $item){
			$arr[$item['value']] = $item['label'];
		}
		return $arr;
	}
}
