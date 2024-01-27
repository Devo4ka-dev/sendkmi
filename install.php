<?php
header('Content-Type: text/plain');

$file_content .= "sudo echo \"#!/bin/bash\" > /usr/bin/kmi\n";
$file_content .= "sudo echo \"curl -F 'kmi=<-' https://kmi.devo4ka.top/\" >> /usr/bin/kmi\n";
$file_content .= "sudo chmod +x /usr/bin/kmi\n";

echo $file_content;
?>
