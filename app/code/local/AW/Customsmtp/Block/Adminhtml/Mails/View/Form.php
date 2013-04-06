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
class AW_Customsmtp_Block_Adminhtml_Mails_View_Form extends Mage_Adminhtml_Block_Widget_Form{
	
	protected function _prepareForm(){
		
		if ( Mage::getSingleton('adminhtml/session')->getFormData() ){
			$data = Mage::getSingleton('adminhtml/session')->getFormData();
		} elseif ($this->getEntry()){
			$data = $this->getEntry()->getData();
		}else{
			$data = array();
		}
		

		return parent::_prepareForm();
	}
	
	public function getMail(){
		return $this->getEntry();
	}
	
	
	public function _toHtml(){
		$headerHtml = Mage::app()
			->getLayout()
			->createBlock('core/template')
			->setMail($this->getMail())
			->setTemplate('aw_customsmtp/mail/view/header.phtml')
			->toHtml();
		$bodyHtml = Mage::app()
			->getLayout()
			->createBlock('core/template')
			->setMail($this->getMail())
			->setTemplate('aw_customsmtp/mail/view/body.phtml')
			->toHtml();			
			
		return $headerHtml.$bodyHtml;
	}
	

}
