<?php
if ($ui->st('w','get')!='al') {
    echo '<select id="inputSelect" name="what">';
    foreach ($data as $value) echo $value;
    echo '</select>';
}
?>