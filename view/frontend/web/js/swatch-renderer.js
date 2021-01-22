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
             * When no .webp file is available the source tag will be removed
             * to prevent old image being shown on the frontend.
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
                        const webpSourceTag = context.find('[type="image/webp"]');

                        if (justAnImage.img.indexOf('.webp') !== -1) {
                            (webpSourceTag !== undefined)
                                ? webpSourceTag.attr('srcset', justAnImage.img)
                                : context
                                    .find('.product-image-photo')
                                    .prepend('<source type="image/webp" srcset="' + justAnImage.img + '">');
                        } else {
                            if (webpSourceTag !== undefined) {
                                webpSourceTag.remove();
                            }

                            context
                                .find('[type="image/jpg"], [type="image/png"]')
                                .attr('srcset', justAnImage.img);
                        }

                    }
                }
            }
        });

        return $.mage.SwatchRenderer;
    }
});
