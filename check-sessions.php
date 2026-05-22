<?php
$sessionsDir = 'storage/framework/sessions';
echo "Directory: " . $sessionsDir . "\n";
echo "Exists: " . (file_exists($sessionsDir) ? 'YES' : 'NO') . "\n";
echo "Is Writable: " . (is_writable($sessionsDir) ? 'YES' : 'NO') . "\n";
