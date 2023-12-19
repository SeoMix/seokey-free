<?php
/**
 * Load SEOKEY help messages content
 *
 * @Loaded  during plugin load
 * @see     seokey_load()
 *
 * @package SEOKEY
 */

/**
 * Security
 *
 * Prevent direct access to this file
 */
if ( ! defined( 'ABSPATH' ) ) {
    die( 'You lost the key...' );
}

// Todo comments
function seokey_helper_has_help_messages( $text ){
    $has_message = [
        'https1',
        'https2',
        'https3',
        'https4',
    ];
    if ( in_array( $text, $has_message ) ) {
        return true;
    }
    return false;
}

function seokey_helper_help_messages( $id = false, $right = false, $data = "" ) {
    // TODO later better factorisation (move help messages with their own files? Use an array?)
    // TODO Delete 2nd variable ( $right ) But see where it is used before !
    if ( false === $id ) {
        return '';
    }
    switch ( $id ) {
        // Keyword choice explanation
        case 'keyword-choice-explanation':
            $h2     = __( 'How do I choose a keyword?', 'seo-key' );
            $text   = __( 'A keyword or a phrase is what the Internet user will type in when searching in Google.', 'seo-key' ) . '<br><br>';
            $text   .= __( '<strong>Choose a keyword that makes sense</strong> (e.g. "Beginner cooking course" rather than "beginner" or just "course")', 'seo-key' ) . '<br><br>';
            $text   .= __( "<strong>Your content must meet the user's needs</strong>. Ask yourself the following question: what is the user looking for when he search this phrase into Google?", 'seo-key' );
            break;
        // Audit explanation
        case 'seo-score-explanation':
            $h2     = __( 'How does SEOKEY rate your site?', 'seo-key' );
            $text   = __( 'Our unique tool rates your site according to 3 criteria', 'seo-key' ) . '<br><br>';
            $text   .= '<ul>';
            $text   .= '<li>' . __( '1 - we rate contents that attracts traffic, regardless of whether they follow good SEO practices', 'seo-key' ) . '</li>';
            $text   .= '<li>' . __( '2 - we rate different technical elements on the whole site (HTTPS, duplicate domains, etc.)', 'seo-key' ) . '</li>';
            $text   .= '<li>' . __( '3 - we rate each content from an SEO point of view (content quality, meta tags, etc.).', 'seo-key' ) . '</li>';
            $text   .= '</ul>';
            break;
        // Audit individual - suggestions
        case 'worktodo_nokeyword':
            $h2 = __( 'No targeted keyword','seo-key');
	        $text   = __( "You haven't targeted a keyword yet. SEOKEY needs this information to give you real advice.",'seo-key');
	        $text   .= "<strong>" . __( "Full advices are only available in PRO version",'seo-key')  . "</strong>";
            break;
        case 'worktodo_wait_7':
            $h2 = __( 'You should wait','seo-key');
            $text = __('You content has been updated less than 7 days ago.','seo-key');
            $text .= ' ';
            $text .= __('Wait before doing any major optimization.','seo-key');
            $text .= "<strong>" . __( "Full advices are only available in PRO version",'seo-key')  . "</strong>";
			break;
	    case 'worktodo_wait_30':
            $h2 = __( 'You should wait','seo-key');
            $text = __('This content was published less than 30 days ago.','seo-key') . '<br><br>';
            $text .= __('SEOKEY needs more information from Search Console to guide you, and Google is still processing your content.','seo-key');
            $text   .= "<strong>" . __( "Full advices are only available in PRO version",'seo-key')  . "</strong>";
			break;
        case 'worktodo':
            $h2 = __( 'Keep Working','seo-key' );
            $text = __( 'Your content is not yet visible in search engines: you are still far from page 1.','seo-key') . '<br><br>';
            $text .= __('Continue to improve your text, respond to user needs and create links from other websites to this content.','seo-key');
            $text   .= "<strong>" . __( "Full advices are only available in PRO version",'seo-key')  . "</strong>";
            break;
        // Settings
        // Schemaorg
        case 'settings-api-title-seokey-field-schemaorg-context':
            $h2     = __( 'Why are you asking?', 'seo-key' );
            $text   = __( 'Google needs to know who you are: a company, an individual, etc. This will enhance the credibility of your website (E.A.T criteria).', 'seo-key' ) . '<br><br>';
            $text   .= __( 'Nothing will be displayed to visitors, it is a schema.org markup only visible to search engines.', 'seo-key' );
            break;
        case 'settings-api-title-seokey-field-cct-cpt':
            $h2     = __( 'What is a post type and what should i do?', 'seo-key' );
            $text   = __( 'By default, WordPress allows you to manage multiple content types (posts and pages are the default ones). Depending on your theme and plugins, you may have others such as products, sliders, clients, ...', 'seo-key' ) . '<br><br>';
            $text   .= __( 'But not all of these content types are always relevant. You can hide some of them from Google.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'To better see what they are, use each question mark to get more information about a specific content type.', 'seo-key' );
            break;
        case 'settings-api-title-seokey-field-cct-taxo':
            $h2     = __( 'What is a taxonomy and what should i do?', 'seo-key' );
            $text   = __( 'By default, WordPress allows you to manage several types of classifications (categories and tags). These are called taxonomies. Depending on your theme and extensions, you may have more.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'But not all these taxonomies are always relevant. So you can hide some of them from Google.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'To better see what they are, hover over the question mark of each taxonomy to get more information.', 'seo-key' );
            break;
        case 'settings-api-title-seokey-field-cct-taxonomy-choice-post':
            $h2     = __( 'Why should i choose a taxonomy for each post type?', 'seo-key' );
            $text   = __( 'In SEOKEY, we will tell Google the best way to rank your content (using schema.org)', 'seo-key' ) . '<br><br>';
            $text   .= __( 'For each of your content types, select the taxonomy that seems most relevant.', 'seo-key' );
            break;
        case 'settings-api-title-seokey-field-cct-pages':
            $h2     = __( 'Why are you asking?', 'seo-key' );
            $text   = __( 'In WordPress, you have pages for each of your authors.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'But depending on your industry and strategy, you may not want to display them in Google.', 'seo-key' );
            break;
        case 'settings-api-title-seokey-field-metas-metatitle':
            $h2     = __( 'What is it?', 'seo-key' );
            $text   = __( 'The Title tag is the main subject of your content. It has a crucial importance for the Internet user and the search engine.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'You must add the keyword you are targeting, while making the user want to click.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'Explain what your content will be about. This text must be appealing to users, because this is what they will read in the Google page results.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'We advise you to add the keyword you are targeting.', 'seo-key' );
            break;

        case 'settings-title-search-console':
            $h2     = __( 'What is the Search Console?', 'seo-key' );
            $text   = __( 'Google Search Console is a free tool that lets you know the health and performance of your site in the search engine.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'This allows SEOKEY to get valuable information to give you advice.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'In no circumstances will we use your data elsewhere: it will only be displayed in the administration of your site.', 'seo-key' );
            break;
        case 'settings-title-search-console-step-1':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'Go to your Search Console to give SEOKEY the right to access your data.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'To do this, please agree to the various requests after clicking on the button below.', 'seo-key' );
            break;
        case 'settings-title-search-console-step-2':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'Then, copy/paste below the code you will have obtained to validate the access to your Search Console.', 'seo-key' );
            break;
		// Settings pages
        case 'settings-api-title-seokey-field-tools-htpasslogin':
            $h2     = __( 'What is a htaccess/htpasswd protection?', 'seo-key' );
            $text   = __( 'When you are working on a development site, Google may sometimes discover it and index it.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'Ideally, you should use .htaccess/.htpasswd protection. But when activated, it can prevent some features of SEOKEY to work.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'You can use fields below to fill in your .htpasswd credentials.', 'seo-key' );
            break;
        case 'sitemaps_data_explanation':
            $h2     = __( 'What is a sitemap?', 'seo-key' );
            $text   = __( 'A sitemap is a file that lists all the useful contents of your site.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'It helps search engines to discover all of your publications.', 'seo-key' ) . '<br><br>';
            $text   .= __( "SEOKEY manages all this for you, you don't have to do anything.", 'seo-key' );
            break;
        case 'breadcrumb-add':
            $h2     = __( 'What is a breadcrumb?', 'seo-key' );
            $text   = __( "It's is a line in which the user is told where he is.", 'seo-key' ) . '<br><br>';
            $text   .= __( 'For example, it can look like this: "Home > Category > Post name"', 'seo-key' ) . '<br><br>';
            $text   .= __( 'SEOKEY adds the breadcrumbs in your pages (schema.org markup for Google). If you also want to display it to your visitors, use code below.', 'seo-key' );
            break;
        // No pagination on author pages
        case 'seokey-field-seooptimizations-pagination-authors':
            $h2     = __( 'What is it?', 'seo-key' );
            $text   = __( "By default, when WordPress displays an author's page, it lists all their posts.", 'seo-key' ) . '<br><br>';
            $text   .= __( 'When there are too many, your site will create additional pages, for example author-name/page/2', 'seo-key' ) . '<br><br>';
            $text   .= __( "These pages are usually of little interest to search engines. This wastes Google's time.", 'seo-key' ) . '<br><br>';
            $text   .= __( 'By activating this option, SEOKEY automatically disables and redirects these pages.', 'seo-key' );
            break;
        // No pagination with comments
        case 'seokey-field-seooptimizations-pagination-comments':
            $h2     = __( 'What is it?', 'seo-key' );
            $text   = __( "In WordPress, when you have too many comments, you can enable pagination of them. It will create additional pages, for example post-name/comment-page-2/", 'seo-key' ) . '<br><br>';
            $text   .= __( "These pages are usually of little interest to search engines. This wastes Google's time.", 'seo-key' ) . '<br><br>';
            $text   .= __( "Even worse, it reduces your actual content quality (some comments are no longer there).", 'seo-key' ) . '<br><br>';
            $text   .= __( 'By activating this option, SEOKEY automatically disables and redirects these pages.', 'seo-key' );
            break;
        // No "reply to" links for each comment
        case 'seokey-field-seooptimizations-replylinks':
            $h2     = __( 'What is it?', 'seo-key' );
            $text   = __( "In WordPress, you can enable individual responses for each comment.", 'seo-key' ) . '<br><br>';
            $text   .= __( 'Unfortunately, this will create additional links in the page. Google will follow them and it will waste time. It will also give less importance to your real links.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'By enabling this option, SEOKEY will hide these buttons.', 'seo-key' );
            break;
        // Hide noindexed content from main loops
        case 'seokey-field-seooptimizations-hide-noindexed':
            $h2     = __( 'What is it?', 'seo-key' );
            $text   = __( "As in all SEO extensions, you can indicate whether a content should be hidden from Google.", 'seo-key' ) . '<br><br>';
            $text   .= __( 'But they will still be displayed in listings, for example in your categories.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'If you enable this option, "noindexed" content will no longer be displayed in your listings (in what is called The Loop in WordPress).', 'seo-key' );
            break;
        // Deactivate and redirect secondary RSS feeds to the main feed
        case 'seokey-field-seooptimizations-rss-secondary':
            $h2     = __( 'What is it?', 'seo-key' );
            $text   = __( "An RSS feed is a tool that allows different applications and sites to stay up to date with your new contents (for example with the excellent Feedly).", 'seo-key' ) . '<br><br>';
            $text   .= __( 'But WordPress will create a lot of RSS feeds. They will make search engines waste time.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'SEOKEY will automatically disable some harmful RSS feeds, such as comments RSS. But to disable other secondary feeds like those of your categories, use this option', 'seo-key' );
            break;

        // metabox
        // SEO Performance
        case 'metabox-data-source-sc':
            $h2     = __( 'Where does this data comes from?', 'seo-key' );
            $text   = __( 'The Google Search Console provides valuable information about your SEO.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'The figures below will help you better understand how your content is currently performing on Google (figures based on the last 90 days).', 'seo-key' ) . '<br><br>';
            $text   .= __( 'By telling SEOKEY the keyword you want to be visible on, it will allow us to tell you what you should do with this content ("suggestion").', 'seo-key' );
            break;
        case 'metabox-data-metatitle':
            $h2     = __( 'What is a title tag?', 'seo-key' );
            $text   = __( 'The Title tag is the main subject of your content. It has a crucial importance for the Internet user and the search engine.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'You must add the keyword you are targeting, while making the user want to click.', 'seo-key' );
            break;
        case 'metabox-data-metadesc':
            $h2     = __( 'What is a meta description?', 'seo-key' );
            $text   = __( 'Explain what your content will be about. This text must be appealing to users, because this is what they will read in the Google page results.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'We advise you to add the keyword you are targeting.', 'seo-key' );
            break;
        case 'metabox-data-noindex':
            $h2     = __( 'What is a private page (noindex directive)?', 'seo-key' );
            $text   = __( 'Sometimes contents need to be private. For example, this is the case for a "thank you" page, a "my account" page or the shopping cart page on ecommerce websites.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'If this is the case, check the box. If Google discovers this content, SEOKEY will tell it to remove it from its index.', 'seo-key' );
            break;

        // 301/404
        case '301-404':
            $h2     = __( 'Why is it important?', 'seo-key' );
            $text   = __( 'When Google discovers an error page, it causes several problems: it reduces the credibility of the site and its popularity, while wasting its time.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'By redirecting these error pages, you will improve your SEO. Preferably, redirect these error pages to the most similar page.', 'seo-key' );
            break;
        // Automatic redirections
        case '301-automatic':
            $h2     = __( 'What is it?', 'seo-key' );
            $text   = __( 'WordPress redirects automatically some urls when they have errors. But these redirects are slow and may not be relevant.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'You can therefore modify them (better relevance and loading time) or simply validate them (better loading time)', 'seo-key' );
            break;
        // ALT Editor
        // Label above input
        case 'alt-editor-input-label':
            $h2     = __( 'How to fill in an alternative text?', 'seo-key' );
            $text   = __( 'Describe what your image represents in a few words or a short sentence. You should describe what the image is about.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'If the image is purely decorative, you do not have to fill in the alternative text (because it may harm accessibility).', 'seo-key' );
            break;
        // What is a good ALT ?
        case 'alt-explanations':
            $h2     = __( 'Why does Google need an alternative text?', 'seo-key' );
            $text   = __( 'Google is unable to understand an image alone, so you must help it by associating an alternative text with it.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'You will have to describe your image in a few words or a short sentence.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'This will allow you to be visible in Google Images, and improve the understanding of your content.', 'seo-key' );
            break;
        // Broken media library
        case 'alt-editor-media-library':
            $h2     = __( 'Why does WordPress Media library not update alternative texts within my content?', 'seo-key' );
            $text   = __( "Medias management in WordPress is not great. When you add an image into a content, it is also added to the media library. Once it is done, the two are no longer linked to each other.", 'seo-key' ) . '<br><br>';
            $text   .= __( "If you change or add alternative text for one, it doesn't update the other.", 'seo-key' ) . '<br><br>';
            $text   .= __( "Keep in mind that alternative texts within media library are still important. It will allow WordPress to add alternative texts for your featured images and in every PHP function showing a media. It will also avoid future errors if you want to add an already uploaded image.", 'seo-key' ) . '<br><br>';
            $text   .= __( "If you update to WordPress 6.0 or above, SEOKEY pro can fix this automatically.", 'seo-key' );
            break;
        // Broken media library fixed (WordPress 6.0+)
        case 'alt-editor-media-library-fixed':
            $h2     = __( 'Why does WordPress Media library not update alternative texts within my content?', 'seo-key' );
            $text   = __( 'Medias management in WordPress is not great. When you add an image into a content, it is also added to the media library. Once it is done, the two are no longer linked to each other.', 'seo-key' ) . '<br><br>';
            $text   .= __( "If you change or add alternative text for one, it doesn't update the other.", 'seo-key' ) . '<br><br>';
            $text   .= __( 'Keep in mind that alternative texts within media library are still important. It will allow WordPress to add alternative texts for your featured images and in every PHP function showing a media. It will also avoid future errors if you want to add an already uploaded image.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'With WordPress 6.0 or above, SEOKEY PRO will add missing ALT within contents using alternative texts found in your media library.', 'seo-key' );
            break;
        // Automatic optimizations
        case 'automaticseo-titles-and-meta-descriptions':
            $h2     = __( 'What is it?', 'seo-key' );
            $text   = __( 'Where other extensions offered a restrictive and not very explicit setting with %%title%% to insert, SEOKEY manages it for you.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'You only have to focus on the essential: writing good content.', 'seo-key' );
            break;
        case 'automaticseo-date-archive':
            $h2     = __( 'What is it?', 'seo-key' );
            $text   = __( 'WordPress natively creates URLs for each year, month and day you published content.', 'seo-key' ) . '<br><br>';
            $text   .= __( "These URLs are unfortunately harmful because they do not target any keyword, are duplicated and waste Google's time.", 'seo-key' ) . '<br><br>';
            $text   .= __( 'SEOKEY takes care of getting rid of them.', 'seo-key' );
            break;
        case 'automaticseo-header-cleaning':
            $h2     = __( 'What is it?', 'seo-key' );
            $text   = __( 'WordPress has the annoying tendency to add a lot of information to your HTML code, especially in what is called the HEAD.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'SEOKEY removes this information to lighten the weight of the pages and prevent Google from following some useless links.', 'seo-key' );
            break;
        case 'automaticseo-custom-post-types-archive-page':
            $h2     = __( 'What is it?', 'seo-key' );
            $text   = __( 'In WordPress, you can create new types of content (“products”, “customers”, ...). Some may have activated an archive page, (i.e. a page that lists all content of this type).', 'seo-key' ) . '<br><br>';
            $text   .= __( 'It is, for example, the case of a possible WooCommerce Shop page that lists all content of type “Product”', 'seo-key' ) . '<br><br>';
            $text   .= __( 'SEOKEY finally adds a menu that will allow you to see this page more easily, and optimize the meta tags.', 'seo-key' );
            break;
        case 'automaticseo-login-page':
            $h2     = __( 'What is it?', 'seo-key' );
            $text   = __( 'By default, on your login page, the instructions given to Google (the meta robot tag) are not optimal, and this page is also linked to an external site (WordPress.org).', 'seo-key' ) . '<br><br>';
            $text   .= __( 'SEOKEY corrects these two defects.', 'seo-key' );
            break;
        case 'automaticseo-medias':
            $h2     = __( 'What is it?', 'seo-key' );
            $text   = __( 'WordPress creates by default dedicated URLs for each image you upload.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'But they are empty of text content and waste Google’s time.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'SEOKEY removes them and redirects them to the image itself.', 'seo-key' );
            break;
        case 'automaticseo-pings':
            $h2     = __( 'What is it?', 'seo-key' );
            $text   = __( 'You may have already experienced it, WordPress will sometimes create a comment on your content when it links to another one.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'SEOKEY disables this feature.', 'seo-key' );
            break;
        case 'automaticseo-writing':
            $h2     = __( 'What is it?', 'seo-key' );
            $text   = __( 'In Gutenberg, as in the old WordPress content editor (TinyMCE), some options can be harmful.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'One of them is the possibility to add H1s or links to attachment pages.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'SEOKEY cleans this up.', 'seo-key' );
            break;
        case 'automaticseo-robots-txt-file':
            $h2     = __( 'What is it?', 'seo-key' );
            $text   = __( 'The robots.txt file allows you to tell Google which folders, files and URLs it should never visit again. But this does more harm than good because, on a truly optimized site, search engines should never find these links.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'SEOKEY will create a simple and effective file.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'In addition, we physically create it, making it much faster to load than other extensions.', 'seo-key' );
            break;
        case 'automaticseo-improved-rss-feeds':
            $h2     = __( 'What is it?', 'seo-key' );
            $text   = __( 'RSS feeds can be useful for Internet users to subscribe to your content.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'But for SEO, they often waste time for search engines or cause duplicate content.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'SEOKEY optimizes them in-depth (no more duplicated content, adding a link to the original content, adding an image when possible, deactivating comment RSS feeds, ...).', 'seo-key' );
            break;
        case 'automaticseo-schema-org':
            $h2     = __( 'What is it?', 'seo-key' );
            $text   = __( 'Google can sometimes have trouble understanding who you are and where they are on your website.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'Depending on your settings, SEOKEY will automatically add several schema.org markup that will give it these valuable information (breadcrumbs, person, organization or local business markup).', 'seo-key' );
            break;
        case 'automaticseo-wordpress-sitemaps':
            $h2     = __( 'What is it?', 'seo-key' );
            $text   = __( 'In order to know all the pages of your site, a search engine may need sitemaps.xml files that list all your content.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'WordPress will create them natively, but they are slow and not very optimized.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'SEOKEY disables them to create its own. As for the robots.txt file, we physically generate these files for a better loading time.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'And finally, as soon as your Search Console is connected, we take care of submitting it to Google.', 'seo-key' );
            break;
        case 'automaticseo-user-metas':
            $h2     = __( 'What is it?', 'seo-key' );
            $text   = __( "When you edit a user’s profile, SEOKEY adds several fields to tell Google who you are.", 'seo-key' ) . '<br><br>';
            $text   .= __( 'The objective is simple: to make your site more credible (E.A.T. criteria).', 'seo-key' );
            break;
        case 'automaticseo-performance':
            $h2     = __( 'What is it?', 'seo-key' );
            $text   = __( 'For a better loading time, we strongly advise you to install a cache extension (e.g. WP Rocket).', 'seo-key' ) . '<br><br>';
            $text   .= __( 'But these extensions will sometimes create temporary files that will eventually become error pages harmful to search engines.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'SEOKEY corrects these by using a 410 code that will tell Google that it should not visit them anymore.', 'seo-key' );
            break;

        // Audit tasks
        case 'audit-task-words_count':
            $h2     = __( 'Why longer content are better?', 'seo-key' );
            $text   = __( 'A long content is more likely to be visible in search engines.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'The reason for this is simple: the more text you have, the more likely you are to fully meet the needs of the user.', 'seo-key' );
            break;
        case 'audit-task-HTTPS':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'An SSL certificate is important because it improves the security of your site. For Google, it is also a ranking criterion (although weak).', 'seo-key' ) . '<br><br>';
            $text   .= __( 'But the SSL certificate depends primarily on your hosting, not your site.', 'seo-key' );
            break;
        case 'audit-task-title_length':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'The title tag is very important in SEO. ', 'seo-key' ) . '<br><br>';
            $text   .= __( 'When it is too short or too long, the risk is that it is not optimized enough.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'Write an optimized tag: neither too long nor too short.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'It must include the keyword you are targeting and describe your content.', 'seo-key' );
            break;
        case 'audit-task-meta_desc_length':
        case 'audit-task-meta_desc_length1':
        case 'audit-task-meta_desc_length2':
        case 'audit-task-meta_desc_length3':
        case 'audit-task-meta_desc_length4':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'The meta description is important in SEO. ', 'seo-key' ) . '<br><br>';
            $text   .= __( 'When it is too short or too long, the risk is that it is not optimized enough.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'Write an optimized tag: neither too long nor too short. It must include the keyword you are targeting and describe your content.', 'seo-key' );
            break;
        case 'audit-task-posts_per_page':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'By default, WordPress displays only 10 contents per page.', 'seo-key' ) . '<br><br>';
            $text   .= __( "By increasing this number, it reduces the number of pagination pages (/page/2) so as not to waste Google's time.", 'seo-key' ) . '<br><br>';
            $text   .= __( 'With more content displayed, it will also improve the relevance of your categories.', 'seo-key' );
            break;
        case 'audit-task-image_alt_missing':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'An alternative text allows to describe an image. This is useful for accessibility, but also for search engines that cannot understand your visuals.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'An alternative text must describe in a few words each image.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'You must therefore fill in the alternative texts of all the media you have inserted in your content.', 'seo-key' );
            break;
        case 'audit-task-main_keyword_density':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'When you want to be visible on Google with a specific expression, you must use it in your content.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'This audit task therefore tests the number of times you use your targeted keyword in your content.', 'seo-key' );
            break;
        case 'audit-task-h2':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'Good content is content that perfectly meets the needs of Internet users.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'When this is the case, your content is often relatively long, with several parts.', 'seo-key' ) . '<br><br>';
            $text   .= __( "These are normally separated by level 2 titles (H2s). If you don't have enough of these, chances are your content is not rich enough.", 'seo-key' );
            break;
        case 'audit-task-image_alt_media_library':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'When you upload an image, it is added to the media library.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'An alternative text allows to describe an image. This is useful for accessibility, but also for search engines that cannot understand your visuals.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'An alternative text must describe in a few words each image.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'This audit task lists all the images in your media library that do not have alternative texts.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'This would fix two issues: you may insert them in your content without ALT, and your theme or extensions may do it too.', 'seo-key' );
            break;
        case 'audit-task-traffic':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'This audit task is not there to analyze your content.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'It allows you to know which of your contents are not attracting visitors, and which therefore theoretically need work: improving the content and creating internal and external links to them.', 'seo-key' );
            break;
        case 'audit-task-h1_in_content':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'In SEO, an H1 is the main title of your content. As for the title tag, it must describe your text and include the targeted keyword if possible.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'On classic WordPress themes, the H1 is automatically the name of your content.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'This audit task will show you all contents where you may have wrongly added an H1 inside your text.', 'seo-key' );
            break;
        case 'audit-task-main_keyword_selection':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'To work properly, SEOKEY needs to know which keywords you want to be visible on.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'This audit task lists all the content where you have not filled in this information.', 'seo-key' );
            break;
        case 'audit-task-traffic_main_keyword':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'By choosing a target keyword, SEOKEY can give you more precise advice.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'This audit task tells you whether you have reached your goals or not.', 'seo-key' );
            break;

        case 'audit-task-noindex_contents':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'Sometimes we have to hide some content. It is the case for example of a "my account" or "thank you" page.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'This audit task allows you to visualize all the contents that you would have hidden (box "noindex).', 'seo-key' );
            break;
        case 'audit-task-slug_conflict':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'To have more optimized and readable URLs, you have disabled the category prefix (/category/). ', 'seo-key' ) . '<br><br>';
            $text   .= __( 'But in this case, there is a risk to have a content (post type) with the same URL as one of your category, preventing Google to access it.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'SEOKEY is the only SEO plugin to notify you with this audit task.', 'seo-key' );
            break;
        case 'audit-task-domain_variations':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'If your site is not optimized, it may be accessible via several different URLs: monsite.com and www.monsite.com.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'For Google, this duplicates your content: it is therefore very harmful to your natural referencing. ', 'seo-key' ) . '<br><br>';
            $text   .= __( 'Contact your host to redirect one of these versions to the other.', 'seo-key' );
            break;
        case 'audit-task-theme_support':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'To work properly, your WordPress theme must have the title tag functionality enabled with the add_theme_support function.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'If not, it can cause bugs and bad display of your page titles for Google.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'This audit task verifies that your WordPress theme is properly set up.', 'seo-key' );
            break;
        case 'audit-task-noindexed_website':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'If you have mistakenly checked the box to hide your entire site from Google, this auditing task will notify you immediately.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'The "Discourage search engines from indexing this site" box can be found in "Settings > Reading"', 'seo-key' );
            break;
        case 'audit-task-robotstxt':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'For Google and other search engines, the robots.txt file allows to forbid access to certain files and folders.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'But the optimal solution in SEO is that they never find these links.', 'seo-key' ) . '<br><br>';
            $text   .= __( "That's why this audit task checks that you have the simplest, most optimized robots.txt file possible.", 'seo-key' );
            break;
        case 'audit-task-nosearchconsole':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'To give you real advice, SEOKEY needs access to your Search Console data to use it in this audit.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'This audit task verifies that you have connected your Search Console.', 'seo-key' );
            break;

        case 'audit-task-no_image':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'The addition of images (with alternative texts) helps to optimize and make your content more relevant.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'Do not hesitate to add them.', 'seo-key' );
            break;
        case 'audit-task-no_links':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'To optimize your visibility, you must have internal links towards relevant pages.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'Therefore, you should add links to your most important pages.', 'seo-key' );
            break;
        case 'settings-api-title-seokey-field-search-console-searchconsole-google-verification-code':
            $h2     = __( 'What is this HTML verification code?', 'seo-key' );
            $text   = __( 'To add a site in your Google Search Console account, the tool gives you several methods.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'One of them is to add a meta in the HTML code of your pages.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'If you want to use this method, you can copy below the code given by Google.', 'seo-key' );
            break;
        case 'audit-task-details-https1':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'Having an HTTPS website improves its security. For SEO, this is a requirement to implement.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'HTTPS does not seem to be supported on your site. For this, we invite you to contact your host to enable it at the hosting level.', 'seo-key' );
            break;
        case 'audit-task-details-https2':
            $h2     = __( 'What should i do?', 'seo-key' );
            $text   = __( 'Having an HTTPS website improves its security. For SEO, this is a requirement to implement.', 'seo-key' ) . '<br><br>';
            $text   .= __( 'The URLs of your site are not in HTTPS. Contact a professional to migrate your URLs or use an extension like Really Simple SSL.', 'seo-key' );
            break;
        default :
            $custom = apply_filters( 'seokey_filter_helper_help_messages', false, $id ) ;
            if ( false !== $custom ) {
                if ( isset( $custom['h2'] ) && isset( $custom['text'] ) ) {
                    $h2   = $custom['h2'];
                    $text = $custom['text'];
                }
            } else {
                $h2   = __( 'No help message yet', 'seo-key' );
                $text = __( 'Help text will be added soon', 'seo-key' );
                //$text .= __( ' | ID is ', 'seo-key' ) . $id; // Only for debug
                //seokey_dev_write_log($id); // Only for debug
            }
            break;
    }
    return seokey_helper_create_tooltip( $h2, $text );
}

/**
 * Create a tooltip ( Need to call css + js )
 *
 * @param null $params
 * You can add parameters, see here : https://www.opentip.org/documentation.html
 * example : " data-ot-stem='false'" will remove the pointer
 *
 * @author  Gauvain Van Ghele
 * @since   1.5.2
 */
function seokey_helper_create_tooltip( $title, $content ){
    $tooltip_title      = htmlspecialchars( $title,ENT_QUOTES );
    $tooltip_content    = htmlspecialchars( $content,ENT_QUOTES );
    $html = "<span class='seokey-tooltip-icon' data-ot-fixed='true' data-ot-remove-elements-on-hide='true' data-ot-stem-base='10' data-ot-stem-length='10' ";
	    $html .= 'data-ot-title="' . $tooltip_title . '" data-ot="' . $tooltip_content . '">';
	$html .="</span>";
    return $html;
}