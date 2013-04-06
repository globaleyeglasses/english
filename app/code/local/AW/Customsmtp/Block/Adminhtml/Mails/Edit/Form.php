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
class AW_Customsmtp_Block_Adminhtml_Mails_Edit_Form extends Mage_Adminhtml_Block_Widget_Form{
	
	protected function _prepareForm(){
		
		if ( Mage::getSingleton('adminhtml/session')->getFormData() ){
			$data = Mage::getSingleton('adminhtml/session')->getFormData();
		} elseif ($this->getEntry()){
			$data = $this->getEntry()->getData();
		}else{
			$data = array();
		}
		
		
		$form = new Varien_Data_Form(array(
				  'id' => 'edit_form',
				  'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
				  'method' => 'post',
				  'enctype' => 'multipart/form-data'
			)
		);

		$fieldset = $form->addFieldset('mail_details', array('legend'=>$this->__('Mail Details')));
		$fieldset->addField('id', 'hidden', array(
			'required'  => false,
			'name'      => 'id'
		));		
		

		
		$fieldset->addField('subject', 'text', array(
			'required'  => true,
			'name'      => 'subject',
			'label'		=> 'Subject'
		));	
		
		$fieldset->addField('status', 'select', array(
			'required'  => true,
			'name'      => 'status',
			'label'		=> 'Status',
			'values'	=> Mage::getModel('customsmtp/source_status')->toOptionArray()
		));		
		
		$fieldset->addField('date_date', 'date', array(
			'required'  => true,
			'name'      => 'date_date',
			'label'		=> 'Date',
			'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
		));	
		$fieldset->addField('date_time', 'time', array(
			'required'  => true,
			'name'      => 'date_time',
			'label'		=> 'Time',
            'format'    => Mage::app()->getLocale()->getTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
		));		
		
				
		$fieldset->addField('body', 'textarea', array(
			'required'  => true,
			'name'      => 'body',
			'label'		=> 'Body',
			'style'		=> 'width:800px; height:300px;',
 
		));		
			

		
		if(!@$data['date']){
			$data['date'] = Mage::app()->getLocale()->date();
		}
		$data['date'] = Mage::app()->getLocale()->date($data['date'], 'yyyy-MM-dd H:m:s');
		
		
		$data['date_time'] = $data['date']->toString('H,m,s');
		$data['date_date'] = ($data['date']);
		
		
		$form->setValues($data);

		$form->setUseContainer(true);
		$this->setForm($form);

		return parent::_prepareForm();
	}
	

}
