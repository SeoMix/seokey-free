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

function seokey_helper_help_messages( $id = false, $right = false ) {
	// TODO later better factorisation (move help messages with their own files? Use an array?)
    if ( false === $id ) {
        return '';
    }
    switch ( $id ) {
		// Audit explanation
        case 'seo-score-explanation':
            $h2     = __( 'How does SEOKEY rate your site?', 'seo-key' );
            $text   = '<span>' . __( 'Our unique tool rates your site according to 3 criteria', 'seo-key' ) . '</span>';
	        $text   .= '<ul>';
	            $text   .= '<li>' . __( '1 - we rate contents that attracts traffic, regardless of whether they follow good SEO practices', 'seo-key' ) . '</li>';
	            $text   .= '<li>' . __( '2 - we rate different technical elements on the whole site (HTTPS, duplicate domains, etc.)', 'seo-key' ) . '</li>';
	            $text   .= '<li>' . __( '3 - we rate each content from an SEO point of view (content quality, meta tags, etc.).', 'seo-key' ) . '</li>';
	        $text   .= '</ul>';
            break;

		// Audit individual - suggestions
        case 'worktodo_nokeyword':
            $h2 = __( 'No targeted keyword','seo-key');
	        $text   = "<span>" . __( "You haven't targeted a keyword yet. SEOKEY needs this information to give you real advice.",'seo-key') . "</span>";
	        $text   .= "<span><strong>" . __( "Full advices are only available in PRO version",'seo-key')  . "</strong></span>";
            break;
	    case 'worktodo_wait_7':
		    $h2 = __( 'You should wait (content recently updated)','seo-key');
		    $text   = "<span>" . __( 'You content has been updated less than 7 days ago. Wait before doing any major optimization.','seo-key') . "</span>";
		    $text   .= "<span><strong>" . __( "Full advices are only available in PRO version",'seo-key')  . "</strong></span>";
			break;
	    case 'worktodo_wait_30':
		    $h2 = __( 'You should wait (recently published content)','seo-key');
		    $text   = "<span>" . __( 'This content was published less than 30 days ago. SEOKEY needs more information from Search Console to guide you.','seo-key') . "</span>" ;
		    $text   .= "<span><strong>" . __( "Full advices are only available in PRO version",'seo-key')  . "</strong></span>";
			break;
		case 'worktodo':
		    $h2 = __( 'Keep Working (you are still far from page 1)','seo-key' );
			$text   = "<span>" . __( 'Your content is not yet visible in search engines. Continue to improve your text, address user needs and link from other sites to this content.','seo-key') . "</span>";
			$text   .= "<span><strong>" . __( "Full advices are only available in PRO version",'seo-key')  . "</strong></span>";
			break;
			
		// Settings
	        // Schemaorg
            case 'settings-api-title-seokey-field-schemaorg-context':
			    $h2     = __( 'Why are you asking?', 'seo-key' );
			    $text   = '<span>' . __( 'Google needs to know who you are: a company, an individual, etc. This will enhance the credibility of your website (E.A.T criteria).', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'Nothing will be displayed to visitors, it is a schema.org markup only visible to search engines.', 'seo-key' ) . '</span>';
			    break;
		    case 'settings-api-title-seokey-field-cct-cpt':
			    $h2     = __( 'What is a post type and what should i do?', 'seo-key' );
			    $text   = '<span>' . __( 'By default, WordPress allows you to manage multiple content types (posts and pages are the default ones). Depending on your theme and plugins, you may have others such as products, sliders, clients, ...', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'But not all of these content types are always relevant. You can hide some of them from Google.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'To better see what they are, use each question mark to get more information about a specific content type.', 'seo-key' ) . '</span>';
			    break;
		    case 'settings-api-title-seokey-field-cct-taxo':
			    $h2     = __( 'What is a taxonomy and what should i do?', 'seo-key' );
			    $text   = '<span>' . __( 'By default, WordPress allows you to manage several types of classifications (categories and tags). These are called taxonomies. Depending on your theme and extensions, you may have more.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'But not all these taxonomies are always relevant. So you can hide some of them from Google.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'To better see what they are, hover over the question mark of each taxonomy to get more information.', 'seo-key' ) . '</span>';
			    break;
		    case 'settings-api-title-seokey-field-cct-taxonomy-choice-post':
			    $h2     = __( 'Why should i choose a taxonomy for each post type?', 'seo-key' );
			    $text   = '<span>' . __( 'In SEOKEY, we will tell Google the best way to rank your content (using schema.org)', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'For each of your content types, select the taxonomy that seems most relevant.', 'seo-key' ) . '</span>';
			    break;
		    case 'settings-api-title-seokey-field-cct-pages':
			    $h2     = __( 'Why are you asking?', 'seo-key' );
			    $text   = '<span>' . __( 'In WordPress, you have pages for each of your authors.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'But depending on your industry and strategy, you may not want to display them in Google.', 'seo-key' ) . '</span>';
			    break;
		    case 'settings-api-title-seokey-field-metas-metatitle':
			    $h2     = __( 'What is it?', 'seo-key' );
			    $text   = '<span>' . __( 'The Title tag is the main subject of your content. It has a crucial importance for the Internet user and the search engine.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'You must add the keyword you are targeting, while making the user want to click.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'Explain what your content will be about. This text must be appealing to users, because this is what they will read in the Google page results.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'We advise you to add the keyword you are targeting.', 'seo-key' ) . '</span>';
			    break;

		    case 'settings-title-search-console':
			    $h2     = __( 'What is the Search Console?', 'seo-key' );
			    $text   = '<span>' . __( 'Google Search Console is a free tool that lets you know the health and performance of your site in the search engine.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'This allows SEOKEY to get valuable information to give you advice.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'In no circumstances will we use your data elsewhere: it will only be displayed in the administration of your site.', 'seo-key' ) . '</span>';
			    break;
			case 'settings-title-search-console-step-1':
			    $h2     = __( 'What should i do?', 'seo-key' );
			    $text   = '<span>' . __( 'Go to your Search Console to give SEOKEY the right to access your data.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'To do this, please agree to the various requests after clicking on the button below.', 'seo-key' ) . '</span>';
				break;
			case 'settings-title-search-console-step-2':
				$h2     = __( 'What should i do?', 'seo-key' );
			    $text   = '<span>' . __( 'Then, copy/paste below the code you will have obtained to validate the access to your Search Console.', 'seo-key' ) . '</span>';
			    break;

		// Settings pages
		    case 'settings-api-title-seokey-field-tools-htpasslogin':
			    $h2     = __( 'What is a htaccess/htpasswd protection?', 'seo-key' );
			    $text   = '<span>' . __( 'When you are working on a development site, Google may sometimes discover it and index it.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'Ideally, you should use .htaccess/.htpasswd protection. But when activated, it can prevent some features of SEOKEY to work.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'You can use fields below to fill in your .htpasswd credentials.', 'seo-key' ) . '</span>';
			    break;
		    case 'sitemaps_data_explanation':
			    $h2     = __( 'What is a sitemap?', 'seo-key' );
			    $text   = '<span>' . __( 'A sitemap is a file that lists all the useful contents of your site.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'It helps search engines to discover all of your publications.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( "SEOKEY manages all this for you, you don't have to do anything.", 'seo-key' ) . '</span>';
			    break;
		    case 'breadcrumb-add':
			    $h2     = __( 'What is a breadcrumb?', 'seo-key' );
			    $text   = '<span>' . __( "It's is a line in which the user is told where he is.", 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'For example, it can look like this: "Home > Category > Post name"', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'SEOKEY adds the breadcrumbs in your pages (schema.org markup for Google). If you also want to display it to your visitors, use code below.', 'seo-key' ) . '</span>';
			    break;
			// No pagination on author pages
		    case 'seokey-field-seooptimizations-pagination-authors':
			    $h2     = __( 'What is it?', 'seo-key' );
			    $text   = '<span>' . __( "By default, when WordPress displays an author's page, it lists all their posts.", 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'When there are too many, your site will create additional pages, for example author-name/page/2', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( "These pages are usually of little interest to search engines. This wastes Google's time.", 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'By activating this option, SEOKEY automatically disables and redirects these pages.', 'seo-key' ) . '</span>';
			    break;
		    // No pagination with comments
			case 'seokey-field-seooptimizations-pagination-comments':
			    $h2     = __( 'What is it?', 'seo-key' );
			    $text   = '<span>' . __( "In WordPress, when you have too many comments, you can enable pagination of them. It will create additional pages, for example post-name/comment-page-2/", 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( "These pages are usually of little interest to search engines. This wastes Google's time.", 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( "Even worse, it reduces your actual content quality (some comments are no longer there).", 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'By activating this option, SEOKEY automatically disables and redirects these pages.', 'seo-key' ) . '</span>';
			    break;
		    // No "reply to" links for each comment
			case 'seokey-field-seooptimizations-replylinks':
			    $h2     = __( 'What is it?', 'seo-key' );
			    $text   = '<span>' . __( "In WordPress, you can enable individual responses for each comment.", 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'Unfortunately, this will create additional links in the page. Google will follow them and it will waste time. It will also give less importance to your real links.', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'By enabling this option, SEOKEY will hide these buttons.', 'seo-key' ) . '</span>';
			    break;
			// Hide noindexed content from main loops
		    case 'seokey-field-seooptimizations-hide-noindexed':
			    $h2     = __( 'What is it?', 'seo-key' );
			    $text   = '<span>' . __( "As in all SEO extensions, you can indicate whether a content should be hidden from Google.", 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'But they will still be displayed in listings, for example in your categories.', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'If you enable this option, "noindexed" content will no longer be displayed in your listings (in what is called The Loop in WordPress).', 'seo-key' ) . '</span>';
			    break;
		    // Deactivate and redirect secondary RSS feeds to the main feed
		    case 'seokey-field-seooptimizations-rss-secondary':
			    $h2     = __( 'What is it?', 'seo-key' );
			    $text   = '<span>' . __( "An RSS feed is a tool that allows different applications and sites to stay up to date with your new contents (for example with the excellent Feedly).", 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'But WordPress will create a lot of RSS feeds. They will make search engines waste time.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'SEOKEY will automatically disable some harmful RSS feeds, such as comments RSS. But to disable other secondary feeds like those of your categories, use this option', 'seo-key' ) . '</span>';
			    break;

		// metabox
		    // SEO Performance
			case 'metabox-data-source-sc':
				$h2     = __( 'Where does this data comes from?', 'seo-key' );
				$text   = '<span>' . __( 'The Google Search Console provides valuable information about your SEO.', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'The figures below will help you better understand how your content is currently performing on Google (figures based on the last 90 days).', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'By telling SEOKEY the keyword you want to be visible on, it will allow us to tell you what you should do with this content ("suggestion").', 'seo-key' ) . '</span>';
				break;
		    case 'metabox-data-metatitle':
			    $h2     = __( 'What is a title tag?', 'seo-key' );
			    $text   = '<span>' . __( 'The Title tag is the main subject of your content. It has a crucial importance for the Internet user and the search engine.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'You must add the keyword you are targeting, while making the user want to click.', 'seo-key' ) . '</span>';
			    break;
		    case 'metabox-data-metadesc':
			    $h2     = __( 'What is a meta description?', 'seo-key' );
			    $text   = '<span>' . __( 'Explain what your content will be about. This text must be appealing to users, because this is what they will read in the Google page results.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'We advise you to add the keyword you are targeting.', 'seo-key' ) . '</span>';
			    break;
		    case 'metabox-data-noindex':
			    $h2     = __( 'What is a private page (noindex directive)?', 'seo-key' );
			    $text   = '<span>' . __( 'Sometimes contents need to be private. For example, this is the case for a "thank you" page, a "my account" page or the shopping cart page on ecommerce websites.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'If this is the case, check the box. If Google discovers this content, SEOKEY will tell it to remove it from its index.', 'seo-key' ) . '</span>';
			    break;

	        // 301/404
		    case '301-404':
			    $h2     = __( 'Why is it important?', 'seo-key' );
			    $text   = '<span>' . __( 'When Google discovers an error page, it causes several problems: it reduces the credibility of the site and its popularity, while wasting its time.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'By redirecting these error pages, you will improve your SEO. Preferably, redirect these error pages to the most similar page.', 'seo-key' ) . '</span>';
			    break;
	        // Automatic redirections
            case '301-automatic':
	            $h2     = __( 'What is it?', 'seo-key' );
	            $text   = '<span>' . __( 'WordPress redirects automatically some urls when they have errors. But these redirects are slow and may not be relevant.', 'seo-key' ) . '</span>';
	            $text   .= '<span>' . __( 'You can therefore modify them (better relevance and loading time) or simply validate them (better loading time)', 'seo-key' ) . '</span>';
	            break;
		// ALT Editor
	        // Label above input
	        case 'alt-editor-input-label':
	            $h2     = __( 'How to fill in an alternative text?', 'seo-key' );
	            $text   = '<span>' . __( 'Describe what your image represents in a few words or a short sentence.', 'seo-key' ) . '</span>';
	            $text   .= '<span>' . __( 'If the image is purely decorative, you do not have to fill in the alternative text.', 'seo-key' ) . '</span>';
	            break;
	        // What is a good ALT ?
	        case 'alt-explanations':
	            $h2     = __( 'Why does Google need an alternative text?', 'seo-key' );
	            $text   = '<span>' . __( 'Google is unable to understand an image alone, so you must help it by associating an alternative text with it.', 'seo-key' ) . '</span>';
	            $text   .= '<span>' . __( 'You will have to describe your image in a few words or a short sentence.', 'seo-key' ) . '</span>';
	            $text   .= '<span>' . __( 'This will allow you to be visible in Google Images, and improve the understanding of your content.', 'seo-key' ) . '</span>';
	            break;
	        // Broken media library
	        case 'alt-editor-media-library':
	            $h2     = __( 'Why does WordPress Media library not update alternative texts within my content?', 'seo-key' );
	            $text   = '<span>' . __( 'The management of media by WordPress is not great. When you add an image to content, it is also added to the media library. Once it is done, the two are no longer linked to each other.', 'seo-key' ) . '</span>';
	            $text   .= '<span>' . __( 'If you change or add alternative text for one, it doesn\'t update the other.', 'seo-key' ) . '</span>';
	            $text   .= '<span>' . __( 'Why should you care if my ALT are not updated withing my contents? It will add the alternative text for your featured images and in every PHP function showing an image. It will also avoid future errors while adding already uploaded content.', 'seo-key' ) . '</span>';
	            break;

	    // Automatic optimizations
			case 'automaticseo-titles-and-meta-descriptions':
				$h2     = __( 'What is it?', 'seo-key' );
				$text   = '<span>' . __( 'Where other extensions offered a restrictive and not very explicit setting with %%title%% to insert, SEOKEY manages it for you.', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'You only have to focus on the essential: writing good content.', 'seo-key' ) . '</span>';
				break;
			case 'automaticseo-date-archive':
				$h2     = __( 'What is it?', 'seo-key' );
				$text   = '<span>' . __( 'WordPress natively creates URLs for each year, month and day you published content.', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( "These URLs are unfortunately harmful because they do not target any keyword, are duplicated and waste Google's time.", 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'SEOKEY takes care of getting rid of them.', 'seo-key' ) . '</span>';
				break;
			case 'automaticseo-header-cleaning':
				$h2     = __( 'What is it?', 'seo-key' );
				$text   = '<span>' . __( 'WordPress has the annoying tendency to add a lot of information to your HTML code, especially in what is called the HEAD.', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'SEOKEY removes this information to lighten the weight of the pages and prevent Google from following some useless links.', 'seo-key' ) . '</span>';
				break;
			case 'automaticseo-custom-post-types-archive-page':
				$h2     = __( 'What is it?', 'seo-key' );
				$text   = '<span>' . __( 'In WordPress, you can create new types of content (“products”, “customers”, ...). Some may have activated an archive page, (i.e. a page that lists all content of this type).', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'It is, for example, the case of a possible WooCommerce Shop page that lists all content of type “Product”', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'SEOKEY finally adds a menu that will allow you to see this page more easily, and optimize the meta tags.', 'seo-key' ) . '</span>';
				break;
			case 'automaticseo-login-page':
				$h2     = __( 'What is it?', 'seo-key' );
				$text   = '<span>' . __( 'By default, on your login page, the instructions given to Google (the meta robot tag) are not optimal, and this page is also linked to an external site (WordPress.org).', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'SEOKEY corrects these two defects.', 'seo-key' ) . '</span>';
				break;
			case 'automaticseo-medias':
				$h2     = __( 'What is it?', 'seo-key' );
				$text   = '<span>' . __( 'WordPress creates by default dedicated URLs for each image you upload.', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'But they are empty of text content and waste Google’s time.', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'SEOKEY removes them and redirects them to the image itself.', 'seo-key' ) . '</span>';
				break;
			case 'automaticseo-pings':
				$h2     = __( 'What is it?', 'seo-key' );
				$text   = '<span>' . __( 'You may have already experienced it, WordPress will sometimes create a comment on your content when it links to another one.', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'SEOKEY disables this feature.', 'seo-key' ) . '</span>';
				break;
			case 'automaticseo-writing':
				$h2     = __( 'What is it?', 'seo-key' );
				$text   = '<span>' . __( 'In Gutenberg, as in the old WordPress content editor (TinyMCE), some options can be harmful.', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'One of them is the possibility to add H1s or links to attachment pages.', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'SEOKEY cleans this up.', 'seo-key' ) . '</span>';
				break;
			case 'automaticseo-robots-txt-file':
				$h2     = __( 'What is it?', 'seo-key' );
				$text   = '<span>' . __( 'The robots.txt file allows you to tell Google which folders, files and URLs it should never visit again. But this does more harm than good because, on a truly optimized site, search engines should never find these links.', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'SEOKEY will create a simple and effective file.', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'In addition, we physically create it, making it much faster to load than other extensions.', 'seo-key' ) . '</span>';
				break;
			case 'automaticseo-improved-rss-feeds':
				$h2     = __( 'What is it?', 'seo-key' );
				$text   = '<span>' . __( 'RSS feeds can be useful for Internet users to subscribe to your content.', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'But for SEO, they often waste time for search engines or cause duplicate content.', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'SEOKEY optimizes them in-depth (no more duplicated content, adding a link to the original content, adding an image when possible, deactivating comment RSS feeds, ...).', 'seo-key' ) . '</span>';
				break;
			case 'automaticseo-schema-org':
				$h2     = __( 'What is it?', 'seo-key' );
				$text   = '<span>' . __( 'Google can sometimes have trouble understanding who you are and where they are on your website.', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'Depending on your settings, SEOKEY will automatically add several schema.org markup that will give it these valuable information (breadcrumbs, person, organization or local business markup).', 'seo-key' ) . '</span>';
				break;
			case 'automaticseo-wordpress-sitemaps':
				$h2     = __( 'What is it?', 'seo-key' );
				$text   = '<span>' . __( 'In order to know all the pages of your site, a search engine may need sitemaps.xml files that list all your content.', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'WordPress will create them natively, but they are slow and not very optimized.', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'SEOKEY disables them to create its own. As for the robots.txt file, we physically generate these files for a better loading time.', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'And finally, as soon as your Search Console is connected, we take care of submitting it to Google.', 'seo-key' ) . '</span>';
				break;
			case 'automaticseo-user-metas':
				$h2     = __( 'What is it?', 'seo-key' );
				$text   = '<span>' . __( "When you edit a user’s profile, SEOKEY adds several fields to tell Google who you are.", 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'The objective is simple: to make your site more credible (E.A.T. criteria).', 'seo-key' ) . '</span>';
				break;
			case 'automaticseo-performance':
				$h2     = __( 'What is it?', 'seo-key' );
				$text   = '<span>' . __( 'For a better loading time, we strongly advise you to install a cache extension (e.g. WP Rocket).', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'But these extensions will sometimes create temporary files that will eventually become error pages harmful to search engines.', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'SEOKEY corrects these by using a 410 code that will tell Google that it should not visit them anymore.', 'seo-key' ) . '</span>';
				break;

	    // Audit tasks
	        case 'audit-task-words_count':
		        $h2     = __( 'Why longer content are better?', 'seo-key' );
		        $text   = '<span>' . __( 'A long content is more likely to be visible in search engines.', 'seo-key' ) . '</span>';
		        $text   .= '<span>' . __( 'The reason for this is simple: the more text you have, the more likely you are to fully meet the needs of the user.', 'seo-key' ) . '</span>';
		        break;
		    case 'audit-task-HTTPS':
			    $h2     = __( 'What should i do?', 'seo-key' );
			    $text   = '<span>' . __( 'An SSL certificate is important because it improves the security of your site. For Google, it is also a ranking criterion (although weak).', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'But the SSL certificate depends primarily on your hosting, not your site.', 'seo-key' ) . '</span>';
			    break;
		    case 'audit-task-title_length':
			    $h2     = __( 'What should i do?', 'seo-key' );
			    $text   = '<span>' . __( 'The title tag is very important in SEO. ', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'When it is too short or too long, the risk is that it is not optimized enough.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'Write an optimized tag: neither too long nor too short.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'It must include the keyword you are targeting and describe your content.', 'seo-key' ) . '</span>';
			    break;
		    case 'audit-task-meta_desc_length':
			    $h2     = __( 'What should i do?', 'seo-key' );
			    $text   = '<span>' . __( 'The meta description is important in SEO. ', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'When it is too short or too long, the risk is that it is not optimized enough.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'Write an optimized tag: neither too long nor too short. It must include the keyword you are targeting and describe your content.', 'seo-key' ) . '</span>';
			    break;
		    case 'audit-task-posts_per_page':
			    $h2     = __( 'What should i do?', 'seo-key' );
			    $text   = '<span>' . __( 'By default, WordPress displays only 10 contents per page.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( "By increasing this number, it reduces the number of pagination pages (/page/2) so as not to waste Google's time.", 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'With more content displayed, it will also improve the relevance of your categories.', 'seo-key' ) . '</span>';
			    break;
		    case 'audit-task-image_alt_missing':
			    $h2     = __( 'What should i do?', 'seo-key' );
			    $text   = '<span>' . __( 'An alternative text allows to describe an image. This is useful for accessibility, but also for search engines that cannot understand your visuals.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'An alternative text must describe in a few words each image.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'You must therefore fill in the alternative texts of all the media you have inserted in your content.', 'seo-key' ) . '</span>';
			    break;
		    case 'audit-task-main_keyword_density':
			    $h2     = __( 'What should i do?', 'seo-key' );
			    $text   = '<span>' . __( 'When you want to be visible on Google with a specific expression, you must use it in your content.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'This audit task therefore tests the number of times you use your targeted keyword in your content.', 'seo-key' ) . '</span>';
			    break;
		    case 'audit-task-h2':
			    $h2     = __( 'What should i do?', 'seo-key' );
			    $text   = '<span>' . __( 'Good content is content that perfectly meets the needs of Internet users.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'When this is the case, your content is often relatively long, with several parts.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( "These are normally separated by level 2 titles (H2s). If you don't have enough of these, chances are your content is not rich enough.", 'seo-key' ) . '</span>';
			    break;
		    case 'audit-task-image_alt_media_library':
			    $h2     = __( 'What should i do?', 'seo-key' );
			    $text   = '<span>' . __( 'When you upload an image, it is added to the media library.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'An alternative text allows to describe an image. This is useful for accessibility, but also for search engines that cannot understand your visuals.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'An alternative text must describe in a few words each image.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'This audit task lists all the images in your media library that do not have alternative texts.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'This would fix two issues: you may insert them in your content without ALT, and your theme or extensions may do it too.', 'seo-key' ) . '</span>';
			    break;
		    case 'audit-task-traffic':
			    $h2     = __( 'What should i do?', 'seo-key' );
			    $text   = '<span>' . __( 'This audit task is not there to analyze your content.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'It allows you to know which of your contents are not attracting visitors, and which therefore theoretically need work: improving the content and creating internal and external links to them.', 'seo-key' ) . '</span>';
			    break;
		    case 'audit-task-h1_in_content':
			    $h2     = __( 'What should i do?', 'seo-key' );
			    $text   = '<span>' . __( 'In SEO, an H1 is the main title of your content. As for the title tag, it must describe your text and include the targeted keyword if possible.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'On classic WordPress themes, the H1 is automatically the name of your content.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'This audit task will show you all contents where you may have wrongly added an H1 inside your text.', 'seo-key' ) . '</span>';
			    break;
		    case 'audit-task-main_keyword_selection':
			    $h2     = __( 'What should i do?', 'seo-key' );
			    $text   = '<span>' . __( 'To work properly, SEOKEY needs to know which keywords you want to be visible on.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'This audit task lists all the content where you have not filled in this information.', 'seo-key' ) . '</span>';
			    break;
		    case 'audit-task-traffic_main_keyword':
			    $h2     = __( 'What should i do?', 'seo-key' );
			    $text   = '<span>' . __( 'By choosing a target keyword, SEOKEY can give you more precise advice.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'This audit task tells you whether you have reached your goals or not.', 'seo-key' ) . '</span>';
			    break;

			case 'audit-task-noindex_contents':
				$h2     = __( 'What should i do?', 'seo-key' );
				$text   = '<span>' . __( 'Sometimes we have to hide some content. It is the case for example of a "my account" or "thank you" page.', 'seo-key' ) . '</span>';
				$text   .= '<span>' . __( 'This audit task allows you to visualize all the contents that you would have hidden (box "noindex).', 'seo-key' ) . '</span>';
				break;
		    case 'audit-task-slug_conflict':
			    $h2     = __( 'What should i do?', 'seo-key' );
			    $text   = '<span>' . __( 'To have more optimized and readable URLs, you have disabled the category prefix (/category/). ', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'But in this case, there is a risk to have a content (post type) with the same URL as one of your category, preventing Google to access it.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'SEOKEY is the only SEO plugin to notify you with this audit task.', 'seo-key' ) . '</span>';
			    break;
		    case 'audit-task-domain_variations':
			    $h2     = __( 'What should i do?', 'seo-key' );
			    $text   = '<span>' . __( 'If your site is not optimized, it may be accessible via several different URLs: monsite.com and www.monsite.com.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'For Google, this duplicates your content: it is therefore very harmful to your natural referencing. ', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'Contact your host to redirect one of these versions to the other.', 'seo-key' ) . '</span>';
				break;
		    case 'audit-task-theme_support':
			    $h2     = __( 'What should i do?', 'seo-key' );
			    $text   = '<span>' . __( 'To work properly, your WordPress theme must have the title tag functionality enabled with the add_theme_support function.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'If not, it can cause bugs and bad display of your page titles for Google.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'This audit task verifies that your WordPress theme is properly set up.', 'seo-key' ) . '</span>';
			    break;
		    case 'audit-task-noindexed_website':
			    $h2     = __( 'What should i do?', 'seo-key' );
			    $text   = '<span>' . __( 'If you have mistakenly checked the box to hide your entire site from Google, this auditing task will notify you immediately.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'The "Discourage search engines from indexing this site" box can be found in "Settings > Reading"', 'seo-key' ) . '</span>';
			    break;
		    case 'audit-task-robotstxt':
			    $h2     = __( 'What should i do?', 'seo-key' );
			    $text   = '<span>' . __( 'For Google and other search engines, the robots.txt file allows to forbid access to certain files and folders.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'But the optimal solution in SEO is that they never find these links.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( "That's why this audit task checks that you have the simplest, most optimized robots.txt file possible.", 'seo-key' ) . '</span>';
			    break;
		    case 'audit-task-nosearchconsole':
			    $h2     = __( 'What should i do?', 'seo-key' );
			    $text   = '<span>' . __( 'To give you real advice, SEOKEY needs access to your Search Console data to use it in this audit.', 'seo-key' ) . '</span>';
			    $text   .= '<span>' . __( 'This audit task verifies that you have connected your Search Console.', 'seo-key' ) . '</span>';
			    break;
				
	    case 'audit-task-no_image':
		    $h2     = __( 'What should i do?', 'seo-key' );
		    $text   = '<span>' . __( 'The addition of images (with alternative texts) helps to optimize and make your content more relevant.', 'seo-key' ) . '</span>';
		    $text   .= '<span>' . __( 'Do not hesitate to add them.', 'seo-key' ) . '</span>';
		    break;
	    case 'audit-task-no_links':
		    $h2     = __( 'What should i do?', 'seo-key' );
		    $text   = '<span>' . __( 'To optimize your visibility, you must have internal links towards relevant pages.', 'seo-key' ) . '</span>';
		    $text   .= '<span>' . __( 'Therefore, you should add links to your most important pages.', 'seo-key' ) . '</span>';
		    break;
	    case 'settings-api-title-seokey-field-search-console-searchconsole-google-verification-code':
		    $h2     = __( 'What is this HTML verification code?', 'seo-key' );
		    $text   = '<span>' . __( 'To add a site in your Google Search Console account, the tool gives you several methods.', 'seo-key' ) . '</span>';
		    $text   .= '<span>' . __( 'One of them is to add a meta in the HTML code of your pages.', 'seo-key' ) . '</span>';
		    $text   .= '<span>' . __( 'If you want to use this method, you can copy below the code given by Google.', 'seo-key' ) . '</span>';
		    break;
	    case 'audit-task-details-https1':
		    $h2     = __( 'What should i do?', 'seo-key' );
		    $text   = '<span>' . __( 'Having an HTTPS website improves its security. For SEO, this is a requirement to implement.', 'seo-key' ) . '</span>';
		    $text   .= '<span>' . __( 'HTTPS does not seem to be supported on your site. For this, we invite you to contact your host to enable it at the hosting level.', 'seo-key' ) . '</span>';
		    break;
	    case 'audit-task-details-https2':
		    $h2     = __( 'What should i do?', 'seo-key' );
		    $text   = '<span>' . __( 'Having an HTTPS website improves its security. For SEO, this is a requirement to implement.', 'seo-key' ) . '</span>';
		    $text   .= '<span>' . __( 'The URLs of your site are not in HTTPS. Contact a professional to migrate your URLs or use an extension like Really Simple SSL.', 'seo-key' ) . '</span>';
		    break;
        default :
			$custom = apply_filters( 'seokey_filter_helper_help_messages', false, $id ) ;
			if ( false !== $custom ) {
				if ( isset( $custom['h2'] ) && isset( $custom['text'] ) ) {
					$h2   = $custom['h2'];
					$text = $custom['text'];
				} else {
					$custom = false;
				}
			} else {
				$h2   = __( 'No help message yet', 'seo-key' );
				$text = __( 'Help text will be added soon', 'seo-key' );
				// $text .= __( ' | ID is ', 'seo-key' ) . $id; // Only for debug
			}
    }
    $class = ( true === $right) ? ' right' : '';
    $html = '
    <span class="seokey-tooltip-icon">
        <span class="seokey-tooltip-text' . $class . '">
            <span class="seokey-help-title">' . $h2 . '</span>
            <span class="seokey-help-p">' . $text . '</span>
        </span>
    </span>';
    return $html;
}