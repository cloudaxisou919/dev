<?php 
$name = null;
$pass = null;
echo exec("cd.. ; composer update",$name,$pass);
print_r($name);
?>