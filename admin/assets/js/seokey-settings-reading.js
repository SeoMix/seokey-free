jQuery(document).ready(function ($) {
    // define each label involved (WordPress has no hooks here)
    var rss_use_excerpt1 = $(".form-table tr:nth-child(4) label");
    rss_use_excerpt1.css('text-decoration', 'line-through');
    var rss_use_excerpt2 = $(".form-table tr:nth-child(4) .description");
    rss_use_excerpt2.css('text-decoration', 'line-through');
    // Disable some options
    var rss_use_excerptinput = $(".form-table tr:nth-child(4) input");
    rss_use_excerptinput.attr("disabled", true);;
    // Translation functions
    const { __, _x, _n, _nx } = wp.i18n;
    // Add text for each disable option
    var text = __( ' - Disabled by SEOKEY: these options may harm your SEO', 'seo-key' );
    rss_use_excerpt2.after(text);
});