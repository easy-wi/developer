<?php
//example by tbs, implentation by Nexus633

$start_time = microtime(TRUE);

$free = shell_exec('free');
$free = (string)trim($free);
$free_arr = explode("\n", $free);
$mem = explode(" ", $free_arr[1]);
$mem = array_filter($mem, function($value) {
                return ($value !== null && $value !== false && $value !== '');
              }
          ); // removes nulls from array

// puts arrays back to [0],[1],[2] after filter removes nulls
$mem = array_merge($mem);
$memtotal = round($mem[1] / 1000000,2);
$memused = round($mem[2] / 1000000,2);
$memfree = round($mem[3] / 1000000,2);
$memshared = round($mem[4] / 1000000,2);
$memcached = round($mem[5] / 1000000,2);
$memavailable = round($mem[6] / 1000000,2);

$memusage = round(($memtotal/$memavailable)*100-100,2);

$connections = `netstat -ntu | grep :80 | grep ESTABLISHED | grep -v LISTEN | awk '{print $5}' | cut -d: -f1 | sort | uniq -c | sort -rn | grep -v 127.0.0.1 | wc -l`;
$totalconnections = `netstat -ntu | grep :80 | grep -v LISTEN | awk '{print $5}' | cut -d: -f1 | sort | uniq -c | sort -rn | grep -v 127.0.0.1 | wc -l`;

$load = sys_getloadavg();
$cpuload = $load[0];

$diskfree = round(disk_free_space(".") / 1000000000);
$disktotal = round(disk_total_space(".") / 1000000000);
$diskused = round($disktotal - $diskfree);

$diskusage = round($diskused/$disktotal*100);

if ($memusage > 85 || $cpuload > 4 || $diskusage > 95) {
  $trafficlight = 'box-danger';
} elseif ($memusage > 70 || $cpuload > 2 || $diskusage > 85) {
  $trafficlight = 'box-warning';
} else {
  $trafficlight = 'box-success';
}


$end_time = microtime(TRUE);
$time_taken = $end_time - $start_time;
$total_time = round($time_taken,4);

// Load the TPL file -> template/default/custom_modules/example.tpl
$template_file = "example.tpl";

?>

