=== SEOKEY - WordPress SEO audit and optimization plugin===
Author: SEOKEY
Contributors: seokey, seomix, confridin
Donate link: https://www.seo-key.com/
Tags: seo, search engine, google, content analysis, schema, référencement, breadcrumbs, référencement naturel, indexation, crawl, rich snippets, serp, search engine, search engine optimization, alternative text, redirection, seo audit
Requires at least:  5.5
Tested up to: 6.0.3
Requires PHP: 7.2
Stable tag: 1.4.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The Key to easily improve your SEO. SEOKEY the most simple and efficient Search Engine Audit and Optimization plugin for WordPress.

== Description ==

### SEOKEY: the simplest and most efficient SEO plugin

We give you all the keys to succeed:

- A unique SEO audit module
- A really easy-to-use interface
- Automatic technical optimizations without frustrating options
- A complete SEO toolbox (redirections, ALT editor, etc.)

#### SEO audit

There is no point in getting tips on a single content if you don't have the whole picture.

With SEOKEY, you can audit all your content in one go to improve your visibility in Google!

With the PRO version, we go even further with a more detailed analysis of 100% of your content, and especially by giving you a main directive for each one: optimize, change the keyword to target, do nothing, wait, etc.

#### Easy-to-use interface

Unlike other SEO plugins, SEOKEY's interface is by far the simplest one

We've removed all unnecessary options, and simplified the ones that will really improve your visibilty.

The result: you'll be able to configure SEOKEY much faster than with any other SEO plugin.

#### Automatic technical optimizations

In WordPress, many SEO optimizations should be active by default. This is our belief as SEO experts.

So why would we force you to check and fill in some options when they should already be active?

That's why SEOKEY automatically deploys SEO improvements without any clicks.

#### SEO toolbox (redirections, ALT editor, etc.)

With free version, SEOKEY provides you with all the basic features needed in SEO, without installing third-party plugins or the need to upgrade to the PRO version.

SEOKEY offers a complete SEO toolbox:

- the management of your redirects
- an alternative text editor to modify them all in one place
- 100% automatic generation of your schema.org markup (Local Business, Organization or Person, Breadcrumbs, Website)
- adding a management page for your Post Type archives if you have one

In PRO version, SEOKEY will warn you in case of 404 error detected by Google or when WordPress generates an automatic 301 redirect without warning you.

### Need more info?

More information is available on our website:
- in english <a href="https://www.seo-key.com">SEOKEY</a>
- in french <a href="https://www.seo-key.fr">SEOKEY en français</a>.

== Installation ==

This section describes how to install SEOKEY and get it working.

1. Install the plugin through the WordPress plugins screen directly ("Add New > Upload Plugin"), or upload the plugin files to the `/wp-content/plugins/plugin-name` directory.
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