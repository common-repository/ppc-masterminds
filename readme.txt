=== PPC Masterminds ===
Contributors: zaidovski, thecrackerjack
Plugin link: https://ppcmasterminds.com/
Tags: ppc, pay per click, landing page optimization, cro, geoip, plugin extensions
Requires at least: 5.2
Tested up to: 5.6
Requires PHP: 7.2
Stable tag: 1.1.1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

The PPC Masterminds plugin is a utility plugin developed by PPC Masterminds to assist with dynamic content insertion into landing pages.

== Description ==
PPC Masterminds plugin helps you increase ppc quality scores, relevancy and conversion rates on landing pages by dynamically appending meta titles, descriptions and on page text (via shortcode) based on actual keyword search queries. It also has the ability to display a users approximate location by city or state.

To summarize, this plugin does the following:

1. Appends meta titles and descriptions with paid search queries to increase quality scores. This feature currently works only in combination with either the All In One SEO or Yoast SEO plugins. No other SEO plugin is supported at this time.
2. Dynamically changes text on any page to show paid search queries, using shortcode.
3. Dynamically inserts closest city/state based on the user's public IP address. This requires the use of Maxmind’s GeoIP extension.

== Frequently Asked Questions ==
= How to append keywords in meta title and description? =

Once the plugin is installed and activated, it will add a new section within your page editor, usually at the bottom, called “PPC Masterminds Meta Settings”. That section is responsible for appending the meta title/description based on your settings. Within that section, you will need to enter the URL parameter you are using to capture paid search queries. By default, that parameter is usually “keyword”, but that can be set to whatever parameter you like.

The plugin then checks for the URL parameter you indicated. When a URL parameter matches the parameter in your settings, the plugin will swap any {param} text in the title or meta description fields with that url parameter. (screenshot below).

If there is no match, or if the page title or page meta description fields are empty, the page will use the All In One SEO Pack title or description instead.
For example, for https://mysite.com/?my_param=Foo, if the parameter was my_param, then "{param}" would be replaced with "Foo" wherever it exists in the title and meta description.

= How to show paid search queries within page text? =
Once the plugin is installed and activated. You can place the shortcode below anywhere within your page you want the paid search query to appear.

**[url_params_to_text text="This is the {param} text you want displayed" params="word" default="This text will appear instead if no params match"] **

Please make sure you indicate what URL parameter the plugin should look for by adding it within the params field above. In the above shortcode, the URL parameter the plugin will look for is “word”. This means your URL should look something like this: https://yourwebsite.com?word=[paid-search-query]

If no parameter is found, the plugin will then use the default text you indicated within the shortcode. Example in action below:

Actual Ad URL: https://yourwebsite.com?word=ppc-agency

Shortcode On Your Webpage: Looking for a [url_params_to_text text="This is the {param} text you want displayed" params="word" default="marketing company"]? You are here!

Displayed (when parameter is found): Looking for a **ppc agency**? You are here!
Displayed (when NO parameter is found): Looking for a **marketing company**? You are here!

= How to show users locations (city or state) on pages? =
In order to use the GeoIP feature of this plugin:

1. You will need to ensure that the Maxmind GeoLite2 City database is installed in the plugin directory. You can download this database by signing up for a free account here: https://www.maxmind.com/en/geolite2/signup. The file should come named as “GeoLite2-City.mmdb”. It will need to be placed in the **“[PLUGIN_DIRECTORY]/ppcmasterminds/includes/geoip/”** folder.

2. Once the above steps are complete, you can place the shortcode below anywhere within your page to display the approximate location of the user based on their public IP address.

Use this shortcode to display city name:
**[geoip_location state="no" not_found_text=""]**

Use this shortcode to display state name:
**[geoip_location city="no" not_found_text=""]**

Use this shortcode to display both city & state:
**[geoip_location not_found_text="Oops, couldn't find your location!"]**

Note: Add your own not_found_text to the shortcode in order to customize what it says when the IP could not be matched to the database.

== Changelog ==
= 1.0.0 - 2020-07-31 =
**Initial Release**
* Plugin released to the public.

= 1.1.0 - 2020-08-31 =
**Yoast SEO Support**
* The swapping of meta titles and descriptions with URL parameters is now compatible with both Yoast SEO & All-In-One SEO Pack

= 1.1.1 - 2021-01-12 =
**AIOSEO Deprecated Filter Update**
* Updated code to accommodate All-In-One SEO plugin deprecated hooks and filters.
