<?php
if ($w!='al') {
    echo '<select name="what">';
foreach ($data as $value) echo $value;
echo '</select>';
} else {
echo '';
}
?>