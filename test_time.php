<?php
//phpinfo();
$time_old = '2020-03-09 17:20:00';
echo $time_old.'<br>';
$time_due = date('Y-m-d H:i:s', strtotime($time_old . ' + 3 hours'));
echo $time_due.'<br>';
echo '<hr>';
echo date('Y-m-d H:i:s').'<br>';