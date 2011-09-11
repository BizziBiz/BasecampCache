<?php
  parse_mysql_dump('bccache.sql',$_POST['host'], $_POST['db'], $_POST['usr'], $_POST['pass']);
  
  function parse_mysql_dump($url,$nowhost,$nowdatabase,$nowuser,$nowpass){
	$link = mysql_connect($nowhost, $nowuser, $nowpass);
	if (!$link) { die('Not connected : ' . mysql_error());}
	$db_selected = mysql_select_db($nowdatabase, $link);
	if (!$db_selected) { die ('Can\'t use '.$nowdatabase.' : ' . mysql_error()); }
    $file_content = file($url);
    foreach($file_content as $sql_line){
     if(trim($sql_line) != "" && strpos($sql_line, "--") === false){ mysql_query($sql_line); echo $sql_line . '<br />'; }
    }
    echo 'Setup successful!';
  }
?>