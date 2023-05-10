# ECDSA-PHP
 Elliptic Curve Cryptography for PHP
 
 ##Usage

 ```php
 use ECDSA\Algorithms;use ECDSA\curves\Curves;use ECDSA\ECDSA;use ECDSA\Key;

$pem = 'EC PRIVATE KEY PEM FORMAT';

$curve = Curves::NIST256p();
$algorithm = Algorithms::ES256();

$key = new Key($pem, '', $curve, $algorithm);

$message = 'HELLO';

$Signature = ECDSA::Sign($message, $key);

$verif = ECDSA::Verify($message, $Signature, $key);

var_dump($verif);
 ```
