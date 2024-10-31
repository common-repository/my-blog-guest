=== My Blog Guest ===
Contributors: MyBlogGuest
Donate link: http://myblogguest.com/
Tags: blogging, free content, infographics, promote infographics, free infographics, content marketing, content for my blog, free articles, guest articles, guest post, myblogguest, my blog guest, guest authors, guest posting, guest contributor, write for us, looking for contributors
Requires at least: 3.0.1
Tested up to: 4.4.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Integrate MyBlogGuest free content with your Wordpress blog.

== Description ==

This Wordpress plugin is meant to integrate Wordpress.org blog with myblogguest.com. For it to work, you will need:

* To register a free account at myblogguest.com
* To add your blog to MyBlogGuest.com profile and verify it (please [refer](http://myblogguest.com/blog/faq/why-and-how-do-i-verify-my-blog/) to myblogguest.com documentation for more details on verification)

After the plugin is installed and activated, it will need to be authorized, i.e. you will need to connect to myblogguest.com using your account. To do this, you will need to go: MyBlogGuest -> Settings and run "Init MyBlogGuest Connection", then you will need to provide your myblogguest.com login&password.

Mind that the plugin does not store your login and password, as well as any other private information. Your login and password is used once to generate access token - which will be used to connect to MyBlogGuest.com afterwards.

After the plugin is authorized, you will be able to view the available articles from MyBlogGuest Articles Gallery in the 'Find Articles' section. You will also be able to "offer" your blog to publish any of those articles for free.

If your "offer" is accepted, the article is moved from "Find Articles" section to "Articles Given to Me". Using the latter, you can import the article into drafts, i.e. to automatically create a new draft containing the article (mind that the article is imported as a draft for you to review and edit). You can also decline any article. The "Find Infographics" section works the same way.

You can read more about the process of making offers and publishing / declining articles that were given to you through MyBlogGuest.com in the official documentation:

* http://myblogguest.com/forum/features.php
* http://myblogguest.com/blog/

== Installation ==

This section describes how to install the plugin and get it working.


1. Upload folder `my-blog-guest` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to MyBlogGuest->settings menu, click 'Init MyBlogGuest Connection' button and authorize your copy of plugin on myblogguest.com

== Frequently Asked Questions ==

= I installed the plugin, connected it to my MyBlogGuest.com account, saw the available articles but couldn't make the offer because of the error 'Sorry, but your blog does not meet the requirements of MBG system' =

To be able to publish articles from "Find Articles" section, your blog needs to comply with minimum requirements (read more about the requirements [here](http://myblogguest.com/blog/faq/it-says-my-blog-does-not-meet-the-requirements-what-does-that-mean/) )
Besides, the article writers can set their own requirements.
Anyone can "offer" their blog in "Find Infographics" section where there are no minimum requirements.

= The plugin used to be working fine, but recently it started returning the error 'Your sites authorization has either expired, or there was a problem communicating with MyBlogGuest..' =

Most likely, your Access Token has expired. You can renew it using 'Renew Access Token' option in MyBlogGuest -> Settings. If you were unable to renew the token, MyBlogGuest.com may be having a problem connecting to your blog server. Please get help from your hosting provider.

== Screenshots ==

1. You can choose among already written articles and infographis or post your own request (or both)

2. Search for specific topics, see the number of words and author ratings, preview the first paragraphs and number of links, offer your blog to publish it for free!

3. Choose infographics and, once your offer is accepted, publish them on your blog together with original description that has been written for you

4. Importing the approved article to drafts is very easy: One click of a a mouse and you can review, publish or decline it!

== Changelog ==

= 2.0.14 = 
* Bug fix; WordPress 4.x.x support

= 2.0.6 = 
* Required to enter a reject reason

= 2.0.5 = 
* Small interface fixes

= 2.0.4 = 
* New javascript dialogs engine

= 2.0.3 = 
* Auto initialize plugin from MBG.

= 2.0.1 = 	
* Infographics support

== Upgrade Notice ==

== Arbitrary section ==

