<?php

/*
 * @copyright  Copyright (c) 2012 by  ESS-UA.
 */

class Ess_M2ePro_Block_Adminhtml_Buy_Listing_Filter extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();

        // Initialization block
        //------------------------------
        $this->setId('buyListingFilter');
        //------------------------------

        $this->setTemplate('M2ePro/buy/listing/filter.phtml');
    }

    protected function _beforeToHtml()
    {
        //-------------------------------
        $maxRecordsQuantity = Mage::helper('M2ePro/Module')->getConfig()->getGroupValue('/autocomplete/',
            'max_records_quantity');
        $maxRecordsQuantity <= 0 && $maxRecordsQuantity = 100;
        //-------------------------------

        //-------------------------------
        $this->selectedSellingFormatTemplate = (int)$this->getRequest()
            ->getParam('filter_buy_selling_format_template');
        $sellingFormatTemplatesCollection = Mage::helper('M2ePro/Component_Buy')
            ->getCollection('Template_SellingFormat')
            ->setOrder('title', 'ASC');

        if ($sellingFormatTemplatesCollection->getSize() < $maxRecordsQuantity) {
            $this->sellingFormatTemplatesDropDown = true;
            $sellingFormatTemplates = array();

            foreach ($sellingFormatTemplatesCollection->getItems() as $item) {
                $sellingFormatTemplates[$item->getId()] = Mage::helper('M2ePro')->escapeHtml($item->getTitle());
            }
            $this->sellingFormatTemplates = $sellingFormatTemplates;
        } else {
            $this->sellingFormatTemplatesDropDown = false;
            $this->sellingFormatTemplates = array();

            if ($this->selectedSellingFormatTemplate > 0) {
                $this->selectedSellingFormatTemplateValue = Mage::helper('M2ePro/Component_Buy')
                    ->getModel('Template_SellingFormat')
                    ->load($this->selectedSellingFormatTemplate)
                    ->getTitle();
            } else {
                $this->selectedSellingFormatTemplateValue = '';
            }
        }

        $this->sellingFormatTemplateUrl = $this->makeCutUrlForTemplate('filter_buy_selling_format_template');
        //-------------------------------

        //-------------------------------
        $this->selectedSynchronizationTemplate = (int)$this->getRequest()
            ->getParam('filter_buy_synchronization_template');
        $synchronizationsTemplatesCollection = Mage::helper('M2ePro/Component_Buy')
            ->getCollection('Template_Synchronization')
            ->setOrder('title', 'ASC');

        if ($synchronizationsTemplatesCollection->getSize() < $maxRecordsQuantity) {
            $this->synchronizationsTemplatesDropDown = true;
            $synchronizationsTemplates = array();

            foreach ($synchronizationsTemplatesCollection->getItems() as $item) {
                $synchronizationsTemplates[$item->getId()] = Mage::helper('M2ePro')->escapeHtml($item->getTitle());
            }
            $this->synchronizationsTemplates = $synchronizationsTemplates;
        } else {
            $this->synchronizationsTemplatesDropDown = false;
            $this->synchronizationsTemplates = array();

            if ($this->selectedSynchronizationTemplate > 0) {
                $this->selectedSynchronizationTemplateValue = Mage::helper('M2ePro/Component_Buy')
                    ->getModel('Template_Synchronization')
                    ->load($this->selectedSynchronizationTemplate)
                    ->getTitle();
            } else {
                $this->selectedSynchronizationTemplateValue = '';
            }
        }

        $this->synchronizationTemplateUrl = $this->makeCutUrlForTemplate('filter_buy_synchronization_template');
        //-------------------------------

        return parent::_beforeToHtml();
    }

    protected function makeCutUrlForTemplate($templateUrlParamName)
    {
        $paramsFilters = array(
            'filter_buy_selling_format_template',
            'filter_buy_synchronization_template'
        );

        $params = array();
        foreach ($paramsFilters as $value) {
            if ($value != $templateUrlParamName) {
                $params[$value] = $this->getRequest()->getParam($value);
            }
        }

        $params['tab'] = Ess_M2ePro_Block_Adminhtml_Component_Abstract::TAB_ID_BUY;

        return $this->getUrl('*/adminhtml_listing/*',$params);
    }
}
