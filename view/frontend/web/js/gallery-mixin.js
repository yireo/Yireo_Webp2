define([
    'jquery',
    './has-webp',
    "domReady!"
], function ($, hasWebP) {
    'use strict';

    var mixin = {
        initialize: function (config, element) {
            if (hasWebP()) {
                this._replaceImageDataWithWebp(config.data);
            }
            this._super(config, element);
        },

        _replaceImageDataWithWebp: function (imagesData) {
            if (_.isEmpty(imagesData)) {
                return;
            }

            $.each(imagesData, function (key, imageData) {
                imageData['full'] = imageData['full_webp'];
                imageData['img'] = imageData['img_webp'];
                imageData['thumb'] = imageData['thumb_webp'];
            });
        }
    };

    return function (target) {
        return target.extend(mixin);
    }
});