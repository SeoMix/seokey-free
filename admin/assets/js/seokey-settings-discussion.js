jQuery(document).ready(function ($) {
    // define each label involved (WordPress has no hooks here)
    var page_comments1 = $("label[for='page_comments']");
    var page_comments2 = $("label[for='comments_per_page']");
    var page_comments3 = $("label[for='default_comments_page']");
    var page_comments4 = $("label[for='comment_order']");
    // Add strike effect to useless options
    page_comments1.css('text-decoration', 'line-through')
    page_comments2.css('text-decoration', 'line-through')
    page_comments3.css('text-decoration', 'line-through')
    page_comments4.css('text-decoration', 'line-through')
    // Disable some options
    $("input#page_comments").attr("disabled", true);;
    // Translation functions
    const { __, _x, _n, _nx } = wp.i18n;
    // Add text for each disable option
    var text = __( ' - Disabled by SEOKEY: these options may harm your SEO', 'seo-key' );
    page_comments4.after(text);
});