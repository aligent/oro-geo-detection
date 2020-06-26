/*global define*/
define(function(require) {
    'use strict';

    var GeoDetectionModalComponent;
    var $ = require('jquery');
    var BaseComponent = require('oroui/js/app/components/base/component');
    var Modal = require('oroui/js/modal');
    require('jquery.cookie');

    GeoDetectionModalComponent = BaseComponent.extend({
        modal: null,

        options: null,

        initialize: function (options) {
            this.options = options;
            // Create Modal with our content
            this.modal = new Modal({
                title: 'It looks like you are visiting from ' + this.options.suggestedSiteLocale,
                content: 'Please visit your countries store, or continue to ' + this.options.defaultSiteUrl,
                cancelText: 'Visit ' + this.options.defaultSiteLocale,
                okText: 'Visit ' + this.options.suggestedSiteLocale
            });

            // Display Modal immediately on page load
            this.modal.open();

            // Bind events
            var self = this;
            this.modal.on({
                'ok': this.onRedirect.bind(self),
                'cancel': this.onClose
            });
        },

        // Set cookie so the modal does not display again
        onClose: function() {
            $.cookie('displayRedirectModal', false, {expires: 365});
        },

        // Redirect user to correct site
        onRedirect: function () {
            var url = this.options.suggestedSiteUrl;

            // If the URL does not already have a scheme prepend HTTPS
            if (!url.match(/^[a-zA-Z]+:\/\//))
            {
                url = 'https://' + url;
            }

            window.location.replace(url);
        }
    });

    return GeoDetectionModalComponent;
});