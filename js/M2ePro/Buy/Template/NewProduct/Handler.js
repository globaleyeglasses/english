BuyTemplateNewProductHandler = Class.create();
BuyTemplateNewProductHandler.prototype = Object.extend(new CommonHandler(), {

    //----------------------------------

    popups: [],

    //----------------------------------

    initialize: function(M2ePro,attributesHandler, attributeSetHandler)
    {
        var self = this;

        self.M2ePro = M2ePro;
        self.attributesHandler = attributesHandler;
        self.attributesHandler.categoryHandler = self;
        self.attributeSetHandler = attributeSetHandler;

        self.initPopUp('search_category',850,550,self.M2ePro.text.search_category);
        self.initPopUp('browse_category',600,500,self.M2ePro.text.browse_category);

        self.categoriesTr  = $('categories_tr');
        self.changeButton  = $('category_change_button_container');
        self.confirmButton = $('category_confirm_button_container');
        self.nodeTitleEl   = $('node_title');
        self.categoriesContainer = $('categories_container');

        self.searchCategoryButton = $('search_category_button');
        self.browseCategoryButton = $('browse_category_button');

        self.searchKeywordsInput = $('search_keywords');

        self.categoryPathHiddenInput = new Element('input', {
            'type': 'hidden',
            'name': "category[path]",
            'class': 'required-entry'
        });

        self.notes = [];
        self.notes.image  = [
            '- Must be at least 500 pixels in width or height',
            '- No more than 5 megabytes (MB) in size',
            '- In the JPG/JPEG format',
            '- Only contain the product being sold',
            '- Be in focus with realistic color with a pure white background',
            '- Cannot contain additional text, graphics or inset images'
        ];
        self.notes.description = [
            'Text to describe the product. Should be one block of text or a single paragraph. Do not use special characters and html tags'
        ];

        self.changeButton.insert({
            after: self.categoryPathHiddenInput
        });

        if (self.M2ePro.formData.category.category_path) {
            self.categoryPathHiddenInput.value = self.M2ePro.formData.category.category_path;
            self.showSpanWithCategoryPath(self.M2ePro.formData.category.category_path);
        }

        Validation.add('M2ePro-validation-float', 'Invalid input data. Decimal value required. Example 12.05', function(value, element) {
            if (!element.up('tr').visible()) {
                return true;
            }
            return self['floatTypeValidator'](value,element);
        });
    },

    //----------------------------------

    floatTypeValidator: function(value, element) {
        var pattern = /[^\d.]+/;
        if (pattern.exec(value) != null) {
            return false;
        }

        if (isNaN(parseFloat(value)) ||
            substr_count(value,'.') > 1 ||
            value.substr(-1) == '.') {
            return false;
        }

        if (value.indexOf('.') != -1 && (value.substring(value.indexOf('.')+1)).length > 2) {
            return false;
        }

        return true;
    },

    //----------------------------------

    showSpanWithCategoryPath: function(path)
    {
        var spanEl = new Element('span',{'class': 'nobr','style': 'font-weight: bold'});
        this.changeButton.insert({'before': spanEl});
        spanEl.insert(path);
    },

    //----------------------------------

    checkAttributeSetSelection: function()
    {
        if (!AttributeSetHandlerObj.checkAttributeSetSelection()) {
            buyTemplateNewProductEditTabsJsTabs.showTabContent($('buyTemplateNewProductEditTabs_general'));
        }
    },

    checkAttributesReady: function()
    {
        if (BuyTemplateNewProductHandlerObj.attributesHandler.rakutenCategoryId == 0) {
            alert(this.M2ePro.text.select_category_first);
            buyTemplateNewProductEditTabsJsTabs.showTabContent($('buyTemplateNewProductEditTabs_general'));
        }
    },

    //----------------------------------

    node_title_change: function(element)
    {
        this.parentId = element.down('option[value=' + element.value + ']').getAttribute('id_node_key');

        this.categoriesTr.show();
        this.categoriesContainer.show();
        this.categoriesContainer.update();
        this.removeSpanWithCategoryPath();
        this.changeButton.hide();
        this.confirmButton.hide();

        this.getCategories(element.down('option[value=' + element.value + ']').getAttribute('node_id'),function(transport) {
            this.categories = transport.responseText.evalJSON();
            this.showCategories(this.categoriesContainer);
        });

    },

    //----------------------------------

    getCategories: function(nodeId,callback)
    {
        var self = this;

        new Ajax.Request(self.M2ePro.url.getCategories,
            {
                method : 'get',
                asynchronous : true,
                parameters : {
                    node_id : nodeId
                },
                onSuccess: function(transport) {
                    callback.call(self,transport);
                }
            });
    },

    //----------------------------------

    showCategories: function(container)
    {
        var self       = this;
        var categories = [];
        self.categoryPathHiddenInput.value = '';
        self.confirmButton.hide();
        self.removeContainers(container.parentNode,container);
        var selectEl = container.appendChild(new Element('select'));
        selectEl.appendChild(new Element('option',{'value': 'empty','style': 'display: none'}));

        self.categories.each(function(category) {
            category.parent_id === self.parentId && categories.push(category)
        });

        if (categories.length == 0 || self.getCategoryInfo('id',self.parentId).is_listable == 1) {
            self.confirmButton.show();
            if (categories.length == 0) {
                selectEl.hide();
                return '';
            }
        }

        categories.each(function(category) {
            selectEl.appendChild(new Element('option',{'value': category.id}))
                .update(category.title);
        });

        selectEl.observe('change',function(event) {
            self.parentId = this.value;
            self.showCategories(container.appendChild(new Element('div')));
        });
    },

    //----------------------------------

    getCategoryInfo: function(key,value)
    {
        var info = {};
        this.categories.each(function(category) {
            if (value == category[key]) {
                return info = category;
            }
        });
        return info;
    },

    removeContainers: function(container,doNotDeleteContainer)
    {
        container.childElements().each(function(child) {
            child.localName == 'div' && child != doNotDeleteContainer && container.removeChild(child)
        });
    },

    //----------------------------------

    confirmCategory: function()
    {
        var self = BuyTemplateNewProductHandlerObj;

        this.categoriesContainer.hide();
        this.confirmButton.hide();

        var categoryInfo = this.getCategoryInfo('id',this.parentId);
        var categoryPath = categoryInfo.path.replace(/->/g,' > ');

        this.categoryPathHiddenInput.value = categoryPath;

        this.changeButton.show();
        this.categoriesTr.hide();
        $$('input[name=category[node_title]]').shift().value = this.nodeTitleEl.value;

        this.nodeTitleEl.value = '';

        this.browse_category.close();
        this.searchCategoryButton.parentNode.parentNode.hide();

        this.showSpanWithCategoryPath(categoryPath);

        // -- render Attributes
        $('rakuten_category_id').value = categoryInfo.category_id;
        self.attributesHandler.rakutenCategoryId = categoryInfo.category_id;
        self.attributesHandler.showAttributes(categoryInfo.category_id);
    },

    // ---------------------------------

    changeCategory: function()
    {
        this.changeButton.hide();
        this.confirmButton.show();
        this.removeSpanWithCategoryPath();
        this.attributesHandler.showAttributes(0);
        this.categoriesContainer.show();
        this.searchCategoryButton.parentNode.parentNode.show();
        this.categoryPathHiddenInput.value = '';
        BuyTemplateNewProductHandlerObj.attributesHandler.rakutenCategoryId = 0;
    },

    //----------------------------------

    removeSpanWithCategoryPath: function()
    {
        var span = this.changeButton.previous('span');
        span && span.remove();
    },

    //----------------------------------

    confirmAttributeSets: function()
    {
        var self = BuyTemplateNewProductHandlerObj;

        self.attributeSetHandler.confirmAttributeSets();
        self.attribute_sets_confirm()

        // -- render Attributes
        var category_id = $('rakuten_category_id').value;
        BuyTemplateNewProductHandlerObj.attributesHandler.rakutenCategoryId = category_id;
        BuyTemplateNewProductHandlerObj.attributesHandler.showAttributes(category_id);
    },

    //----------------------------------

    initPopUp: function(contentId,width,height,title)
    {
        this[contentId] = new Window({
            draggable: true,
            resizable: true,
            closable: true,
            className: "magento",
            windowClassName: "popup-window",
            top: 100,
            title: title,
            width: width,
            height: height,
            zIndex: 100,
            recenterAuto: false,
            hideEffect: Element.hide,
            showEffect: Element.show
        });

        this[contentId].getContent().update($(contentId));
    },

    //----------------------------------

    searchClick: function()
    {
        $('error_block').hide();

        var keywords = this.searchKeywordsInput.value.trim();

        new Ajax.Request(this.M2ePro.url.searchCategory,
            {
                method : 'get',
                asynchronous : true,
                parameters : {
                    keywords : keywords
                },
                onSuccess: (function(transport) {

                    var response = transport.responseText;
                    if (response.length == 0) {
                        this.resetSearchClick();
                        return $('error_block').show() && $('error_message').update(this.M2ePro.text.internal_error);
                    }
                    if (response.isJSON() && response.evalJSON().result == 'error') {
                        this.resetSearchClick();
                        return $('error_block').show() && $('error_message').update(response.evalJSON().message);
                    }
                    $('reset_category_popup_button').show();
                    $('searchGrid').update(response);

                }).bind(this)
            });
    },

    resetSearchClick: function()
    {
        self = BuyTemplateNewProductHandler;
        this.searchKeywordsInput.value = '';
        this.searchKeywordsInput.simulate('blur');
        $('searchGrid').update();
        $('reset_category_popup_button').hide();
    },

    confirmSearchClick: function(categoryInfo)
    {
        this.changeButton.show();
        this.categoriesContainer.hide();
        this.confirmButton.hide();

        var categoryPath = categoryInfo.path.replace(/->/g,' > ');

        this.categoryPathHiddenInput.value = categoryPath;

        this.categoriesTr.hide();

        $$('input[name=category[node_title]]').shift().value = this.nodeTitleEl.select('option[node_id='+ categoryInfo.node_id +']').shift().getAttribute('value');

        this.search_category.close();

        this.searchCategoryButton.parentNode.parentNode.hide();
        this.showSpanWithCategoryPath(categoryPath);

        // -- render Attributes
        $('rakuten_category_id').value = categoryInfo.category_id;
        BuyTemplateNewProductHandlerObj.attributesHandler.showAttributes(categoryInfo.category_id);
        BuyTemplateNewProductHandlerObj.attributesHandler.rakutenCategoryId = categoryInfo.category_id;
        this.resetSearchClick();
    },

    //----------------------------------

    isbn_mode_change: function()
    {
        var self = BuyTemplateNewProductHandlerObj;
        var handlers = {};

        handlers[self.ISBN_MODE_NONE] = function() {
            $('isbn_custom_attribute_tr').hide();
        };

        handlers[self.ISBN_MODE_CUSTOM_ATTRIBUTE] = function() {
            $('isbn_custom_attribute_tr').show();
        };

        handlers[this.value].call(self);
    },

    asin_mode_change: function()
    {
        var self = BuyTemplateNewProductHandlerObj;
        var handlers = {};

        handlers[self.ASIN_MODE_NONE] = function() {
            $('asin_custom_attribute_tr').hide();
        };

        handlers[self.ASIN_MODE_CUSTOM_ATTRIBUTE] = function() {
            $('asin_custom_attribute_tr').show();
        };

        handlers[this.value].call(self);
    },

    mfg_part_number_mode_change: function()
    {
        var self = BuyTemplateNewProductHandlerObj;
        var handlers = {};

        handlers[self.MFG_PART_NUMBER_MODE_CUSTOM_VALUE] = function() {
            $('mfg_part_number_custom_value_tr').show();
            $('mfg_part_number_custom_attribute_tr').hide();
        };

        handlers[self.MFG_PART_NUMBER_MODE_CUSTOM_ATTRIBUTE] = function() {
            $('mfg_part_number_custom_value_tr').hide();
            $('mfg_part_number_custom_attribute_tr').show();
        };

        if (this.value != "") {
            handlers[this.value].call(self);
        }
    },

    product_set_id_mode_change: function()
    {
        var self = BuyTemplateNewProductHandlerObj;
        var handlers = {};

        handlers[self.PRODUCT_SET_ID_MODE_NONE] = function() {
            $('product_set_id_custom_attribute_tr').hide();
            $('product_set_id_custom_value_tr').hide();
        };

        handlers[self.PRODUCT_SET_ID_MODE_CUSTOM_VALUE] = function() {
            $('product_set_id_custom_attribute_tr').hide();
            $('product_set_id_custom_value_tr').show();
        };

        handlers[self.PRODUCT_SET_ID_MODE_CUSTOM_ATTRIBUTE] = function() {
            $('product_set_id_custom_attribute_tr').show();
            $('product_set_id_custom_value_tr').hide();
        };

        handlers[this.value].call(self);
    },

    //----------------------------------

    title_mode_change: function()
    {
        var self = BuyTemplateNewProductHandlerObj;
        var handlers = {};

        handlers[self.TITLE_MODE_PRODUCT_NAME] = function() {
            $('custom_title_tr').hide();
        };

        handlers[self.TITLE_MODE_CUSTOM_TEMPLATE] = function() {
            $('custom_title_tr').show();
        };

        if (this.value != "") {
            handlers[this.value].call(self);
        }
    },

    description_mode_change: function()
    {
        var self = BuyTemplateNewProductHandlerObj;

        $$('.c-custom_description_tr').invoke('hide');

        if (this.value == self.DESCRIPTION_MODE_CUSTOM_TEMPLATE) {
            if (AttributeSetHandlerObj.checkAttributeSetSelection()) {
                $$('.c-custom_description_tr').invoke('show');
            } else {
                this.value = 0;
            }
        }
    },

    main_image_mode_change: function()
    {
        var self = BuyTemplateNewProductHandlerObj;

        if (this.value == self.IMAGE_MAIN_MODE_PRODUCT_BASE) {
            $('main_image_attribute_tr').hide();
        } else if (this.value == self.IMAGE_MAIN_MODE_CUSTOM_ATTRIBUTE){
            $('main_image_attribute_tr').show();
        }
    },

    additional_images_mode_change: function()
    {
        var self = BuyTemplateNewProductHandlerObj;
        var handlers = {};

        handlers[self.ADDITIONAL_IMAGES_MODE_NONE] = function() {
            $('additional_images_attribute_tr').hide();
            $('additional_images_limit_tr').hide();
        };

        handlers[self.ADDITIONAL_IMAGES_MODE_PRODUCT] = function() {
            $('additional_images_limit_tr').show();
            $('additional_images_attribute_tr').hide();
        };

        handlers[self.ADDITIONAL_IMAGES_MODE_CUSTOM_ATTRIBUTE] = function() {
            $('additional_images_limit_tr').hide();
            $('additional_images_attribute_tr').show();
        };

        handlers[this.value].call(self);
    },

    keywords_mode_change: function()
    {
        var self = BuyTemplateNewProductHandlerObj;
        var handlers = {};

        handlers[self.KEYWORDS_MODE_NONE] = function() {
            $('keywords_custom_attribute_tr').hide();
            $('keywords_custom_value_tr').hide();
        };

        handlers[self.KEYWORDS_MODE_CUSTOM_ATTRIBUTE] = function() {
            $('keywords_custom_attribute_tr').show();
            $('keywords_custom_value_tr').hide();
        };

        handlers[self.KEYWORDS_MODE_CUSTOM_VALUE] = function() {
            $('keywords_custom_attribute_tr').hide();
            $('keywords_custom_value_tr').show();
        };

        handlers[this.value].call(self);
    },

    features_mode_change: function()
    {
        var self = BuyTemplateNewProductHandlerObj;

        if (this.value == self.FEATURES_MODE_NONE) {
            $$('.features_tr').invoke('hide');
            $$('input[name="category[features_template][]"]').each(function(obj) {
                obj.value = '';
            });
            $('features_actions_tr').hide();
        } else {
            if (AttributeSetHandlerObj.checkAttributeSetSelection()) {
                var visibleElementsCounter = 0;

                $$('.features_tr').each(function(obj) {
                    if (visibleElementsCounter == 0 || $(obj).select('input[name="category[features_template][]"]')[0].value != '') {
                        if ($(obj).select('input[name="category[features_template][]"]')[0].value == '') {
                            $('show_features_action').addClassName('action-disabled');
                        }
                        $('show_features_action').removeClassName('action-disabled');
                        $(obj).show();
                        visibleElementsCounter++;
                    }
                });

                var emptyVisibleFeaturesExist = $$('.features_tr').any(function(obj) {
                    return $(obj).visible() && $(obj).select('input[name="category[features_template][]"]')[0].value == '';
                });
                $('features_actions_tr').show();

                if (visibleElementsCounter > BuyTemplateNewProductHandlerObj.FEATURES_COUNT - 1 || emptyVisibleFeaturesExist) {
                    $('show_features_action').addClassName('action-disabled');
                }
            } else {
                this.value = self.FEATURES_MODE_NONE;
            }
        }
    },

    weight_mode_change:function()
    {
        var self = BuyTemplateNewProductHandlerObj;
        var handlers = {};

        handlers[self.WEIGHT_MODE_CUSTOM_VALUE] = function() {
            $('weight_custom_value_tr').show();
            $('weight_custom_attribute_tr').hide();
        };

        handlers[self.WEIGHT_MODE_CUSTOM_ATTRIBUTE] = function() {
            $('weight_custom_value_tr').hide();
            $('weight_custom_attribute_tr').show();
        };

        if (this.value != "") {
            handlers[this.value].call(self);
        }
    },

    //----------------------------------

    showFeature: function()
    {
        var emptyVisibleFeaturesExist = $$('.features_tr').any(function(obj) {
            return $(obj).visible() && $(obj).select('input[name="category[features_template][]"]')[0].value == '';
        });

        if (emptyVisibleFeaturesExist) {
            $('show_features_action').addClassName('action-disabled');
            return;
        }

        var hiddenFeatures = $$('.features_tr').findAll(function(obj) {
            return !$(obj).visible();
        });

        if (hiddenFeatures.size() == 0) {
            return;
        }

        hiddenFeatures.shift().show();
        $('hide_features_action').removeClassName('action-disabled');
        $('show_features_action').addClassName('action-disabled');
    },

    hideFeature: function()
    {
        var visibleElements = $$('.features_tr').findAll(Element.visible);

        if (visibleElements.size() == 1) {
            var featuresModeElement = $('features_mode');
            featuresModeElement.value = this.FEATURES_MODE_NONE;
            featuresModeElement.simulate('change');
        }

        if (visibleElements.size() > 1) {
            var lastVisibleFeaure = visibleElements.pop();
            lastVisibleFeaure.select('input[name="category[features_template][]"]')[0].value = '';
            lastVisibleFeaure.hide();
        }

        var emptyVisibleFeaturesExist = $$('.features_tr').any(function(obj) {
            return $(obj).visible() && $(obj).select('input[name="category[features_template][]"]')[0].value == '';
        });

        if (visibleElements.size() != BuyTemplateNewProductHandlerObj.FEATURES_COUNT && !emptyVisibleFeaturesExist) {
            $('show_features_action').removeClassName('action-disabled');
        }
    },

    allowAddFeature: function(elem)
    {
        var visibleElements= $$('.features_tr').findAll(Element.visible);
        var emptyVisibleFeaturesExist = $$('.features_tr').any(function(obj) {
            return $(obj).visible() && $(obj).select('input[name="category[features_template][]"]')[0].value == '';
        });

        if (visibleElements.size() != BuyTemplateNewProductHandlerObj.FEATURES_COUNT && !emptyVisibleFeaturesExist) {
            $('show_features_action').removeClassName('action-disabled');
        }
    },

    //----------------------------------

    attribute_sets_confirm: function()
    {
        var self = BuyTemplateNewProductHandlerObj;

        var AttributeSetHandlerObj = self.attributeSetHandler;

        AttributeSetHandlerObj.renderAttributesWithEmptyHiddenOption(
            'category[seller_sku_custom_attribute]',
            'seller_sku_custom_attribute_td',
            M2ePro.formData.category.seller_sku_custom_attribute,
            '280');
        AttributeSetHandlerObj.renderAttributesWithEmptyHiddenOption(
            'category[gtin_custom_attribute]',
            'gtin_custom_attribute_td',
            M2ePro.formData.category.gtin_custom_attribute,
            '280');
        AttributeSetHandlerObj.renderAttributesWithEmptyHiddenOption(
            'category[isbn_custom_attribute]',
            'isbn_custom_attribute_td',
            M2ePro.formData.category.isbn_custom_attribute,
            '280');
        AttributeSetHandlerObj.renderAttributesWithEmptyHiddenOption(
            'category[asin_custom_attribute]',
            'asin_custom_attribute_td',
            M2ePro.formData.category.asin_custom_attribute,
            '280');
        AttributeSetHandlerObj.renderAttributes(
            'select_attributes_for_mfg_name',
            'select_attributes_for_manufacturer_span',
            0,
            '150');
        AttributeSetHandlerObj.renderAttributesWithEmptyHiddenOption(
            'category[mfg_part_number_custom_attribute]',
            'mfg_part_number_custom_attribute_td',
            M2ePro.formData.category.mfg_part_number_custom_attribute,
            '280');

        AttributeSetHandlerObj.renderAttributesWithEmptyHiddenOption(
            'category[product_set_id_custom_attribute]',
            'product_set_id_custom_attribute_td',
            M2ePro.formData.category.product_set_id_custom_attribute,
            '280');

        // -- core
        AttributeSetHandlerObj.renderAttributes('select_attributes','select_attributes_span');
        AttributeSetHandlerObj.renderAttributes(
            'select_attributes_for_title',
            'select_attributes_for_title_span',
            0,
            '150');
        AttributeSetHandlerObj.renderAttributesWithEmptyHiddenOption(
            'category[main_image_attribute]',
            'main_image_attribute_td',
            M2ePro.formData.category.main_image_attribute,
            '280');
        AttributeSetHandlerObj.renderAttributesWithEmptyHiddenOption(
            'category[additional_images_attribute]',
            'additional_images_attribute_td',
            M2ePro.formData.category.additional_images_attribute,
            '280'
        );
        AttributeSetHandlerObj.renderAttributesWithEmptyHiddenOption(
            'category[keywords_custom_attribute]',
            'keywords_custom_attribute_td',
            M2ePro.formData.category.keywords_custom_attribute,
            '280'
        );
        for (var i = 0; i < BuyTemplateNewProductHandlerObj.FEATURES_COUNT; i++) {
            AttributeSetHandlerObj.renderAttributes(
                'select_attributes_for_features_' + i,
                'select_attributes_for_features_' + i + '_span',
                0,
                '150');
        }
        AttributeSetHandlerObj.renderAttributesWithEmptyHiddenOption(
            'category[weight_custom_attribute]',
            'weight_custom_attribute_td',
            M2ePro.formData.category.weight_custom_attribute,
            '280'
        );
    },

    //-----------------------------------

    renderHelpIcon: function(param, notes) {

        var self = this;
        var win;

        winContent = new Element('ul',{'style': 'text-align: left; margin-top: 10px'});

        notes.forEach(function(element, index) {
            winContent.insert('<li><p>' + element + '</p></li>');
        });

        $(param).observe('click',function() {

            var position = param.positionedOffset()

            win = win || new Window({
                className: "magento",
                title: 'All of your product images should meet the following rules:',
                width: 400,
                height: 180,
                zIndex: 100,
                top: position.top - 100,
                left: position.left + 100
            });

            win.setHTMLContent(winContent.outerHTML);

            if (win.visible) {
                win.hide();
            } else {
                self.popups.each(function(popup) {
                    popup.close();
                });
                win.show();
            }

            self.popups = [win];
        });
    }
});