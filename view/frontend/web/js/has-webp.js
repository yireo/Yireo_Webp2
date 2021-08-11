define([], function () {
    'use strict';

    return function hasWebP() {
        var elem = document.createElement('canvas');

        if (!!(elem.getContext && elem.getContext('2d'))) {
            return elem.toDataURL('image/webp').indexOf('data:image/webp') === 0;
        }
        return false;
    }
});
