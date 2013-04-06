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
class AW_Customsmtp_IndexController extends Mage_Adminhtml_Controller_Action{
	protected function _initAction() {
		$this->loadLayout()->_setActiveMenu('system');
		return $this;
	}

	public function indexAction(){
		$this->_initAction()
			->_addContent($this->getLayout()->createBlock('customsmtp/adminhtml_mails'))
			->renderLayout();
	}
	
	public function editAction(){
		$post = Mage::getModel('customsmtp/mail')->load($this->getRequest()->getParam('id'));
		$this->_initAction()
			->_addContent($this->getLayout()->createBlock('customsmtp/adminhtml_mails_edit')->setEntry($post))
			->renderLayout();
	}	
	public function viewAction(){
		$post = Mage::getModel('customsmtp/mail')->load($this->getRequest()->getParam('id'));
		$this->_initAction()
			->_addContent($this->getLayout()->createBlock('customsmtp/adminhtml_mails_view')->setEntry($post))
			->renderLayout();
	}		
	
	public function saveAction(){
		$Entry = Mage::getModel('customsmtp/mail')->load($this->getRequest()->getParam('id'));
		try{
			$data = $this->getRequest()->getPost();
			
			$date = new Zend_Date($data['date_date']." ".implode(":", $data['date_time']), 
				Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)." H:m:s");
			$date = $date->addHour(-substr(Mage::app()->getLocale()->date()->get(Zend_Date::GMT_DIFF), 0,3));
			
			$data['date'] = $date->toString('yyyy-MM-dd H:m:s');
			
			$Entry->setData($data);
			//$Period->validate();
			if(!$Entry->getId()){
				$Entry->setId(null);
			}
			
			$Entry->save();
			Mage::getSingleton('adminhtml/session')->addSuccess("Mail successfully saved");
		}catch(Mage_Core_Exception $E){
			Mage::getSingleton('adminhtml/session')->addError($E->getMessage());
			
		}
		$this->_redirect('*/*');
	}
	
	public function deleteAction(){
		try{
			$Entry = Mage::getModel('customsmtp/mail')->load($this->getRequest()->getParam('id'));
			if($Entry->getId()){
				$Entry->delete();
				
			}else{
				throw new AW_Sarp_Exception("Can't delete mail that doesn't exist");
			}
			Mage::getSingleton('adminhtml/session')->addSuccess("Mail successfully deleted");
			$this->_redirect('*/index/');
		}catch(Mage_Core_Exception $E){
			Mage::getSingleton('adminhtml/session')->addError($E->getMessage());
			$this->_redirectReferer();
		}
		
	}
	
	public function massDeleteAction() {
		$tickets = $this->getRequest()->getParam('mails');
		if(!is_array($tickets)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
		} else {
			try {
				foreach ($tickets as $id) {
					$ticket = Mage::getModel('customsmtp/mail')->load($id);
					$ticket->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('adminhtml')->__(
						'Total of %d record(s) were successfully deleted', count($tickets)
					)
				);
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
        $this->_redirect('*/*/index');
    }
    
	public function massStatusAction()
	{
		$ids = $this->getRequest()->getParam('mails');
		if(!is_array($ids)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
		} else {
			try {
				foreach ($ids as $id) {
					$ticket = Mage::getSingleton('customsmtp/mail')
						->load($id)
						->setStatus($this->getRequest()->getParam('status'))
						->setIsMassupdate(true)
						->save();
				}
				$this->_getSession()->addSuccess(
					$this->__('Total of %d record(s) were successfully updated', count($ids))
				);
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}


         protected function _isAllowed() {

		return Mage::getSingleton('admin/session')->isAllowed('customsmtp/mails');

	  }
          
}