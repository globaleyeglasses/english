<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Block_Adminhtml_Amazon_Template_NewProduct_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        // Initialization block
        //------------------------------
        $this->setId('templateNewProductGrid');
        //------------------------------

        // Set default values
        //------------------------------
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        //------------------------------

        $this->attributeSets = Mage::getResourceModel('eav/entity_attribute_set_collection')
                                    ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
                                    ->load()->toOptionHash();

        //------------------------------
        $listingProductIds = Mage::helper('M2ePro')->getSessionValue('listing_product_ids');
        $listingProductId = reset($listingProductIds);

        $this->listingAttributes = Mage::helper('M2ePro/Component_Amazon')
            ->getObject('Listing_Product',$listingProductId)
            ->getListing()
            ->getAttributeSetsIds();
    }

    protected function _prepareCollection()
    {
        $marketplaceId = $this->getRequest()->getParam('marketplace_id');

        $collection = Mage::getModel('M2ePro/Amazon_Template_NewProduct')->getCollection();
        $collection->addFieldToFilter('`marketplace_id`', $marketplaceId);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'       => Mage::helper('M2ePro')->__('ID'),
            'align'        => 'right',
            'type'         => 'number',
            'width'        => '50px',
            'index'        => 'id',
            'filter_index' => 'id',
            'frame_callback' => array($this, 'callbackColumnId')
        ));

        $this->addColumn('node_title', array(
            'header'       => Mage::helper('M2ePro')->__('Department'),
            'align'        => 'left',
            'type'         => 'text',
            'width'        => '100px',
            'index'        => 'node_title',
            'filter_index' => 'node_title',
            'frame_callback' => array($this, 'callbackColumnNodeTitle')
        ));

        $this->addColumn('category_path', array(
            'header'       => Mage::helper('M2ePro')->__('Category'),
            'align'        => 'left',
            'type'         => 'text',
            'width'        => '350px',
            'index'        => 'category_path',
            'filter_index' => 'category_path',
            'frame_callback' => array($this, 'callbackColumnCategoryPath')
        ));

        $this->addColumn('attribute_sets', array(
            'header' => Mage::helper('M2ePro')->__('Attribute Sets'),
            'align'  => 'left',
            'width'  => '200px',
            'filter'    => false,
            'sortable'  => false,
            'frame_callback' => array($this, 'callbackColumnAttributeSets')
        ));

        $marketplace_id = $this->getRequest()->getParam('marketplace_id');

        $back = Mage::helper('M2ePro')->makeBackUrlParam('*/adminhtml_amazon_template_newProduct',array(
            'marketplace_id'      => $marketplace_id,
        ));

        $this->addColumn('assignment', array(
            'header'       => Mage::helper('M2ePro')->__('Assignment'),
            'align'        => 'left',
            'type'         => 'text',
            'width'        => '130px',
            'filter'       => false,
            'sortable'     => false,
            'frame_callback' => array($this, 'callbackColumnActions'),
        ));

        $this->addColumn('actions', array(
            'header'    => Mage::helper('M2ePro')->__('Actions'),
            'align'     => 'left',
            'width'     => '100px',
            'type'      => 'action',
            'index'     => 'actions',
            'filter'    => false,
            'sortable'  => false,
            'getter'    => 'getId',
            'actions'   => array(
                array(
                    'caption'   => Mage::helper('M2ePro')->__('Edit Template'),
                    'url'       => array('base'=> '*/adminhtml_amazon_template_newProduct/edit/marketplace_id/'
                                                  .$marketplace_id.
                                                  '/back/'.$back),
                    'field'     => 'id'
                ),
                array(
                    'caption'   => Mage::helper('M2ePro')->__('Delete Template'),
                    'url'       => array('base'=> '*/adminhtml_amazon_template_newProduct/delete/marketplace_id/'
                                                  .$marketplace_id.
                                                  '/back/'.$this->getRequest()->getParam('back')),
                    'field'     => 'ids',
                    'confirm'   => Mage::helper('M2ePro')->__('Are you sure?')
                ),
            )
        ));
    }

    // ####################################

    protected function _prepareMassaction()
    {
        // Set massaction identifiers
        //--------------------------------
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        //--------------------------------

        // Set delete action
        //--------------------------------
        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('M2ePro')->__('Delete'),
             'url'      => $this->getUrl('*/*/delete'),
             'confirm'  => Mage::helper('M2ePro')->__('Are you sure?')
        ));
        //--------------------------------

        return parent::_prepareMassaction();
    }

    // ####################################

    public function callbackColumnId($value, $row, $column, $isExport)
    {
        return $value.'&nbsp;';
    }

    public function callbackColumnTitle($value, $row, $column, $isExport)
    {
        return '&nbsp'.$value;
    }

    public function callbackColumnNodeTitle($value, $row, $column, $isExport)
    {
        return '&nbsp'.$value;
    }

    public function callbackColumnCategoryPath($value, $row, $column, $isExport)
    {
        return '&nbsp;'.$value;
    }

    public function callbackColumnAttributeSets($value, $row, $column, $isExport)
    {
        $attributeSets = Mage::getModel('M2ePro/AttributeSet')->getCollection()
            ->addFieldToFilter('object_type',Ess_M2ePro_Model_AttributeSet::OBJECT_TYPE_AMAZON_TEMPLATE_NEW_PRODUCT)
            ->addFieldToFilter('object_id',(int)$row->getId())
            ->toArray();

        $value = '';
        foreach ($attributeSets['items'] as $attributeSet) {
            if (strlen($value) > 100) {
                $value .= ', <strong>...</strong>';
                break;
            }
            if (isset($this->attributeSets[$attributeSet['attribute_set_id']])) {
                $value != '' && $value .= ', ';
                $value .= $this->attributeSets[$attributeSet['attribute_set_id']];
            }
        }

        return $value;
    }

    public function callbackColumnActions($value, $row, $column, $isExport)
    {
        $url = $this->getUrl(
            '*/adminhtml_amazon_template_newProduct/map/',
            array(
                'id' => $row->getId()
            )
        );
        $newAsinTemplateAttributes = $row->getAttributeSetsIds();

        $listingAttributesAreIncludedInNewAsinTemplate = true;
        foreach ($this->listingAttributes as $listingAttribute) {
            if (array_search($listingAttribute,$newAsinTemplateAttributes) === false) {
                $listingAttributesAreIncludedInNewAsinTemplate = false;
                continue;
            }
        }

        if ($listingAttributesAreIncludedInNewAsinTemplate) {
            $confirmMessage = Mage::helper('M2ePro')->__('Are you sure?');
            $actions = '&nbsp;<a href="javascript:;" onclick="confirm(\''.$confirmMessage.'\') && ';
            $actions .= 'setLocation(\''.$url.'\');">';
            $actions .= Mage::helper('M2ePro')->__('Assign To This Template');
            $actions .= '</a>';
        } else {
            $actions = '<span style="color: #808080;">';
            $actions .= '&nbsp;'.Mage::helper('M2ePro')->__('Assign To This Template');
            $actions .= '</span>';
        }

        return $actions;
    }

    // ####################################

    protected function _toHtml()
    {
        $javascriptsMain = <<<JAVASCRIPT
<script type="text/javascript">

    $$('#listingMoveToListingGrid div.grid th').each(function(el){
        el.style.padding = '2px 4px';
    });

    $$('#listingMoveToListingGrid div.grid td').each(function(el){
        el.style.padding = '2px 4px';
    });

</script>
JAVASCRIPT;

        return parent::_toHtml() . $javascriptsMain;
    }

    // ####################################

    public function getGridUrl()
    {
        return $this->getUrl('*/adminhtml_amazon_template_newProduct/templateNewProductGrid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        $marketplace_id = $this->getRequest()->getParam('marketplace_id');

        $back = Mage::helper('M2ePro')->makeBackUrlParam('*/adminhtml_amazon_template_newProduct',array(
            'marketplace_id'      => $marketplace_id
        ));

        return $this->getUrl('*/adminhtml_amazon_template_newProduct/edit', array(
            'id' => $row->getId(),
            'marketplace_id' => $marketplace_id,
            'back' => $back
        ));
    }

    // ####################################
}