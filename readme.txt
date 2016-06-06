=== Sailenseo by Shiva Acharjee ===
Contributors: prasantrai
Tags: schema, schema.org, microdata, structured data, seo, html5
Tested up to: 1.0.0 
Stable tag: 1.0.0
Requires at least: 3.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Insert schema.org microdata into WordPress pages and posts.

== Description ==

Provides an easy to use form to embed properly constructed schema.org microdata into a WordPress post or page.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `sailenseo` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= How does this all work? =

Will update soon..

= Can I test the output to see how the search engine will view it? =

Yes, although there is no guarantee that it will indeed show up that way. Google offers a [Rich Snippet Testing tool](http://www.google.com/webmasters/tools/richsnippets "Google Rich Snippet Test") to review. 

= I have a problem. Where do I go? =

This plugin is also maintained on [GitHub](https://github.com/shivaacharjee/sailenseo/ "Sailenseo on Github"). The best place to post questions / issues / bugs / enhancement requests is on the [issues page](https://github.com/raventools/sailenseo/issues "Issues page for Schema Creator on GitHub") there.

= How can I use this in my own language ? =
If your `locale` is not provided in the `/languages/` subfolder, you have to either wait or create your own language file. For example, you could use POEdit to open default.po and save it as your own locale and translate it bby yourself.


== Screenshots ==

1. The plugin creates a Schema Creator icon above the rich text editor. Click the icon to create a new schema.
2. Choose the schema you want to create from the select menu and then enter the data. Once you're finished, insert it into your post or page.
3. Schema Creator creates shortcode, which enables you to edit the schema after it's created.
4. This is an example of schema being rendered on a post.
5. Schema Creator also has a Settings page.
6. The Settings page allows you to turn on and off CSS, and to also include or exclude certain microdata attributes.

= Action Hooks =
- `raven_sc_register_settings` runs when the settings are registered. Use this hook to register more settings in the same options namespace.
- `raven_sc_options_validate` runs when the settings are saved ( &array ). Use this hook to save the settings. Accepts one referenced array as parameter.
- `raven_sc_metabox` runs when the metabox is outputted. Use this hook to add to the metabox.
- `raven_sc_save_metabox` runs when the metabox is saved. Use this hook to save added options from the metabox.

= Filters =
- `raven_sc_default_settings` gets default settings values. Add filters to add more default settings. In conjunction with `raven_sc_register_settings`.
- `raven_sc_admin_tooltip` gets the tooltips for admin pages. Add filters to add more tooltips. In conjunction with `raven_sc_register_settings`.




= 1.0.0 =
* Initial development based  on "Schema Creator"


