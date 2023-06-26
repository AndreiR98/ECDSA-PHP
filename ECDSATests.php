<?php
require_once(__DIR__.'/vendor/autoload.php');

echo "<pre>";

use ECDSA\Algorithms;
use ECDSA\curves\Curves;
use ECDSA\ECDSA;
use ECDSA\Key;
use ECDSA\Math;
use ECDSA\points\ECpoint;
use ECDSA\points\Point;

$pem = '-----BEGIN EC PRIVATE KEY-----
MHQCAQEEIDiIe/UM0hZ3FCKwTSSD4/I0cEIS4R5rW/R5UmTLXO0ZoAcGBSuBBAAK
oUQDQgAEGaWa05oZMF55Uy7lz1/f/gX090ujeyrCH/+m5aysApbE7t+WUx5fzTTW
iYvz/MSXRGITuzHcFAZ6KhSKImkrFQ==
-----END EC PRIVATE KEY-----';

//$curve = Curves::SECP256k1();
//$algorithm = Algorithms::ES256();
//
//$key = new Key($curve,$algorithm, "pollll");
//$key->fromPemFormat($pem);
//
//$signature = ECDSA::Sign("test", $key);
//$verify = ECDSA::Verify("test", $signature, $key);
//
//
////Retrieve the key
//$rInverted = gmp_invert($signature->getR(), $curve->getOrder());
//$h = (Math::hex2int(openssl_digest("test", 'sha256'))) % $curve->getOrder();
//
//$point1 = $curve->getGenerator()->multiply(gmp_init("6"));

$curve = Curves::SECP256k1();
$algorithm = Algorithms::ES256();

$key = new Key($curve, $algorithm, "kilburn");
$key->generateRandomKey();
//$key->fromPemFormat($pem);
//
$signature = ECDSA::Sign("test", $key);
$s = $signature->getR();



print_r($signature);

