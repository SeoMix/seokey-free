=== SEOKEY - SEO audit and optimization ===
Author: SEOKEY
Contributors: seokey, seomix, confridin
Donate link: https://www.seo-key.com/
Tags: seo audit, seo, search engine, google, content analysis, schema, référencement, breadcrumbs, référencement naturel, indexation, crawl, rich snippets, serp, search engine, search engine optimization, alternative text, redirection
Requires at least: 5.5
Tested up to: 6.2
Requires PHP: 7.2
Stable tag: 1.6.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

SEOKEY the simplest and most effective Search Engine Audit and Optimization plugin for WordPress. SEO has never been so easy with SEOKEY !

== Description ==

New in 1.6: multilingual compatibility with WPML and Polylang + compatibility with DIVI, Elementor and WooCommerce

## SEOKEY: the simplest and most effective SEO plugin

We give you all the keys to succeed:

- A unique **global SEO audit tool**
- **Automatic technical optimizations** without frustrating options
- A really **easy-to-use interface**
- A complete **SEO toolbox** (redirections, ALT editor, etc.)

### SEO audit

There is no point in getting tips on a single content if you don't have the whole picture. With SEOKEY, you can **audit all your content in one go** to improve your visibility in Google and other Search Engines!

And when you edit a specific content, we also provide you with a full SEO content analysis.

With PRO version, we go even further with a more detailed analysis of 100% of your content : we give you a **main directive based on real traffic data**: optimize, change the keyword to target, do nothing, wait, etc.


### Automatic SEO technical optimizations

In WordPress, many SEO optimizations should be active by default. This is our belief as SEO experts since 2008. So why would we force you to check and fill in some options when they should already be active?

That's why **SEOKEY automatically deploys SEO improvements without any clicks**: we clean your <head>, we disable date archives, we redirect attachment pages and so on.

Need more information about it? Check here all our <a href="https://www.seo-key.com/features/optimizations/">SEO optimizations</a>


### Easy-to-use interface

Unlike other SEO plugins, SEOKEY's interface is by far the simplest one.

We've removed all unnecessary options, and simplified the ones that will really improve your visibility.

The result: you'll be able to configure and use SEOKEY much faster than with any other SEO plugin.


### SEO toolbox (redirections, ALT editor, etc.)

With free version, SEOKEY provides you with all features needed in SEO, without installing third-party plugins or the need to upgrade to the PRO version.

SEOKEY offers a complete WordPress SEO toolbox:

- easily manage your **301 redirects**
- an **alternative text editor** to modify them all in one place (and we use these new ALT texts to fix images without one within your contents)
- 100% automatic generation of your **schema.org markup** (Local Business, Organization or Person, Breadcrumbs, Website)
- we create **new management pages for your Post Type archives** if you have one (for example, your shop product page with Woocommerce)


### PRO VERSION

With SEOKEY PRO, our audit is more in-depth with a much more thorough analysis of your content, and better advice with data from your Search Console telling you exactly what you need to do !

SEOKEY PRO will also warn you in case of **404 error** detected by Google or **when WordPress generates an automatic 301 redirect** behind your back.


### Our SEO plugin summarized ?

- **SEOKEY guides you in your SEO actions**
- **SEOKEY automatically corrects many SEO defects**
- **SEOKEY is the easiest SEO plugin for WordPress**
- **SEOKEY has practical tools for everyday use**

## Need more info about our WordPress SEO plugin?

Check our websites: 
- in english <a href="https://www.seo-key.com">SEOKEY</a>
- in french <a href="https://www.seo-key.fr">SEOKEY en français</a>.

== Installation ==

This section describes how to install SEOKEY and get it working.

1. Install the plugin through the WordPress plugins screen directly with the plugin Search Engine, with the SEOKEY zip file ("Add New > Upload Plugin") or upload the plugin files to the `/wp-content/plugins/plugin-name` directory.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Wizard to configure it

== Screenshots ==

