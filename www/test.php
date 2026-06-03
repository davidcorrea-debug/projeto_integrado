<?php
$password = 'admin123';
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
echo "Hash: " . $hash . "\n";
echo "Verifica: " . (password_verify($password, $hash) ? 'Sim' : 'Não') . "\n";
?>
