/**
 * Yireo Webp2 for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright (c) 2019 Yireo (https://www.yireo.com/)
 * @license     GNU General Public License
 */

define([
    'jquery'
], function ($) {

    return function(config) {
        if ($.cookie('webp')) {
            $('body').addClass('webp');
            return true;
        }

        var Tester = new Image();
        Tester.onload = function () {
            if (Tester.width > 0 && Tester.height > 0) {
                console.log('Set cookie');
                document.cookie = 'webp=1';
                $('body').addClass('webp');
            }
        };

        Tester.src = config.image;
    };
});
