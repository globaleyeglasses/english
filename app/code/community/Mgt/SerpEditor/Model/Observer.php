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

class Mgt_SerpEditor_Model_Observer 
{
    static protected $_urlKeyElement;
    
    public function setFieldRenderer(Varien_Event_Observer $observer)
    {
        $form = $observer->getEvent()->getForm();
        $helper = Mage::helper('mgt_serp_editor');
        
        if ($form->getElement('url_key')) {
            self::$_urlKeyElement = $form->getElement('url_key');
        }
        
        if ($form && $form->getElement('meta_title') && $helper->isEnabled()) {

            $layout = Mage::app()->getLayout();
            
            $metaTitle = $form->getElement('meta_title');
            $metaTitle->setRenderer($layout->createBlock('mgt_serp_editor_adminhtml/form_renderer_attribute_meta_title'));

            $metaKeywords = $form->getElement('meta_keyword');
            $metaKeywords->setRenderer($layout->createBlock('mgt_serp_editor_adminhtml/form_renderer_attribute_meta_keywords'));

            $metaDescription = $form->getElement('meta_description');
            $metaDescription->unsNote();
            $metaDescription->setRenderer($layout->createBlock('mgt_serp_editor_adminhtml/form_renderer_attribute_meta_description'));

            if (self::$_urlKeyElement) {
                $urlKeyReadOnlyElement = $metaTitle->getContainer()->addField('mgt_serp_editor_url_key', 'text', array('name' => 'MGT-URL-Key', 'label' => 'URL-Key', 'required' => false));
                $urlKeyReadOnlyRenderer = $layout->createBlock('mgt_serp_editor_adminhtml/form_renderer_url_key');
                $urlKeyReadOnlyRenderer->setUrlKey(self::$_urlKeyElement);
                $urlKeyReadOnlyElement->setRenderer($urlKeyReadOnlyRenderer);
            }

            $fieldset = $form->addFieldset('mgt_serp_editor_preview', array('legend' => $helper->__('SERP Preview'), 'class' => 'fieldset-wide'));
            
            $mgtSerpEditorPreviewElement = $fieldset->addField('mgt_erp_editor_preview', 'text', array('required'  => false));
            $mgtSerpEditorPreviewRenderer = $layout->createBlock('mgt_serp_editor_adminhtml/form_renderer_preview');
            $mgtSerpEditorPreviewRenderer->setMetaTitle($metaTitle);
            $mgtSerpEditorPreviewRenderer->setMetaKeywords($metaKeywords);
            $mgtSerpEditorPreviewRenderer->setMetaDescription($metaDescription);
            if (self::$_urlKeyElement) {
                $mgtSerpEditorPreviewRenderer->setUrlKey(self::$_urlKeyElement);
            }
            $mgtSerpEditorPreviewElement->setRenderer($mgtSerpEditorPreviewRenderer);
        }
    }
}