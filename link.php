<?php 
 $target = '/home/fjjwblgz/public_html/storage/app/public'; 
 $link = '/home/fjjwblgz/public_html/storage'; 
 
 if (symlink($target, $link)) {
     echo "Symlink created successfully from $target to $link";
 } else {
     echo "Failed to create symlink. Check permissions or if the link already exists.";
 }
