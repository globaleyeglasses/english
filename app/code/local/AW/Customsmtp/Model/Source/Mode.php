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
class AW_Customsmtp_Model_Source_Mode extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    
    const OFF = 'off';
    const DELAYED = 'delayed';
    const ON = 'immediately';
	const CORE = 'core';
    
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
			array('value' => self::OFF, 'label' => Mage::helper('customsmtp')->__('Don\'t Send')),
			array('value' => self::ON, 'label' => Mage::helper('customsmtp')->__('Send From Custom SMTP Server')),
			array('value' => self::CORE, 'label' => Mage::helper('customsmtp')->__('Send From localhost')),
		//	array('value' => self::DELAYED, 'label' => Mage::helper('customsmtp')->__('Send by cron job'))
		);
	}
}
