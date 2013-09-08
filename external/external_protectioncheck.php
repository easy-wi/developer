

<?php


if (isset($_POST['adresse'])) {
 $fail=0;
 $adresse=explode(":", $_POST['adresse']);
 if(isset($adresse['0']) and filter_var($adresse['0'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) and preg_match("/^(0|([1-9]\d{0,3}|[1-5]\d{4}|[6][0-5][0-5]([0-2]\d|[3][0-5])))$/", $adresse['1'])){
  $ip=$adresse['0'];
  $port=$adresse['1'];
 } else {
  $fail=1;
 }
 if ($fail == '0') {
  // If you need a yes or no following is sufficient:
  $status=file_get_contents("https://webinterface.domain.tld/protectioncheck.php?ip=$ip&po=$port");
 } else {
  echo "formular";
  if ( $status == 'yes' ) {
   echo "Server ist gesch&uuml;tzt";
  } else if ( $status == 'no' ) {
   echo "Server ist ungesch&uuml;tzt";
  } else if ( $status == 'unknown' ) {
   echo "Ein Server mit dieser IP wurde nicht gefunden bitte versuche es erneut:";
  }
 } else {
  echo "hier das Formular ausgeben";
 }
 // In case you need status information and or the log:
 libxml_set_streams_context(stream_context_create(array('http'=>array('user_agent'=>'PHP libxml agent',null))));
 $xml = simplexml_load_file("https://webinterface.domain.tld/protectioncheck.php?ip=$ip&po=$port&gamestring=xml");
 if ($xml != 'unknown') {
  echo $xml->hostname.'</br>';
  echo $xml->gametype.'</br>';
  echo $xml->map.'</br>';
  echo $xml->numplayers.'</br>';
  echo $xml->maxplayers.'</br>';
  echo $xml->protection.'</br>';
  if ($xml->psince != '0000:00:00') {
   echo $xml->psince.'</br>';
  }
  foreach ($xml->actions->action as $action) {
   echo $action->time.' '.$action->log.'</br>';
  }
 }
} else {
?>
<form action="external_protectioncheck.php" method="post">
<table>
 <tr>
  <td><input type="text" name="serveraddress" value="11.111.11.111:27015" required="required" maxlength="22" /></td>
  <td><input type="submit" value="Check"/></td>
 </tr>
</table>
</form>
<?php
}
?>

