<?php

function validateDateTime($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
//var_dump( validateDate('2017-05-18 %60%20sleep%204%60'));
?>
