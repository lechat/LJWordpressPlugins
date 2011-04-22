<?php
/*
Plugin Name: LJ Comments Import: Reloaded
Plugin URI: http://www.etspring.su/lj-comments-import-reloaded/
Description: Automatically synchronizes comments from Your LiveJournal blog with Your stand-alone Wordpress-based blog. All imported comments are associated with Your blog entries, crossposted to LiveJournal, and shown accrodingly.
Author: Filosoff, reloaded by Eternal Pring
Version: 0.97.1
Author URI: http://www.etspring.su
*/
 
//add_action('activate_lj_comments_import/lj_comments_import.php', 'lj_comments_install');
add_action('wp_head', 'lj_comments_head');
add_action('wp_footer', 'lj_comments_footer');
add_action('admin_menu', 'lj_comments_admin_menu');

add_filter('comments_array', 'lj_comments_insert');
add_filter('get_comments_number', 'lj_comments_update_number');

if (get_option("lj_comments_update_avatar") == '1') {
	add_filter('get_avatar', 'lj_comments_update_avatar');
}

if (get_option("lj_comments_insert_lj_icon") == '1') {
	add_filter('get_comment_author_link', 'lj_comments_update_username');
}


function lj_comments_install() {
	global $table_prefix, $wpdb;
	$wpdb->query("CREATE TABLE IF NOT EXISTS `".$table_prefix."lj_comments` (
  `id` int(11) unsigned NOT NULL default '0',
  `jitemid` int(11) unsigned NOT NULL default '0',
  `posterid` int(11) unsigned NOT NULL default '0',
  `parentid` int(11) unsigned NOT NULL default '0',
  `subject` varchar(255) NOT NULL default '',
  `body` text NOT NULL,
  `date` int(11) unsigned NOT NULL default '0',
  KEY `id` (`id`),
  KEY `jitemid` (`jitemid`),
  KEY `posterid` (`posterid`),
  KEY `parentid` (`parentid`),
  KEY `date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
	$wpdb->query("CREATE TABLE IF NOT EXISTS `".$table_prefix."lj_comments_meta` (
  `id` int(11) unsigned NOT NULL default '0',
  `posterid` int(11) unsigned NOT NULL default '0',
  `state` varchar(10) NOT NULL default '',
  KEY `id` (`id`),
  KEY `posterid` (`posterid`),
  KEY `state` (`state`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
	$wpdb->query("CREATE TABLE IF NOT EXISTS `".$table_prefix."lj_comments_users` (
  `id` int(11) NOT NULL default '0',
  `username` varchar(255) NOT NULL default '',
  `userpic` varchar(255) NOT NULL default '',
  KEY `id` (`id`),
  KEY `username` (`username`),
  KEY `userpic` (`userpic`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
	add_option('lj_comments_username', '');
	add_option('lj_comments_pass', '');
	add_option('lj_comments_update_every', '');
	add_option('lj_comments_last_sync', '');
	add_option('lj_comments_update_avatar', '1');
	add_option('lj_comments_insert_lj_icon', '1');
} // lj_comments_install

register_activation_hook( __FILE__, 'lj_comments_install');

function lj_comments_head() {
?>

<!-- Added by LJ Comments Import plugin -->
<script type="text/javascript" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/lj-comments-import-reloaded/lj_comments_import_js.php"></script>

<?
} // lj_comments_head

function lj_comments_footer() {
	$lj_last_sync = get_option("lj_comments_last_sync");
	$update_every = get_option("lj_comments_update_every");
	if ($lj_last_sync <= (time() - $update_every)) {
?>

<!-- Added by LJ Comments Import plugin -->
<script type="text/javascript">
	lj_comments_call_sync();
</script>

<?
	}
} // lj_comments_footer

function lj_comments_admin_menu() {
	if (function_exists('add_options_page')) {
		add_options_page('LJ Comments Import Options', 'LJ Comments Import', 7, basename(__FILE__),'lj_comments_options_subpanel');
	}
} // lj_comments_admin_menu

function lj_comments_options_subpanel() {	
	if (isset($_POST['info_update'])) {
		update_option('lj_comments_username', trim($_POST['lj_name']));
		update_option('lj_comments_pass', trim($_POST['lj_pass']));

?><div id="message" class="updated fade"><p><strong>Login and password saved.</strong></p></div><?php
	
	} // update options
	
	if (isset($_POST['settings_update'])) {
		update_option('lj_comments_update_every', trim($_POST['lj_update_every']));
		update_option('lj_comments_update_avatar', isset($_POST['lj_add_avatar']) ? '1' : '0');
		update_option('lj_comments_insert_lj_icon', isset($_POST['lj_add_icon']) ? '1' : '0');

?><div id="message" class="updated fade"><p><strong>Settings saved.</strong></p></div><?php
	
	} // update options

	$lj_username = get_option("lj_comments_username");
	$lj_pass = get_option("lj_comments_pass");
	$update_every = get_option("lj_comments_update_every");
	$lj_last_sync = get_option("lj_comments_last_sync");
	$lj_update_avatar = get_option("lj_comments_update_avatar");
	$lj_insert_icon = get_option("lj_comments_insert_lj_icon");
?>
	
<script type="text/javascript" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/lj-comments-import-reloaded/lj_comments_import_js.php"></script>
<div class="wrap">
	<h2>LJ Comments Import Options</h2>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo basename(__FILE__); ?>">
		<fieldset class="options">
			<table class="optiontable">
				<tr valign="top">
					<th scope="row" align="right">
						<label for="lj_name_input">Your&nbsp;LiveJournal&nbsp;username:&nbsp;</label>
					</th>
					<td>
						<input name="lj_name" type="text" id="lj_name_input" value="<?php echo htmlspecialchars($lj_username); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" align="right">
						<label for="lj_pass_input">Your&nbsp;LiveJournal&nbsp;password:&nbsp;</label>
					</th>
					<td>
						<input name="lj_pass" type="password" id="lj_pass_input" value="" />
						<br />
					</td>
				</tr>
			</table>
		</fieldset>
		
		<fieldset class="options">
			<div class="submit">
				<input type="submit" name="info_update" value="Update Login and Password »" />
			</div>
		</fieldset>
	</form>
	
	<hr />
	
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo basename(__FILE__); ?>">
		<fieldset class="options">
			<table class="optiontable">
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr valign="top">
					<th scope="row" align="right">
						How often do You want to synchronize Your LiveJournal comments:
					</th>
					<td>
						<select name="lj_update_every">
							<option value="300" <?php if ($update_every == '300') { echo 'selected="selected"'; } ?>>every 5 minutes</option>
							<option value="600" <?php if ($update_every == '600') { echo 'selected="selected"'; } ?>>every 10 minutes</option>
							<option value="1200" <?php if ($update_every == '1200') { echo 'selected="selected"'; } ?>>every 20 minutes</option>
							<option value="1800" <?php if ($update_every == '1800') { echo 'selected="selected"'; } ?>>every 30 minutes</option>
							<option value="3600" <?php if ($update_every == '3600') { echo 'selected="selected"'; } ?>>every 1 hour</option>
							<option value="7200" <?php if ($update_every == '7200') { echo 'selected="selected"'; } ?>>every 2 hours</option>
							<option value="10800" <?php if ($update_every == '10800') { echo 'selected="selected"'; } ?>>every 3 hours</option>
							<option value="21600" <?php if ($update_every == '21600') { echo 'selected="selected"'; } ?>>every 6 hours</option>
							<option value="43200" <?php if ($update_every == '43200') { echo 'selected="selected"'; } ?>>every 12 hours</option>
							<option value="86400" <?php if ($update_every == '86400') { echo 'selected="selected"'; } ?>>every 24 hours</option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" align="right">
						<label for="lj_add_avatar_box">Add default LiveJournal avatar to the comment:</label>
					</th>
					<td>
						<input type="checkbox" name="lj_add_avatar" value="1" id="lj_add_avatar_box" <?php echo (($lj_update_avatar == '1') ? 'checked="checked"' : ''); ?> />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" align="right">
						<label for="lj_add_icon_box">Add LiveJournal user icon to the comment poster name:</label>
					</th>
					<td>
						<input type="checkbox" name="lj_add_icon" value="1" id="lj_add_icon_box" <?php echo (($lj_insert_icon == '1') ? 'checked="checked"' : ''); ?> />
					</td>
				</tr>
			</table>
		</fieldset>
		
		<fieldset class="options">
			<div class="submit">
				<input type="submit" name="settings_update" value="Update Settings »" />
			</div>
		</fieldset>
	</form>
	
	<hr />
	
	Last successful sync: <?php echo (($lj_last_sync == '') ? '<i>unknown</i>' : strftime('%d.%m.%Y %H:%M', $lj_last_sync)); ?><br />
	<br />
	
	<input type="button" value="Sync Livejournal comments now" onclick="if (confirm('Are You sure?')) { lj_comments_adminpage_sync(); }" /><br />
	<small><i>It's ok if synchronization takes a long time. Just wait :).</i></small><br />
	<div id="lj_sync_progress" style="font-weight: bold; margin-top: 10px;"></div>
</div>
<?php

} // lj_comments_options_subpanel



function lj_comment_to_stdclass($arr, $postid, $lj_plus_id) {
//var_dump($arr);
	$tmp = new stdClass();
	$tmp->comment_ID = $arr['id'] + $lj_plus_id;
	$tmp->comment_post_ID = $postid;
	$tmp->comment_author = $arr['username'];
	$tmp->comment_author_email = '@livejournal';
        if ($arr['username']==NULL) {
	  $tmp->comment_author_url = $arr['posterid'];
        } else {
          $tmp->comment_author_url = 'http://'.$arr['username'].'.livejournal.com';
        }
	$tmp->comment_author_IP = '';
	$tmp->comment_date = strftime('%Y-%m-%d %H:%M:%S', $arr['date']);
	$tmp->comment_date_gmt = strftime('%Y-%m-%d %H:%M:%S', $arr['date']);
	$tmp->comment_content = htmlspecialchars_decode($arr['body']);
	$tmp->comment_karma = 0;
	$tmp->comment_approved = 1;
	$tmp->comment_agent = '';
	$tmp->comment_type = '';
	$tmp->comment_parent = (($arr['parentid'] == 0) ? '0' : $arr['parentid'] + $lj_plus_id);
	$tmp->user_id = 0;
	$tmp->openid = 0;
	$tmp->comment_subscribe = 'N';
	return $tmp;
}

function lj_comments_insert($comments) {
	global $post, $wpdb, $table_prefix;
//ini_set('display_errors', '1');
//error_reporting(E_ALL);
	$rez = $wpdb->get_results('SELECT lj.*, u.`username`
								FROM `'.$table_prefix.'lj_comments` as lj
								LEFT JOIN `'.$table_prefix.'postmeta` as m ON lj.`jitemid` = m.`meta_value`
								LEFT JOIN `'.$table_prefix.'lj_comments_users` as u ON lj.`posterid` = u.`id`
								LEFT JOIN `'.$table_prefix.'lj_comments_meta` as ljmeta ON lj.`id` = ljmeta.`id`
								WHERE (m.`meta_key` = "lj_itemid") and
									(m.`post_id` = '.$post->ID.')
									GROUP BY lj.`id`
									ORDER BY `date` ASC', ARRAY_A);
	if (count($rez) > 0) {
		$local_comments_cnt = $wpdb->get_var('SELECT MAX(`comment_ID`)
																					FROM `'.$table_prefix.'comments`');
		$lj_plus_id = substr($local_comments_cnt, 0, 1);
		$lj_plus_id++;
		for ($i = 1; $i < strlen($local_comments_cnt); $i++) {
			$lj_plus_id .= '0';
		}
	
		for ($i = 0; $i < count($rez); $i++) {
			$rez[$i]['pushed'] = false;
/*			if (count($comments) > 0) {
				for ($i = 0; $i < count($rez); $i++) {
					if ($comments[0]->comment_date > $rez[$i]['date']) {
						$rez[$i]['pushed'] = true;
						array_unshift($comments, lj_comment_to_stdclass($rez[$i], $post->ID, $lj_plus_id));
					}
				}
			}*/
			for ($c = 0; ($c < count($comments) and !$rez[$i]['pushed']); $c++) {
				if (($rez[$i]['date'] > intval(@strtotime(@$comments[$c-1]->comment_date))) and
						($rez[$i]['date'] <= intval(@strtotime(@$comments[$c]->comment_date)))) {
					$rez[$i]['pushed'] = true;
					$cnew = array();
					for ($t = 0; $t < $c; $t++) {
						array_push($cnew, $comments[$t]);
					}
					array_push($cnew, lj_comment_to_stdclass($rez[$i], $post->ID, $lj_plus_id));
					for ($t = $c; $t < count($comments); $t++) {
						array_push($cnew, $comments[$t]);
					}
					$comments = $cnew;
				} // found place to push into
			} // for $comments
		} // for $rez
		for ($i = 0; $i < count($rez); $i++) {
			if (!$rez[$i]['pushed']) {
				array_push($comments, lj_comment_to_stdclass($rez[$i], $post->ID, $lj_plus_id));
			}
		}
	} // if we have some lj comments
	return $comments;
}

function lj_comments_update_number($num) {
	global $post, $wpdb, $table_prefix;
	
	$rez = $wpdb->get_results('SELECT COUNT(*) as `cnt`
				FROM `'.$table_prefix.'lj_comments` as lj
				LEFT JOIN `'.$table_prefix.'postmeta` as m ON lj.`jitemid` = m.`meta_value`
				WHERE (m.`meta_key` = "lj_itemid") and
				(m.`post_id` = '.$post->ID.')
				GROUP BY lj.`id`', ARRAY_A);
/*$sql='SELECT COUNT(*) as `cnt`
				FROM `'.$table_prefix.'lj_comments` as lj
				LEFT JOIN `'.$table_prefix.'postmeta` as m ON lj.`jitemid` = m.`meta_value`
				WHERE (m.`meta_key` = "lj_itemid") and
				(m.`post_id` = '.$post->ID.')
				GROUP BY lj.`id`';
var_dump($sql);*/
	return $num + count($rez);
}

/*
function lj_comments_get_avatar($ljusername) {
	$login = get_wp_option('lj_comments_username');
	$password = get_wp_option('lj_comments_pass');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/lj-comments-import-reloaded/cookie.txt'); //get cookie from file
    curl_setopt($ch, CURLOPT_COOKIEJAR, $_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/lj-comments-import-reloaded/cookie.txt');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5');
    curl_setopt($ch, CURLOPT_URL, 'http://www.livejournal.com/');
    $page = curl_exec($ch);
    curl_setopt($ch, CURLOPT_URL, 'https://www.livejournal.com/login.bml?ret=1');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'mode=login&user='.$login.'&password='.$ljusername.'&_submit=%D0%92%D1%85%D0%BE%D0%B4+');
    $page = curl_exec($ch);
    curl_setopt($ch, CURLOPT_URL, 'http://'.$ljusername.'.livejournal.com/data/foaf');
    $page = curl_exec($ch);
    preg_match("/<foaf:img rdf:resource=(.*)\/>/",$page,$matchess);
    //echo '<img src='.$matchess[1].'>';
    return $matchess[1];
}
*/

function lj_comments_update_avatar($avatar) {
  global $comment, $wpdb, $table_prefix;
// var_dump($comment);
  if (strpos($avatar, '3bec5d490dd126237c40d5d17f17b227') !== false) {
    if ($comment->comment_author == NULL) {
      return '<form method="post" action="/wp-content/plugins/lj-comments-import-reloaded/lj-comments-identify.php"><input type="text" name="username" name="username"><input type="hidden" value="'.$comment->comment_author_url.'" name="userid"><input type="submit" value="Identify"></form>';
    }
        $sql = 'SELECT userpic FROM `'.$table_prefix.'lj_comments_users` as lj
                                      WHERE lj.`username` = "'.$comment->comment_author.'"';
        $userpic = $wpdb->get_results($sql);
//        var_dump($userpic);

        return '<img src="'.$userpic[0]->userpic.'" style="border: 0pt none ; vertical-align: bottom;" height="100" width="91"> '.$contents;
//		return '<img src="/wp-content/plugins/lj-comments-import-reloaded/lj_avatar.png" alt="" />';
	}
	else {
		return $avatar;
	}
}

function lj_comments_update_username($contents) {
	global $comment, $wpdb, $table_prefix;

// var_dump($comment);
	if ($comment->comment_author_email == '@livejournal') {
        $sql = 'SELECT userpic FROM `'.$table_prefix.'lj_comments_users` as lj
                                      WHERE lj.`username` = "'.$comment->comment_author.'"';
        $userpic = $wpdb->get_results($sql);
//        var_dump($userpic);
        
		return '<img src="'.$userpic[0]->userpic.'" style="border: 0pt none ; vertical-align: bottom;" height="100" width="91"> '.$contents;
	}
	else {
		return $contents;
	}
}

?>
