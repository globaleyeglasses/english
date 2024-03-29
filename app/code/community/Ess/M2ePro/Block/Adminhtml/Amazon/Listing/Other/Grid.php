<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Block_Adminhtml_Amazon_Listing_Other_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    // ####################################

    public function __construct()
    {
        parent::__construct();

        /** @var $this->connRead Varien_Db_Adapter_Pdo_Mysql */
        $this->connRead = Mage::getSingleton('core/resource')->getConnection('core_read');

        // Initialization block
        //------------------------------
        $this->setId('amazonListingOtherGrid');
        //------------------------------

        // Set default values
        //------------------------------
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        //------------------------------
    }

    public function getMassactionBlockName()
    {
        return 'M2ePro/adminhtml_component_grid_massaction';
    }

    // ####################################

    protected function _prepareCollection()
    {
        $collection = Mage::helper('M2ePro/Component_Amazon')->getCollection('Listing_Other');

        $collection->getSelect()->joinLeft(
            array('mp' => Mage::getResourceModel('M2ePro/Marketplace')->getMainTable()),
            'mp.id = main_table.marketplace_id',
            array('marketplace_title' => 'mp.title')
        )->joinLeft(
            array('am' => Mage::getResourceModel('M2ePro/Amazon_Marketplace')->getMainTable()),
            'am.marketplace_id = main_table.marketplace_id',
            array('currency' => 'am.default_currency')
        );

        // Add Filter By Account
        if ($this->getRequest()->getParam('amazonAccount')) {
            $collection->addFieldToFilter('main_table.account_id',
                                          $this->getRequest()->getParam('amazonAccount'));
        }

        // Add Filter By Marketplace
        if ($this->getRequest()->getParam('amazonMarketplace')) {
            $collection->addFieldToFilter('main_table.marketplace_id',
                                          $this->getRequest()->getParam('amazonMarketplace'));
        }

        //var_dump($collection->getSelect()->__toString()); exit();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('product_id', array(
            'header' => Mage::helper('M2ePro')->__('Product ID'),
            'align'  => 'left',
            'width'  => '80px',
            'type'   => 'number',
            'index'  => 'product_id',
            'filter_index' => 'product_id',
            'frame_callback' => array($this, 'callbackColumnProductId')
        ));

        $this->addColumn('title', array(
            'header'    => Mage::helper('M2ePro')->__('Product Title / SKU'),
            'align' => 'left',
            'type' => 'text',
            'index' => 'title',
            'filter_index' => 'second_table.title',
            'frame_callback' => array($this, 'callbackColumnProductTitle'),
            'filter_condition_callback' => array($this, 'callbackFilterTitle')
        ));

        $this->addColumn('general_id', array(
            'header' => Mage::helper('M2ePro')->__('ASIN / ISBN'),
            'align' => 'left',
            'type' => 'text',
            'index' => 'general_id',
            'filter_index' => 'general_id',
            'frame_callback' => array($this, 'callbackColumnGeneralId')
        ));

        $this->addColumn('online_qty', array(
            'header' => Mage::helper('M2ePro')->__('Amazon QTY'),
            'align' => 'right',
            'width' => '100px',
            'type' => 'number',
            'index' => 'online_qty',
            'filter_index' => 'online_qty',
            'frame_callback' => array($this, 'callbackColumnAvailableQty')
        ));

        $this->addColumn('online_price', array(
            'header' => Mage::helper('M2ePro')->__('Amazon Price'),
            'align' => 'right',
            'width' => '100px',
            'type' => 'number',
            'index' => 'online_price',
            'filter_index' => 'online_price',
            'frame_callback' => array($this, 'callbackColumnPrice')
        ));

        $this->addColumn('is_afn_channel', array(
            'header' => Mage::helper('M2ePro')->__('Fulfillment'),
            'width' => '100px',
            'index' => 'is_afn_channel',
            'filter_index' => 'second_table.is_afn_channel',
            'type' => 'options',
            'sortable' => false,
            'options' => array(
                0 => Mage::helper('M2ePro')->__('Merchant'),
                1 => Mage::helper('M2ePro')->__('Amazon')
            ),
            'frame_callback' => array($this, 'callbackColumnAfnChannel')
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('M2ePro')->__('Status'),
            'width' => '60px',
            'index' => 'status',
            'filter_index' => 'main_table.status',
            'type' => 'options',
            'sortable' => false,
            'options' => array(
                Ess_M2ePro_Model_Listing_Product::STATUS_UNKNOWN => Mage::helper('M2ePro')->__('Unknown'),
                Ess_M2ePro_Model_Listing_Product::STATUS_LISTED => Mage::helper('M2ePro')->__('Active'),
                Ess_M2ePro_Model_Listing_Product::STATUS_STOPPED => Mage::helper('M2ePro')->__('Inactive'),
                Ess_M2ePro_Model_Listing_Product::STATUS_BLOCKED => Mage::helper('M2ePro')->__('Inactive (Blocked)')
            ),
            'frame_callback' => array($this, 'callbackColumnStatus')
        ));

        $this->addColumn('start_date', array(
             'header' => Mage::helper('M2ePro')->__('Start Date'),
             'align' => 'right',
             'width' => '130px',
             'type' => 'datetime',
             'format' => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),
             'index' => 'start_date',
             'filter_index' => 'second_table.start_date',
             'frame_callback' => array($this, 'callbackColumnStartDate')
        ));

        $this->addColumn('end_date', array(
             'header' => Mage::helper('M2ePro')->__('End Date'),
             'align' => 'right',
             'width' => '130px',
             'type' => 'datetime',
             'format' => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),
             'index' => 'end_date',
             'filter_index' => 'second_table.end_date',
             'frame_callback' => array($this, 'callbackColumnEndDate')
        ));

        $this->addColumn('actions', array(
            'header'    => Mage::helper('M2ePro')->__('Actions'),
            'align'     => 'left',
            'width'     => '70px',
            'type'      => 'action',
            'index'     => 'actions',
            'filter'    => false,
            'sortable'  => false,
            'getter'    => 'getId',
            'actions'   => array(
                array(
                    'caption'   => Mage::helper('M2ePro')->__('Unmap'),
                    'url'       => array('base'=> '*/adminhtml_amazon_listingOther/unmapToProduct'),
                    'field'     => 'id',
                    'confirm'  => Mage::helper('M2ePro')->__('Are you sure?')
                ),
                array(
                    'caption'   => Mage::helper('M2ePro')->__('View Log'),
                    'url' => array('base'=>
                                      '*/adminhtml_log/listingOther/back/'
                                      .Mage::helper('M2ePro')
                                          ->makeBackUrlParam('*/adminhtml_listingOther/index',
                                              array('tab'=>
                                                      Ess_M2ePro_Block_Adminhtml_Component_Abstract::TAB_ID_AMAZON))
                                      .'/'),
                    'field'     => 'id'
                ),
                array(
                    'caption'   => Mage::helper('M2ePro')->__('Clear Log'),
                    'url' => array('base'=>
                                      '*/adminhtml_listingOther/clearLog/back/'
                                      .Mage::helper('M2ePro')
                                          ->makeBackUrlParam('*/adminhtml_listingOther/index',
                                              array('tab'=>
                                                      Ess_M2ePro_Block_Adminhtml_Component_Abstract::TAB_ID_AMAZON))
                                      .'/'),
                    'field'     => 'id',
                    'confirm'  => Mage::helper('M2ePro')->__('Are you sure?')
                )
            )
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        // Set mass-action identifiers
        //--------------------------------
        $this->setMassactionIdField('`main_table`.id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        //--------------------------------

        // Set mass-action
        //--------------------------------
        $this->getMassactionBlock()->addItem('map_products', array(
            'label'   => Mage::helper('M2ePro')->__('Map Item(s) Automatically'),
            'url'     => '',
            'confirm' => Mage::helper('M2ePro')->__('Are you sure?')
        ));
        $this->getMassactionBlock()->addItem('move_to_listing', array(
            'label'   => Mage::helper('M2ePro')->__('Move Item(s) to Listing'),
            'url'     => '',
            'confirm' => Mage::helper('M2ePro')->__('Are you sure?')
        ));
        //--------------------------------

        return parent::_prepareMassaction();
    }

    // ####################################

    public function callbackColumnProductId($value, $row, $column, $isExport)
    {
        if (empty($value)) {
            $productTitle = Mage::helper('M2ePro')->escapeHtml($row->getData('title'));
            $productTitle = Mage::helper('M2ePro')->escapeJs($productTitle);
            if (strlen($productTitle) > 60) {
                $productTitle = substr($productTitle, 0, 60) . '...';
            }
            $htmlValue = '&nbsp;<a href="javascript:void(0);"
                                    onclick="AmazonListingOtherMapToProductHandlerObj.openPopUp(\''.
                                        $productTitle.
                                        '\','.
                                        (int)$row->getId().
                                    ');">' . Mage::helper('M2ePro')->__('Map') . '</a>';

            if (Mage::helper('M2ePro/Server')->isDeveloper()) {
                $htmlValue .= '<br>' . $row->getId();
            }
            return $htmlValue;
        }

        $htmlValue = '&nbsp<a href="'
                     .$this->getUrl('adminhtml/catalog_product/edit',
                                    array('id' => $row->getData('product_id')))
                     .'" target="_blank">'
                     .$row->getData('product_id')
                     .'</a>';

        $htmlValue .= '&nbsp&nbsp&nbsp<a href="javascript:void(0);"'
                      .' onclick="AmazonListingMoveToListingHandlerObj.getGridHtml('
                      .json_encode(array((int)$row->getData('id')))
                      .')">'
                      .Mage::helper('M2ePro')->__('Move')
                      .'</a>';

        if (Mage::helper('M2ePro/Server')->isDeveloper()) {
            $htmlValue .= '<br>' . $row->getId();
        }

        return $htmlValue;
    }

    public function callbackColumnProductTitle($value, $row, $column, $isExport)
    {
        if (strlen($value) > 60) {
            $value = substr($value, 0, 60) . '...';
        }

        $value = '<span>' . Mage::helper('M2ePro')->escapeHtml($value) . '</span>';

        $tempSku = $row->getData('sku');
        empty($tempSku) && $tempSku = 'N/A';

        $value .= '<br/><strong>'
                  .Mage::helper('M2ePro')->__('SKU')
                  .':</strong> '
                  .Mage::helper('M2ePro')->escapeHtml($tempSku);

        return $value;
    }

    public function callbackColumnGeneralId($value, $row, $column, $isExport)
    {
        $url = Mage::helper('M2ePro/Component_Amazon')->getItemUrl($value, $row->getData('marketplace_id'));
        return '<a href="'.$url.'" target="_blank">'.$value.'</a>';
    }

    public function callbackColumnAvailableQty($value, $row, $column, $isExport)
    {
        if (is_null($value) || $value === '') {
            return Mage::helper('M2ePro')->__('N/A');
        }

        if ((bool)$row->getData('is_afn_channel')) {
            return '--';
        }

        if ($value <= 0) {
            return '<span style="color: red;">0</span>';
        }

        return $value;
    }

    public function callbackColumnPrice($value, $row, $column, $isExport)
    {
        if (is_null($value) || $value === '') {
            return Mage::helper('M2ePro')->__('N/A');
        }

        if ((float)$value <= 0) {
            return '<span style="color: #f00;">0</span>';
        }

        return Mage::app()->getLocale()->currency($row->getData('currency'))->toCurrency($value);
    }

    public function callbackColumnAfnChannel($value, $row, $column, $isExport)
    {
        switch ($row->getData('is_afn_channel')) {
            case Ess_M2ePro_Model_Amazon_Listing_Product::IS_ISBN_GENERAL_ID_YES:
                $value = '<span style="font-weight: bold;">' . $value . '</span>';
                break;

            default:
                break;
        }

        return $value;
    }

    public function callbackColumnStatus($value, $row, $column, $isExport)
    {
        switch ($row->getData('status')) {

            case Ess_M2ePro_Model_Listing_Product::STATUS_UNKNOWN:
            case Ess_M2ePro_Model_Listing_Product::STATUS_NOT_LISTED:
                $value = '<span style="color: gray;">' . $value . '</span>';
                break;

            case Ess_M2ePro_Model_Listing_Product::STATUS_LISTED:
                $value = '<span style="color: green;">' . $value . '</span>';
                break;

            case Ess_M2ePro_Model_Listing_Product::STATUS_STOPPED:
                $value = '<span style="color: red;">' . $value . '</span>';
                break;

            case Ess_M2ePro_Model_Listing_Product::STATUS_BLOCKED:
                $value = '<span style="color: orange; font-weight: bold;">' . $value . '</span>';
                break;

            default:
                break;
        }

        return $value.$this->getViewLogIconHtml($row->getId());
    }

    public function callbackColumnStartDate($value, $row, $column, $isExport)
    {
        if (empty($value)) {
            return Mage::helper('M2ePro')->__('N/A');
        }

        return $value;
    }

    public function callbackColumnEndDate($value, $row, $column, $isExport)
    {
        if (empty($value)) {
            return Mage::helper('M2ePro')->__('N/A');
        }

        return $value;
    }

    protected function callbackFilterTitle($collection, $column)
    {
        $value = $column->getFilter()->getValue();

        if ($value == null) {
            return;
        }

        $collection->getSelect()->where('second_table.title LIKE ? OR second_table.sku LIKE ?', '%'.$value.'%');
    }

    // ####################################

    public function getViewLogIconHtml($amazonListingProductId)
    {
        // Get last messages
        //--------------------------
        $dbSelect = $this->connRead->select()
                                   ->from(Mage::getResourceModel('M2ePro/Listing_Other_Log')->getMainTable(),
                                          array('action_id','action','type','description','create_date','initiator'))
                                   ->where('`listing_other_id` = ?',(int)$amazonListingProductId)
                                   ->where('`action_id` IS NOT NULL')
                                   ->order(array('id DESC'))
                                   ->limit(30);

        $logRows = $this->connRead->fetchAll($dbSelect);
        //--------------------------

        // Get grouped messages by action_id
        //--------------------------
        $actionsRows = array();
        $tempActionRows = array();
        $lastActionId = false;

        foreach ($logRows as $row) {

            $row['description'] = Mage::helper('M2ePro')->escapeHtml($row['description']);
            $row['description'] = Mage::getModel('M2ePro/Log_Abstract')->decodeDescription($row['description']);

            if ($row['action_id'] !== $lastActionId) {
                if (count($tempActionRows) > 0) {
                    $actionsRows[] = array(
                        'type' => $this->getMainTypeForActionId($tempActionRows),
                        'date' => $this->getMainDateForActionId($tempActionRows),
                        'action' => $this->getActionForAction($tempActionRows[0]),
                        'initiator' => $this->getInitiatorForAction($tempActionRows[0]),
                        'items' => $tempActionRows
                    );
                    $tempActionRows = array();
                }
                $lastActionId = $row['action_id'];
            }
            $tempActionRows[] = $row;
        }

        if (count($tempActionRows) > 0) {
            $actionsRows[] = array(
                'type' => $this->getMainTypeForActionId($tempActionRows),
                'date' => $this->getMainDateForActionId($tempActionRows),
                'action' => $this->getActionForAction($tempActionRows[0]),
                'initiator' => $this->getInitiatorForAction($tempActionRows[0]),
                'items' => $tempActionRows
            );
        }

        if (count($actionsRows) <= 0) {
            return '';
        }

        $actionsRows = array_slice($actionsRows,0,3);
        $lastActionRow = $actionsRows[0];
        //--------------------------

        // Get log icon
        //--------------------------
        $icon = 'normal';
        $iconTip = Mage::helper('M2ePro')->__('Last action was completed successfully.');

        if ($lastActionRow['type'] == Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR) {
            $icon = 'error';
            $iconTip = Mage::helper('M2ePro')->__('Last action was completed with error(s).');
        }
        if ($lastActionRow['type'] == Ess_M2ePro_Model_Log_Abstract::TYPE_WARNING) {
            $icon = 'warning';
            $iconTip = Mage::helper('M2ePro')->__('Last action was completed with warning(s).');
        }

        $iconSrc = $this->getSkinUrl('M2ePro').'/images/log_statuses/'.$icon.'.png';
        //--------------------------

        $html = '<span style="float:right;">';
        $html .= '<a title="'.$iconTip.'" id="lpv_grid_help_icon_open_'.(int)$amazonListingProductId
              .'" href="javascript:void(0);" onclick="AmazonListingItemGridHandlerObj.viewItemHelp('
              .(int)$amazonListingProductId.',\''.base64_encode(json_encode($actionsRows)).'\');"><img src="'
              .$iconSrc.'" /></a>';
        $html .= '<a title="'.$iconTip.'" id="lpv_grid_help_icon_close_'.(int)$amazonListingProductId
           .'" style="display:none;" href="javascript:void(0);" onclick="AmazonListingItemGridHandlerObj.hideItemHelp('
           .(int)$amazonListingProductId.');"><img src="'.$iconSrc.'" /></a>';
        $html .= '</span>';

        return $html;
    }

    public function getActionForAction($actionRows)
    {
        $string = '';

        switch ((int)$actionRows['action']) {
            case Ess_M2ePro_Model_Listing_Other_Log::ACTION_CHANGE_STATUS_ON_CHANNEL:
                $string = Mage::helper('M2ePro')->__('Status Change');
                break;
        }

        return $string;
    }

    public function getInitiatorForAction($actionRows)
    {
        $string = '';

        switch ($actionRows['initiator']) {
            case Ess_M2ePro_Model_Log_Abstract::INITIATOR_UNKNOWN:
                $string = '';
                break;
            case Ess_M2ePro_Model_Log_Abstract::INITIATOR_USER:
                $string = Mage::helper('M2ePro')->__('Manual');
                break;
            case Ess_M2ePro_Model_Log_Abstract::INITIATOR_EXTENSION:
                $string = Mage::helper('M2ePro')->__('Automatic');
                break;
        }

        return $string;
    }

    public function getMainTypeForActionId($actionRows)
    {
        $type = Ess_M2ePro_Model_Log_Abstract::TYPE_SUCCESS;

        foreach ($actionRows as $row) {
            if ($row['type'] == Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR) {
                $type = Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR;
                break;
            }
            if ($row['type'] == Ess_M2ePro_Model_Log_Abstract::TYPE_WARNING) {
                $type = Ess_M2ePro_Model_Log_Abstract::TYPE_WARNING;
            }
        }

        return $type;
    }

    public function getMainDateForActionId($actionRows)
    {
        $format = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
        return Mage::app()->getLocale()->date(strtotime($actionRows[0]['create_date']))->toString($format);
    }

    // ####################################

    public function _toHtml()
    {
        $javascriptsMain = <<<JAVASCRIPT
<script type="text/javascript">

    if (typeof AmazonListingItemGridHandlerObj != 'undefined') {
        AmazonListingItemGridHandlerObj.afterInitPage();
    }

    Event.observe(window, 'load', function() {
        setTimeout(function() {
            AmazonListingItemGridHandlerObj.afterInitPage();
        }, 350);
    });

</script>
JAVASCRIPT;

        return parent::_toHtml().$javascriptsMain;
    }

    // ####################################

    public function getGridUrl()
    {
        return $this->getUrl('*/adminhtml_amazon_listingOther/grid', array('_current' => true));
    }

    public function getRowUrl($row)
    {
        return false;
    }

    // ####################################
}