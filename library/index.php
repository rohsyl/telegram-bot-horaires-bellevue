<?php

require __DIR__ . '/../constant.php';
require __DIR__ . '/../vendor/autoload.php';

include 'HES_Schedule.php';

/*
$classes = HES_Schedule::getClassList();
print_r($classes);
*/

$noClasse = 204;

$schedule = HES_Schedule::getSchedule($noClasse, false);

echo '<pre>';
print_r($schedule);
echo '</pre>';
