<?php
/**
 * MGT-Commerce GmbH
 * http://www.mgt-commerce.com
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@mgt-commerce.com so we can send you a copy immediately.
 *
 * @category    Mgt
 * @package     Mgt_SerpEditor
 * @author      Stefan Wieczorek <stefan.wieczorek@mgt-commerce.com>
 * @copyright   Copyright (c) 2012 (http://www.mgt-commerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mgt_SerpEditor_Block_Adminhtml_Form_Renderer_Preview extends Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
{
    protected $_baseUrl;
    
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mgt_serp_editor/form/renderer/fieldset/preview.phtml');
    }
    
    public function getElementHtml()
    {
        $helper = Mage::helper('mgt_serp_editor');
        
        $metaTitle = $this->getMetaTitle();
        $metaTitleValue = $metaTitle->getEscapedValue() ? $metaTitle->getEscapedValue() : $helper->__('This is an Example of a Title Tag that is Seventy Characters in Length');
        
        $metaDescription = $this->getMetaDescription();
        $metaDescriptionValue = $metaDescription->getEscapedValue() ? substr($metaDescription->getEscapedValue(), 0, 156).'...' : $helper->__('Here is an example of what a snippet looks like in Googles SERPs. The content that appears here is usually taken from the Meta Description tag if relevant.');

        $urlKey = $this->getUrlKey();
        $urlKeyValue = $urlKey && $urlKey->getEscapedValue() ? $this->_getProductUrlKey($urlKey->getForm()->getDataObject(), $urlKey->getEscapedValue()) : 'http://www.mydomain.com/product-url-key.html';
        
        $html = '<div class="mgt-serp-editor-preview">';
          $html .= '<div class="mgt-serp-editor-preview-title"><a id="mgt-serp-editor-preview-title" href="#">'.$metaTitleValue.'</a></div>';
          $html .= '<div class="mgt-serp-editor-preview-url" id="mgt-serp-editor-preview-url">'.$urlKeyValue.'</div>';
          $html .= '<div class="mgt-serp-editor-preview-description" id="mgt-serp-editor-preview-description">'.$metaDescriptionValue.'</div>';
          $html .= '<input type="hidden" id="mgt-serp-editor-base-url" name="mgt-serp-editor-base-url" value="'.$this->_getBaseUrl().'" />';
        $html .= '</div>';

        return $html;
    }
    
    protected function _getProductUrlKey(Mage_Catalog_Model_Product $product, $urlKey)
    {
        $baseUrl = $this->_getBaseUrl($product->getStoreId());
        $productUrlKey = sprintf('%s%s', $baseUrl, $urlKey).'.html';
        return $productUrlKey;
    }
    
    protected function _getBaseUrl($storeId = null)
    {
        if (!$this->_baseUrl) {
            $oldStore = Mage::app()->getStore();
            $store = Mage::app()->getStore($storeId);
            $this->_baseUrl = $store->getBaseUrl();
            Mage::app()->setCurrentStore($oldStore);
        }
        return $this->_baseUrl;
    }
}