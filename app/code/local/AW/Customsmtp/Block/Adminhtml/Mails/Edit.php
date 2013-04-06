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
class AW_Customsmtp_Block_Adminhtml_Mails_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	
	protected $_period;
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'customsmtp';
		$this->_mode = 'edit';
        $this->_controller = 'adminhtml_mails';
        $this->_updateButton('save', 'label', $this->__('Save'));
		
    }
	
	public function _prepareLayout(){
		parent::_prepareLayout();
	}

    public function getHeaderText(){
    	$this->getChild('form')->setEntry($this->getEntry());
		if($this->getEntry()->getId()){
    		return $this->__("Edit Mail \"%s\"", $this->getEntry()->getSubject());
		}else{
			return $this->__("Create New Mail");
		}
    }
	
		
	public function getBackUrl(){
		return Mage::getSingleton('adminhtml/url')->getUrl('*/*');
	}
	public function getDeleteUrl(){
		return Mage::getSingleton('adminhtml/url')->getUrl('*/*/delete', array('id'=>$this->getRequest()->getParam('id')));
	}
}
