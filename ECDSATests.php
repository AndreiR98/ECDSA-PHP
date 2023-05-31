<?php
require_once(__DIR__.'/vendor/autoload.php');

echo "<pre>";

use ECDSA\Algorithms;
use ECDSA\curves\Curves;
use ECDSA\ECDSA;
use ECDSA\Key;

$pem = '-----BEGIN PUBLIC KEY-----
MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEx2HtKUYnSxJw/bgED+2LJQMAIjyA
+I2plnVS50JCXaIALKstac37wB1lUvasfdvbE+nNbCvMkGPdMjLluMfT3g==
-----END PUBLIC KEY-----';

$curve = Curves::NIST256p();
$algorithm = Algorithms::ES256();

$key = new Key($curve,$algorithm, "pollll");
$key->fromPemFormat($pem);

$Y = gmp_init("115713166516540571362874698292943038916433309647960401451137263415878500438387", 10);

$X = $curve->getGenerator()->multiply(gmp_init("2", 10))->toAffine();

//$Y =

print_r($X);
