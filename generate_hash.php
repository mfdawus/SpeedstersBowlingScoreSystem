<?php
// Generate password hash for 'password123'
$password = 'password123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: " . $password . "\n";
echo "Hash: " . $hash . "\n";
echo "\nSQL Update Command:\n";
echo "UPDATE users SET password = '" . $hash . "' WHERE username = 'fiq';\n";
echo "UPDATE users SET password = '" . $hash . "' WHERE username = 'farrish';\n";
echo "UPDATE users SET password = '" . $hash . "' WHERE username = 'adam';\n";
echo "UPDATE users SET password = '" . $hash . "' WHERE username = 'daniel';\n";
echo "UPDATE users SET password = '" . $hash . "' WHERE username = 'najib';\n";
echo "UPDATE users SET password = '" . $hash . "' WHERE username = 'hasif';\n";
echo "UPDATE users SET password = '" . $hash . "' WHERE username = 'ammar';\n";
echo "UPDATE users SET password = '" . $hash . "' WHERE username = 'haziq';\n";
echo "UPDATE users SET password = '" . $hash . "' WHERE username = 'tuan_azmy';\n";
echo "UPDATE users SET password = '" . $hash . "' WHERE username = 'mia';\n";
echo "UPDATE users SET password = '" . $hash . "' WHERE username = 'awin';\n";
echo "UPDATE users SET password = '" . $hash . "' WHERE username = 'eila';\n";
echo "UPDATE users SET password = '" . $hash . "' WHERE username = 'tacha';\n";
echo "UPDATE users SET password = '" . $hash . "' WHERE username = 'faiz';\n";
echo "UPDATE users SET password = '" . $hash . "' WHERE username = 'azie';\n";
echo "UPDATE users SET password = '" . $hash . "' WHERE username = 'erul';\n";
echo "UPDATE users SET password = '" . $hash . "' WHERE username = 'kodey';\n";
echo "UPDATE users SET password = '" . $hash . "' WHERE username = 'farah';\n";
echo "UPDATE users SET password = '" . $hash . "' WHERE username = 'lan';\n";
echo "UPDATE users SET password = '" . $hash . "' WHERE username = 'wanie';\n";
?>
