var config = {
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'Yireo_Webp2/js/swatch-renderer-mixin': true
            },
            'mage/gallery/gallery': {
                'Yireo_Webp2/js/gallery-mixin': true
            }
        }
    },
    deps: [
        'Yireo_Webp2/js/add-webp-class-to-body'
    ]
};
