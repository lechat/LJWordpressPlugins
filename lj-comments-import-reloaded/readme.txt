=== LJ comments import: reloaded ===
Contributors: etspring
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
1. Deactivate original LJ comments import plugin first.
2. Upload archive contents to the `/wp-content/plugins/` directory. You shod have `/wp-content/plugins/lj-comments-import-reloaded` folder after that.
3. Make file `/wp-content/plugins/lj-comments-import-reloaded/cookie.txt` writeable for everyone.
4. Fill nine first lines of `sync_lj_comments.php` with settings from Your `wp-config.php`.
5. Activate the plugin through the `Plugins` menu in WordPress.
6. Go to `Settings - LJ Comments Import` and enter Your LiveJournal username and password. It's completely safe to enter Your password here - it will be neither stored nor transferred in plain text. LJ Comments Import plugin will encode Your password first using md5 algorithm.
7. Press the `Sync Livejournal comments now` button to do the initial comments synchronization.

== Changelog ==

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