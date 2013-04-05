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

class Mgt_SerpEditor_Block_Adminhtml_Form_Renderer_Attribute_Meta_Keywords extends Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
{
    public function getElementHtml()
    {
        $element = $this->getElement();
        
        $value = $element->getEscapedValue();

        $html = '<div class="mgt-serp-editor-meta-keywords">';
          $html .= '<textarea onkeyup="mgtSerpEditorKeywords();" id="mgt-serp-editor-meta-keywords" style="height:65px;border:1px solid #666666;" id="'.$element->getHtmlId().'" name="'.$element->getName().'">';
          $html .= $value;
          $html .= "</textarea>";
        $html .= '</div>';

        return $html;
    }
}