1. Audit Module
2. Automatic optimizations
3. Redirections
4. ALT editor
5. Settings

== Changelog ==
Public roadmap is here: https://trello.com/b/jauwlc3J/seokey-pro-public-roadmap

= 1.6.2
* Improvement: Adding missing translations
* Improvement: Improved metabox display for terms (when adding a new term)
* Fixed: SEOKEY now correctly send 410 headers for old cache files with URl parameters
* Fixed: SEOKEY option disabling author pagination was not working properly
* Fixed: Incorrect XSL path for sitemap files when WordPress is installed in a folder

= 1.6.1
* Fixed: avoid in some cases PHP errors when adding a new post (for authors and editors)

= 1.6.0
* New: multilingual compatibility with WPML and Polylang (WeGlot is coming soon).
* New: full compatibility with DIVI builder.
* New: improved WooCommerce compatibility (audit detection & admin menu for shop page).
* New: (PRO) New audit task for images without alternatives texts in your content but which have one in the media library (this audit task focuses on accessibility issues potentially caused by the automatic addition of this ALT by SEOKEY).
* Improvement: (PRO) keyword density audit task is now much better and handles accents or uppercase words.
* Improvement: (PRO) automatic redirections are now only registered when Google trigger them (it avoids false positives and useless work).
* Improvement: (PRO) traffic audit task is now an information task, not a warning task (it only tells you to keep working, it was not pointing a precise issue to solve).
* Improvement: (PRO) disconnected Search Console notice is now dismissible.
* Improvement: (PRO) improve redirection speed (we now prevent WordPress from guessing URl if current URl is going to be redirected).
* Improvement: change sitemap location to upload folder (better performances).
* Improvement: better help messages for redirection menu.
* Improvement: remove useless checkbox within the image ALT editor.
* Improvement: many French and English typo errors have been fixed.
* Fixed: (PRO) fix Search Console ping message (sometimes, Search Console used to show an error message about unreachable sitemaps)
* Fixed: avoid errors with third party plugins using Bulk option edits within default media library (for example, it removes a fatal error with the Download Monitor plugin).
* Fixed: avoid PHP error when users with author role were trying to access SEOKEY menus.
* Fixed: fix redirections not working when URL had special HTML characters.
* Fixed: fix automatic redirection being registered (but it was already redirected)
* Fixed: fix redirections not working when using specific domain port, for example when using a local WordPress installation with LocalWP.
* Fixed: fix error on Breadcrumbs Schema markup when no "Name" was given to the website.
* Fixed: fix canonical tags when using unusual permalink structure.
* Fixed: prevent wizard from skipping first step after importing SEO data from another plugin.
* Fixed: delete taxonomy sitemap file if user has deleted all associated terms.
* Fixed: avoid fatal error while generating sitemaps if user had not defined any allowed content.
* Dev note: new filter 'seokey_filter_schema_org_actions_name' to change website name value in Website Schema Markup.
* Dev note: sitemaps files will now be stored in a specific folder in uploads/seokey/sitemaps. They will use by default the website language (which will make it easier to switch to multilingual if you want to) : for example "sitemap-index-FRA.xml". We automatically manage the redirection from old URL to the new one.

= 1.5.2
* Improvement: increased post count used in our global audit. This will decrease global audit limitation.
* Improvement: improved redirection form when user is trying to redirect an already known URL
* Improvement: improved tooltips handling (avoid unreadable tooltips)
* Improvement: various french translation fixes
* Bugfix: when creating a new term, SEO data was not added (meta title, noindex, ...)
* Bugfix: fix "Redirection already here" error when adding a redirection with uppercase characters
* Bugfix: classic media library notice is now dismissible
* Bugfix: TinyMCE editor is now available for all taxonomy terms descriptions (for example with WooCommerce)
* Bugfix: various UX and notices fixes
* Third-party: send 410 code for old DIVI Cache files (improves crawl)
* Third-party: add misssing ID in our the_title filter while performing an SEO audit (avoid PHP errors with themes or plugins filtering the_title)
* Third-party: Awin plugins compatibility improved (their CSS were messing up our menus)

