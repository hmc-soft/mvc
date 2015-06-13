<?php

//Update key params and run to generate new keys.

$key_size = mcrypt_get_key_size(MCRYPT_RIJNDAEL_256,MCRYPT_MODE_CBC);
$key = mcrypt_create_iv($key_size, MCRYPT_RAND);

$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

echo 'Key: ' . base64_encode($key) . "\n";
echo 'IV: ' . base64_encode($iv);
