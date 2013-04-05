BuyTemplateSellingFormatHandler = Class.create();
BuyTemplateSellingFormatHandler.prototype = Object.extend(new CommonHandler(), {

    //----------------------------------

    initialize: function()
    {
        this.setValidationCheckRepetitionValue('M2ePro-price-tpl-title',
            M2ePro.text.title_not_unique_error,
            'Template_SellingFormat', 'title', 'id',
            M2ePro.formData.id);

        Validation.add('M2ePro-validate-price-coefficient', M2ePro.text.price_coef_error, function(value)
        {
            if (value == '') {
                return true;
            }

            if (value == '0' || value == '0%') {
                return false;
            }

            return value.match(/^[+-]?\d+[.,]?\d*[%]?$/g);
        });
    },

    //----------------------------------

    duplicate_click: function($headId)
    {
        var attrSetEl = $('attribute_sets_fake');

        if (attrSetEl) {
            $('attribute_sets').remove();
            attrSetEl.observe('change', AttributeSetHandlerObj.changeAttributeSets);
            attrSetEl.id = 'attribute_sets';
            attrSetEl.name = 'attribute_sets[]';
            attrSetEl.addClassName('M2ePro-validate-attribute-sets');

            AttributeSetHandlerObj.confirmAttributeSets();
        }

        if ($('attribute_sets_breadcrumb')) {
            $('attribute_sets_breadcrumb').remove();
        }
        $('attribute_sets_container').show();
        $('attribute_sets_buttons_container').show();

        this.setValidationCheckRepetitionValue('M2ePro-price-tpl-title',
            M2ePro.text.title_not_unique_error,
            'Template_SellingFormat', 'title', '',
            '');

        CommonHandlerObj.duplicate_click($headId);
    },

    //----------------------------------

    attribute_sets_confirm: function()
    {
        AttributeSetHandlerObj.confirmAttributeSets();

        AttributeSetHandlerObj.renderAttributesWithEmptyOption('qty_custom_attribute', 'qty_custom_attribute_td');
        AttributeSetHandlerObj.renderAttributesWithEmptyOption('price_custom_attribute', 'price_custom_attribute_td');
    },

    //----------------------------------

    qty_mode_change: function()
    {
        var self = BuyTemplateSellingFormatHandlerObj;

        $('qty_custom_attribute_tr', 'qty_custom_value_tr').invoke('hide');

        if (this.value == self.QTY_MODE_NUMBER) {
            $('qty_custom_value_tr').show();
        } else if (this.value == self.QTY_MODE_ATTRIBUTE) {
            if (!AttributeSetHandlerObj.checkAttributeSetSelection()) {
                this.value = self.QTY_MODE_PRODUCT;
                return;
            }

            $('qty_custom_attribute_tr').show();
        }
    },

    //----------------------------------

    price_mode_change: function()
    {
        var self = BuyTemplateSellingFormatHandlerObj;

        if (this.value == self.PRICE_ATTRIBUTE) {
            if (AttributeSetHandlerObj.checkAttributeSetSelection()) {
                $('price_custom_attribute_tr').show();
            } else {
                this.value = self.PRICE_PRODUCT;
            }
        } else {
            $('price_custom_attribute_tr').hide();
        }

        if (this.value == self.PRICE_FINAL) {
            $('price_note').innerHTML = M2ePro.text.final_price_note;
        } else {
            $('price_note').innerHTML = M2ePro.text.price_note;
        }

        self.updateCustomerGroupIdVisibility();
    },

    //----------------------------------

    updateCustomerGroupIdVisibility: function()
    {
        var self = BuyTemplateSellingFormatHandlerObj;
        var displayCustomerGroup = $('price_mode').value == self.PRICE_FINAL;

        if (displayCustomerGroup) {
            $('magento_block_buy_template_selling_format_customer_group_id').show();
        } else {
            $('magento_block_buy_template_selling_format_customer_group_id').hide();
            $('customer_group_id').value = '';
        }
    }

    //----------------------------------
});