= 1.5.1
* Bugfix: fix main keyword input (users could not add a main keyword since 1.5.0)

= 1.5.0
* New: automatically add missing alternative texts to images (when user added an ALT to this image with the ALT editor or with the media library)
* New: new keyword menu to help you achieve your SEO goals. You can now view all your targeted keywords with a "next action" to do
* New: new notification after data import. If you import data from another SEO plugin, we warn you about missing OpenGraph and Twitter Card data, and we give you a simple fix to add it back again
* New: new free audit task => no image found in content
* Improvement: increase the number of audited contents in global audit (PRO version is needed to audit all of your contents)
* Improvement: small improvement for our suggestion module (next action to do). Many other improvements have been added to the PRO version
* Improvement: change global score. Scale shown is now 50 and not 100 anymore (full score has always been available only with PRO version)
* Improvement: better menu handling (less code, better performances)
* Improvement: added a link to FAQ in our settings to explain how to install PRO version
* Improvement: new and improved translations for french users
* Bugfix: improved interface (some icons were not correctly showing up on some menus)
* Bugfix: fixing some misplaced tooltips (there are still a few ones we need to clean)
* Bugfix: on some cases, strip whitespace (or other characters) from the beginning and end of <title> tags
* Bugfix: fix missing function to properly delete all data on uninstallation
* Bugfix: fix bad robots.txt encoding on some hosts
* Bugfix: various CSS fixes throughout our plugin

= 1.4.1
* Bugfix: fix Yoast global post type indexation value on data import
* Bugfix: remove some PHP warning while importing data from YOAST and SEOPRESS

= 1.4.0
* New: import SEO data from RankMath
* New: import SEO data from SEOPress
* New: automatic schema.org name attribute for mobile results on Google
* Improvement: improved RangePrice and SameAs automatic schema.org markup
* Improvement: wording and translations improvements (some of them were missing or fuzzy)
* Improvement: fix and improved performances for our "known content" function (SEOKEY will tell you if need to check your settings when new content types are found)
* Improvement: speed improvements (we removed useless code)
* Third-party: Genesis framework compatibility (their SEO functions are now disabled)
* Third-party: remove automatic breadcrumbs switch from Yoast to SEOKEY (too many bugs from many themes)
* Third-party: Elementor compatibility improved (avoid rare fatal errors with some themes)
* Third-party: better menu notification function (avoid bugs when another function is filtering $menu global)
* Bugfix: Yoast import function improved (missing data for schema.org markup)
* Bugfix: fix sitemap encoding

= 1.3.2
* Improvement: improved performance for several functions
* Improvement: better automatic optimization display while using our Wizard
* Improvement: better help message for some audit tasks
* Improvement: adding "contributing FILE" to project
* I18N: adding missing French translations (we still have a few ones to add in the next few weeks)
* I18N: fix bad text for max/min text counters
* Bugfix: improved Yoast data import
* Bugfix: avoid errors with uninstall functions
* Bugfix: avoid another PHP error with PRO version when Free version is still active
* Bugfix: removed some PHP warnings
* Third-party: OceanWP support enhanced (fixes an OceanWP bug with Yoast breadcrumbs)

= 1.3.1
* Improvement: better performance when importing data from Yoast
* Improvement: better performance when performing an audit
* Bugfix: avoid PHP error with PRO version when Free version is still active

= 1.3.0
* Major Free update: automatic optimizations, redirections, audit and much more
(version number has been updated to match our PRO version)

= 0.0.3
* Bugfix: php compatibility for _wakeup method on some hosts

= 0.0.2
* Bugfix: Excerpt more was displayed on other pages (it should only be present on Feed pages)

= 0.0.1
* First commit
* Automatically improve RSS feeds.