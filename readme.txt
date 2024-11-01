=== Smart App Banners ===
Contributors: clevelandwebdeveloper
Donate link: http://www.clevelandwebdeveloper.com/wordpress-plugins/donate.php
Tags: smart app banner, smart app banners, iphone app, app, banner, iOS, iphone, smart, app store badge, android badge, app store badge, app store, download on the app store, download for android, android, apps
Requires at least: 2.9
Tested up to: 3.5.1
Stable tag: 1.2
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automatically implement Safari's new Smart App Banner feature on your Wordpress site. Easily promote your iphone and android apps using badges.

== Description ==

Automatically implement Safari's new Smart App Banner feature on your Wordpress site. The banner provides a standardized method of promoting apps on the App Store from any website. The banner by default will appear on all pages. In order for this to work you have to enter your app id in **Settings > Smart App Banners > Your App ID**.

= New Features =

* Added the ability to disable site-wide smart app banner and assign banners to specific posts and pages.
* Added "Download on the App Store" and "Download for Android" badges. You can add the badges:
* as widgets Settings->Widgets-> **Motech Download on the App Store** and **Motech Download for Android**
* anywhere using shortcodes **[app-store-download id=yourid]** and **[android-download id=yourid]** 

== Installation ==

1. From WP admin > Plugins > Add New
1. Search "Smart App Banners" under search and hit Enter
1. Click "Install Now"
1. Click the Activate Plugin link
1. Enter your app id in Settings > Smart App Banners > Your App ID

== Frequently asked questions ==

= How do I find out what my app id is? =

Check out the [iTunes Link Maker](http://itunes.apple.com/linkmaker/), type the name of your app in the Search field, and select the appropriate country and media type. In the results, find your app and select iPhone App Link in the column on the right. Your app ID is the nine-digit number in between id and ?mt.

= Where will my app banner be displayed? =

By default it will be displayed on the top of every page. You can disable this by unchecking the box at  Settings > Smart App Banners > **Show banner on all pages**

= How do I add a smart app banner on specific posts and pages =

When you are editing a post or page, there is a field box Smart App Banners > Your App ID.

= How do I add a "Download on the App Store" badge using widgets? =

Settings->Widgets-> **Motech Download on the App Store**

= Using shortcodes? =

[app-store-download id="yourid"]. if no id is entered plugin will try to use the the sitewide app id from Settings > Smart App Banners > Your App ID

= How do I add a "Download for Android" badge using widgets? =

Settings->Widgets-> **Motech Download for Android**

= Using shortcodes? =

[android-download id="yourid"]. id is required.

= Are there any optional parameters I can include in shortcodes? =

Yes. **size** and **verticalalign**. By default they are set to 100 and top. for example [android-download id="yourid" size="50" verticalalign="bottom"] would produce a download for android badge which is half sized and aligned at the bottom of the current line.

= How do I know what to use for my android app id? =

It's your apps Package Name. Read about it [here](http://developer.android.com/distribute/googleplay/promote/linking.html)

== Screenshots ==

1. Smart App Banners In Action
1. Enter your app id in Settings > Smart App Banners > Your App ID
1. You can now assign banners to specific posts and pages
1. "Download on the App Store" and "Download for Android" badges viewed on a mobile device
1. Easily add "Download on the App Store" and "Download for Android" badges using widgets or [app-store-download id=yourid] and [android-download id=yourid] shortcodes

== Changelog ==

= 1.1 =
* Added the ability to set smart app banners on specific posts and pages.
* Added "Download on the App Store" and "Download for Android" badges. You can add the badges:
* as widgets Settings->Widgets-> **Motech Download on the App Store** and **Motech Download for Android**
* anywhere using shortcodes **[app-store-download id=yourid]** and **[android-download id=yourid]** 

= 1.0 =
* Initial version

== Upgrade Notice ==

= 1.1 =
This version adds the ability to disable site-wide smart app banner and assign banners to specific posts and pages. Also new to this version is the ability to insert "Download on the App Store" and "Download for Android" badges using widgets and shortcodes.