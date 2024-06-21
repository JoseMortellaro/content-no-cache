=== Content No Cache: prevent specific content from being cached ===
Contributors: giuse
Tags: dynamic content, cache, issues
Requires at least: 4.6
Tested up to: 6.5
Stable tag: 0.1.1
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Serve updated dynamic content even when you add it to a page that is cached.


== Description ==

Normally, if a web page is served by the <a href="https://wordpress.org/plugins/search/cache/">cache</a>, its content will be always the same until the cache is deleted.

By adding the content with the shortcode provided by Content No Cache, you will be able to show dynamic content even on pages served by cache.

You don’t need this plugin to exclude the entire page from the <a href="https://wordpress.org/plugins/search/cache/">cache</a>.
This plugin is to exclude a part of the page from the <a href="https://wordpress.org/plugins/search/cache/">cache</a>. It's useful if you need to cache a page, but part of that page should not be cached.


Imagine you have a page that has this content:

Hello this is some text.
Another line of text
Another line of text
Current day: Monday

if that page is served by cache, the user will always see:

Hello this is some text.
Another line of text
Another line of text
Current day: Monday

If you need that the last line of text is always updated, this plugin will help you.
The entire page will be served by the cache, but the plugin will get the updated content that you need.


== How to show dynamic content with full page cache ==
* Install Content No Cache
* Create a new element "Content No Cache"
* Add to that element all the content that you want to exclude from the cache
* Copy the shortcode that you will see in the section "Shortcode". It will look like [content_no_cache id="3328"]. The parameter "id" is the ID of the content element (in this example 3328).
* Add the shortcode to the page where you want to display that content.


== Compatible <a href="https://wordpress.org/plugins/search/cache/">caching plugins</a> tested with Content No Cache ==
* <a href="https://wordpress.org/plugins/w3-total-cache/">W3 Total Cache</a>
* <a href="https://wordpress.org/plugins/wp-fastest-cache/">WP Fastest Cache</a>
* <a href="https://wordpress.org/plugins/wp-optimize/">WP Optimize</a>
* <a href="https://wordpress.org/plugins/comet-cache/">Comet Cache</a>
* <a href="https://wordpress.org/plugins/cache-enabler/">Cache Enabler</a>
* <a href="https://wordpress.org/plugins/hyper-cache/">Hyper Cache</a>
* <a href="https://wordpress.org/plugins/wp-super-cache/">WP Super Cache</a>
* <a href="https://wordpress.org/plugins/litespeed-cache/">LiteSpeed Cache</a>
* <a href="https://wordpress.org/plugins/sg-cachepress/">SiteGround Optmizer</a>
* <a href="https://wp-rocket.me/">WPRocket</a>

All of those caching plugins are compatible with Content No Cache. If your favorite caching plugin is not compatible for any reason, let us know it


== Main features ==
* Ultralightweight plugin. The few lines of code will run only where you add the shortcode.
* No jQuery, no JS libraries, only a couple of lines of pure JavaScript
* No database queries, no extra HTTP requests for external assets, no bloat
* With a few line of ultralight code you can fully cache the page even if you need dynamic content on that page
* It provides a shortcode, so you can add it everywhere, no matter the builder


== Tips to speed up the process to get the content ==
* Install <a href="https://wordpress.org/plugins/freesoul-deactivate-plugins/">Freesoul Deactivate Plugins</a>
* Go to Freesoul Deactivate Plugins => Actions => Content No Cache
* Disable all the plugins that you don't need to output the content


== Example ==
You can see Content No Cache in action visiting the blog post <a href="https://josemortellaro.com/exclude-specific-content-from-being-cached/">Exclude specific content from being cached</a>.
You will see a number that is always different when you refresh the page. But the page is served by full page cache.
In the example it's just a number, but you can output whatever content you want.


== Possible conflicts ==
Some plugins don't load the shortcodes during ajax requests. Because Content No Cache retrieves the content through ajax, in those cases the content will not be displayed properly.
If you have this kind of issue set the parameter request="remote". In this case the shortcode will look like [content_no_cache id="3328" request="remote"]
The plugin will retrieve the content in a different way that will be a little slower, but this will solve this kind of conflict.


== Help ==
If something doesn't work for you, don't hesitate to open a thread on the <a href="https://wordpress.org/support/plugin/content-no-cache/">Support Forum</a>


== Changelog ==

= 0.1.1 =
* Fix: content not showing with request="remote"

= 0.1.0 =
* Added: integration with Elementor
* Added: spinner during the loading of the content

= 0.0.9 =
*Added: action hook 'content_no_cache_before_sending_content'

= 0.0.8 =
*Added: content_no_cache_added JavaScript event
*Added: hooks for future PRO version

= 0.0.7 =
*Added: integration with Divi Builder

= 0.0.6 =
*Fix: conflict with <a href="https://wordpress.org/plugins/all-in-one-seo-pack/">All In One SEO</a>
*Fix: conflict with plugins that don't load shortcodes during ajax requests. Need to set the parameter request="remote" if having issues with the shortcodes

= 0.0.5 =
*Fix: malfunction if two or more elements are added to the same page

= 0.0.4 =
*Fix: PHP warning

= 0.0.3 =
*Added: integration with <a href="https://wordpress.org/plugins/freesoul-deactivate-plugins/">Freesoul Deactivate Plugins</a>. It's possible now to disable specific plugins whilte getting the content.

= 0.0.2 =
*Fix: semicolon after content

= 0.0.1 =
*Initial release
