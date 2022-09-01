jQuery(document).ready(function ($) {
    // Disable some options
    var category_base_input = $("#category_base");
    category_base_input.attr("disabled", true);
    // Translation functions
    const { __, _x, _n, _nx } = wp.i18n;
    // Add text for each disable option
    var text = __( ' - Disabled by SEOKEY: you have chosen to remove category base from your categories permalinks.', 'seo-key' );
    category_base_input.after(text);
});