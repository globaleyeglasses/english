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
class AW_Customsmtp_Block_Adminhtml_Mails_Grid extends Mage_Adminhtml_Block_Widget_Grid{
	
	public function __construct(){
		parent::__construct();
		$this->setId('mailsGrid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
	}
	
	protected function _prepareCollection(){
		$collection = Mage::getModel('customsmtp/mail')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	protected function _prepareColumns(){
		$this->addColumn('id', array(
		  'header'    => Mage::helper('customsmtp')->__('ID'),
		  'align'     =>'right',
		  'width'     => '5',
		  'index'     => 'id',
		)); 
		if (!Mage::app()->isSingleStoreMode()) {
			$this->addColumn('store_id', array(
			  'header'    => Mage::helper('customsmtp')->__('ID'),
			  'align'     =>'right',
			  'width'     => '120',
			  'type'	  => 'store',
		
	          'store_view' => true,
			  'index'     => 'store_id',
			));		
		}
		$this->addColumn('date', array(
		  'header'    => Mage::helper('customsmtp')->__('Date'),
		  'align'     =>'right',
		'type'		=> 'datetime',
		  'width'     => '150',
		  'index'     => 'date',
		));		
		
		$this->addColumn('subject', array(
		  'header'    => Mage::helper('customsmtp')->__('Subject'),
		  'align'     =>'left',
		  'index'     => 'subject',
		));
		
		$this->addColumn('recipient_email', array(
		  'header'    => Mage::helper('customsmtp')->__('Recipient email'),
		  'align'     =>'left',
		  'index'     => 'to_email',
		  'width'	 => '100'
		));
		
		$this->addColumn('recipient', array(
		  'header'    => Mage::helper('customsmtp')->__('Recipient name'),
		  'align'     =>'left',
		  'index'     => 'to_name',
		  'width'	 => '150'
		));		
		
		$this->addColumn('template_code', array(
		  'header'    => Mage::helper('customsmtp')->__('Template code'),
		  'align'     =>'left',
		  'index'     => 'template_id',
		  'width'	 => '150'
		));			
		
		$this->addColumn('status', array(
		  'header'    => Mage::helper('customsmtp')->__('Status'),
		  'align'     =>'left',
		  'index'     => 'status',
		  'width'	 => '150',
		'options'	=> Mage::getModel('customsmtp/source_status')->toGridOptions(),
		  'type'	  => 'options',
		));			
		
		return parent::_prepareColumns();
	}
	
	protected function _prepareMassaction(){
	
			$this->setMassactionIdField('main_table.id');
			$this->getMassactionBlock()->setFormFieldName('mails');

			$this->getMassactionBlock()->addItem('delete', array(
				 'label'    => $this->__('Delete'),
				 'url'      => $this->getUrl('*/*/massDelete'),
				 'confirm'  => $this->__('Are you sure?')
			));
			
		$statuses = Mage::getSingleton('customsmtp/source_status')->toOptionArray();

			array_unshift($statuses, array('label'=>'', 'value'=>''));
			$this->getMassactionBlock()->addItem('status', array(
				 'label'=> $this->__('Change status'),
				 'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
				 'additional' => array(
						'visibility' => array(
							 'name' => 'status',
							 'type' => 'select',
							 'class' => 'required-entry',
							 'label' => $this->__('Status'),
							 'values' => $statuses
						 )
				 )
			));		
		return $this;
	}
	
	public function getRowUrl($row){
		return $this->getUrl('*/*/view/', array('id' => $row->getId()));
	}
	
}