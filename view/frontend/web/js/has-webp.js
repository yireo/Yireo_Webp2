define([], function () {
    'use strict';

    const createWebPImage = function () {
        return new Promise((resolve, reject) => {
            var webP = new Image();
            webP.onload = () => {
                webP.height === 2 ? resolve() : reject()
            }
            webP.onerror = reject
            webP.src = 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
        });
    }

    return async function hasWebP() {
        return await createWebPImage();
    }
});
