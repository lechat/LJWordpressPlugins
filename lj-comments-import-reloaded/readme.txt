=== LJ comments import: reloaded ===
Contributors: etspring, lechat
Donate link: 
Tags: comments, livejournal, import, crosspost, synchronize
Requires at least: 2.3
Tested up to: 3.1
Stable tag: trunk

Automatically synchronizes comments from Your LiveJournal blog with Your stand-alone Wordpress-based blog.

== Description ==

NEW: Supports built-in Wordpress comments threading! [see notes 2 and 3]

Automatically synchronizes comments from Your LiveJournal blog with Your stand-alone Wordpress-based blog.

All imported comments are associated with Your blog entries, crossposted to LiveJournal, and shown along with comments, written in You blog.

You can choose how often do the synchronization.

Synchronization does not slow down loading of Your pages, everything is done in background.

Note 1: This plugin has to be used together with LJXP (LiveJournal CrossPoster) plugin. Support of JournalPress and other crossposters will come in future versions (please feel free to post feature requests at the plugin homepage).

Note 2: Threading depth is limited by WordPress settings (`Settings - Discussion`).

Note 3: You should consider removing `Reply` link for imported comments, because they are not really a part of WordPress :).

== Installation ==
1. Upload archive contents to the `/wp-content/plugins/` directory. You shod have `/wp-content/plugins/lj-comments-import-reloaded` folder after that.
2. Activate the plugin through the `Plugins` menu in WordPress.
3. Go to `Settings - LJ Comments Import` and enter Your LiveJournal username and password. It's completely safe to enter Your password here - it will be neither stored nor transferred in plain text. LJ Comments Import plugin will encode Your password first using md5 algorithm.
4. Press the `Sync Livejournal comments now` button to do the initial comments synchronization.

== Changelog ==

= 0.99 =
No longer requires special permissions for cookies.txt

= 0.98.0 =
Now works correctly with results of original Wordpress LJ import plugin.
Added ability to identify livejournal friends incorrectly marked as "Anonymous". This happens due to LiveJournal API call not producing a full list of people, who commented in your journal. There is no way to identify the names of those users via LiveJournal API, so a little bit of human help is required. :)
Avatars are working correctly: only first user's avatar is supported and avatars are imported only upon first ever import.

= 0.97.1 =
Minor bugfixes with deisplaying Suspended and Deleted comments.

= 0.97 =
Fixed error with directory path.

= 0.96 =
LJ changes authorizathion system. We did it too.

== Frequently Asked Questions ==

= Can I run this plugin together with original LJ comments import plugin? =

No. You can not.

== Upgrade Notice ==

Deactivate original LJ comments import plugin first.


== Screenshots ==

1. Comments, shown at the stand-alone blog. Comments from LiveJournal are shown along with local ones.
2. Settings page for the plugin.
