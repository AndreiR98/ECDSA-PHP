<?php

/**
 * Class EcDSA
 *
 * @package ecdsa/ecdsaphp
 * @author  Rotaru Andrei <rotaru.andrei98@gmail.com>
 */

namespace ECDSA;

use Brick\Math\BigInteger;
use ECDSA\points\ECpoint;
use GMP;

Class ECDSA {
    /**
     * @param string $message
     * 
     * 
     * @param pem $secretKey
     *
     * @return array
     */
    public static function Sign(String $message, Key $key) : Signature{
        //Recover the secret key from pem by KID
        $secretKey = $key->getPrivateKey()->getSecret();

        $curve = $key->getCurve();
        $algorithm = $key->getAlgorithm();

        $order = $curve->getOrder();
        
        //Recover the hash method for this curve
        $hash = $algorithm->getHash();

        
        $k  = (Math::hex2int(hash_hmac($hash, $message, Math::hexlify(gmp_export($secretKey))))) % $order;

        $h = (Math::hex2int(openssl_digest($message, $hash))) % $order;

        $r = $curve->getGenerator()->multiply(gmp_init($k, 10))->toAffine();

        $s = (gmp_invert($k, $order)*($h + ($r->getX() * $secretKey) % $order)) % $order;

        return new Signature($r->getX(), gmp_init($s, 10));
    }

    /**
     * @param string $message
     * 
     * 
     * @param array(r, s) $signature
     * 
     * @param byte key
     *
     * @return bool
     */

    public static function Verify(String $message, Signature $signature, Key $key) : bool{

        $Px = $key->getPublicKey()->getAffine()->getX();
        $Py = $key->getPublicKey()->getAffine()->getX();

        $r = $signature->getR();
        $s = $signature->getS();

        $curve = $key->getPublicKey()->getCurve();
        $algorithm = $key->getPublicKey()->getAlgorithm();
        $order = $curve->getOrder();

        $generator = $curve->getGenerator();

        $publicPoint = $key->getPublicKey()->getPoint();

        $hash = (Math::hex2int(openssl_digest($message, $algorithm->getHash()))) % $order;

        $c = gmp_invert($s, $order);
        $u1 = ($hash * $c) % $order;
        $u2 = ($r * $c) % $order;

        $pu1 = new ECpoint($generator->multiply(gmp_init($u1, 10))->toAffine(), $curve);
        $pu2 = new ECpoint($publicPoint->multiply(gmp_init($u2, 10))->toAffine(), $curve);

        $publicVerificationPoint = $pu1->add($pu2);
        $vx = $publicVerificationPoint->toAffine()->getX() % $order;

        //print_r($publicVerificationPoint);

        return $r == $vx;
    }
}