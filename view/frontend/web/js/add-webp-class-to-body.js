define([
    'jquery',
    './has-webp'
], function($, hasWebP) {
    if (hasWebP()) {
        $('body').addClass('webp');
    } else {
        $('body').addClass('no-webp');
    }
})