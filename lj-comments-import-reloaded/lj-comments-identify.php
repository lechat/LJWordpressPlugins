<?php
include $_SERVER['DOCUMENT_ROOT'].'/wp-config.php';

function mylog($msg) {
  // open file
  $fd = fopen('/tmp/log.log', "a");
  // write string
  fwrite($fd, date(DATE_W3C) . ' - ' . $msg . "\n");
  // close file
  fclose($fd);
}

function get_wp_option($n) {
        global $table_prefix;

        $rez = mysql_query('SELECT `option_value`
                                                                  FROM `'.$table_prefix.'options`
                                                                                        WHERE `option_name` = "'.$n.'"');
        list($v) = @mysql_fetch_row($rez);

        return @$v;
}

function set_wp_option($n, $v) {
        global $table_prefix;

        mysql_query('UPDATE `'.$table_prefix.'options`
                                                        SET `option_value` = "'.mysql_real_escape_string($v).'"
                                                        WHERE `option_name` = "'.$n.'"');
}

function lj_comments_verify_username($ljusername) {
    $login = get_wp_option('lj_comments_username');
    $password = get_wp_option('lj_comments_pass');
    $ch = curl_init();
    mylog("lj_comments_get_avatar: before curl_exec for login");

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

    mylog("lj_comments_get_avatar: before getting foaf - http://".$ljusername.".livejournal.com/data/foaf");
    curl_setopt($ch, CURLOPT_URL, 'http://'.$ljusername.'.livejournal.com/data/foaf');
    $page = curl_exec($ch);
    mylog('after getting foaf, length='.strlen($page));
    preg_match("/<foaf:img rdf:resource=(.*)\/>/",$page,$matchess);
    mylog('<img src='.$matchess[1].'>');
    if ($matchess[1]!=NULL) {
      $matchess[1] = trim(str_replace('"', '', $matchess[1]));
    }
    return $matchess[1];
}

function lj_comments_process_identify() {
  global $table_prefix;
  
  $username=trim($_POST['username']);
  $userid=$_POST['userid'];

  $userpic = lj_comments_verify_username($username);

  if ($userpic!=NULL) {
    $sql = 'INSERT INTO `'.$table_prefix.'lj_comments_users`
                  (`id`, `username`, `userpic`)
                  VALUES
                  ('.$userid.', "'.$username.'", "'.mysql_real_escape_string($userpic).'")';
mylog("Identify sql: ".$sql);
    $qresult = mysql_query($sql);
    if (!$qresult) {
      mylog('Can not insert username: '.mysql_error());
    } else {
      header('Location: '.$_SERVER['HTTP_REFERER']);
    }
  } 
}

lj_comments_process_identify();

//var_dump($_SERVER);

//HTTP_REFERER

