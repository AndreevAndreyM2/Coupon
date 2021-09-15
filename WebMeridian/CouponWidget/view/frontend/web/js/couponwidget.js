define([
        'uiComponent',
        'Magento_Customer/js/customer-data',
        'jquery'
    ], function (Component, customerData, $) {
        'use strict';
        return Component.extend({
            initialize: function (config) {
                this.config = config;
                var self = this;
                this.componentWrapper = $('.js-wrapper-widget-coupon');
                this.couponwidget = customerData.get('couponwidget');
                customerData.reload(['couponwidget']);
                this._super();
                if ($.cookie("widgetcoupon") == null || $.cookie("widgetcoupon") == true ){
                    this.componentWrapper.show();
                }

                $(document).on('submit', '#form_height', function (e){
                    e.preventDefault();
                    var valueEmail = $(e.target).find("input[name='email']").val();
                    self.sendAjaxForm(valueEmail);
                });
            },

            close: function (){
                this.componentWrapper.hide();
                $.cookie("widgetcoupon", false, { expires: 7 });
            },

            sendAjaxForm: function (valueEmail){
                let url = this.config.urlController;
                jQuery.ajax({
                    url: url,
                    type: "POST",
                    data: {email:valueEmail},
                    showLoader: true,
                    cache: false,
                    success: function(response){
                        $('.js-wrapper-widget-coupon').find('.content-wrapper > span').hide();
                        $('.js-wrapper-widget-coupon').find('.email-sender').remove();
                        console.log($('.js-wrapper-widget-coupon').find('.msg-wrapper')[0])
                        var a = $('.js-wrapper-widget-coupon').find('.msg-wrapper');
                        a.append( response );
                    }
                });
                return false;
            }
        });
    }
);
