<?php 

$cmd = "SELECT * FROM events";
$events = mysqli_query($conn,$cmd);
$arr = [];
$i = 0;
while($event = mysqli_fetch_assoc($events)){
    $arr[$i] = $event;
    $i ++;
}
var_dump($arr);

?>