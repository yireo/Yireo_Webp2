define([
    'jquery',
    "domReady!"
], function ($) {
    'use strict';

    return function (widget) {
        $.widget('mage.SwatchRenderer', widget, {
            /**
             * Update [gallery-placeholder] or [product-image-photo]
             *
             * Overwritten to make sure the image in the category grid is updated
             * when clicking a swatch image.
             *
             * @param {Array} images
             * @param {jQuery} context
             * @param {Boolean} isInProductView
             */
            updateBaseImage: function (images, context, isInProductView) {
                this._super(images, context, isInProductView);

                if (!isInProductView) {
                    const justAnImage = images[0];

                    if (justAnImage && justAnImage.img) {
                        justAnImage.img.indexOf('.webp') !== -1
                            ? context
                                .find('[type="image/webp"]')
                                .attr('srcset', justAnImage.img)
                            : context
                                .find('[type="image/jpg"], [type="image/png"]')
                                .attr('srcset', justAnImage.img);

                    }
                }
            }
        });

        return $.mage.SwatchRenderer;
    }
});
