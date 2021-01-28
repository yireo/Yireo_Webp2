define([
    'jquery',
    './has-webp',
    "domReady!"
], function ($, hasWebP) {
    'use strict';

    return function (widget) {
        $.widget('mage.SwatchRenderer', widget, {
            _init: function () {
                if (hasWebP()) {
                    this._replaceImageDataWithWebp();
                }
                this._super();
            },

            _replaceImageDataWithWebp: function () {
                if (_.isEmpty(this.options.jsonConfig.images)) {
                    return;
                }

                $.each(this.options.jsonConfig.images, function (key, imagesData) {
                    $.each(imagesData, function (key, imageData) {
                        imageData['full'] = imageData['full_webp'];
                        imageData['img'] = imageData['img_webp'];
                        imageData['thumb'] = imageData['thumb_webp'];
                    });
                });
            }
        });

        return $.mage.SwatchRenderer;
    }
});
