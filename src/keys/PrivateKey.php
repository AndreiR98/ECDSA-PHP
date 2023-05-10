<?php

namespace ECDSA\keys;

use Brick\Math\BigInteger;
use ECDSA\Algorithms;
use ECDSA\curves\Curves;
use ECDSA\Math;
use GMP;

class PrivateKey implements PrivateInterface {

    private GMP $secret;

    private PublicKey $publicKey;

    public function getKeyType(): KeyTypes
    {
        return KeyTypes::PrivateKey;
    }

    function __construct(String $secret, Curves $curve, Algorithms $algorithm)
    {
        $this->secret = gmp_init($secret, 16);

        $this->publicKey = self::computePrivateKey($this->secret, $curve, $algorithm);
    }

    public function getPublicKey(): PublicKey
    {
        return $this->publicKey;
    }

    public function getSecret(): GMP
    {
        return $this->secret;
    }

    private static function computePrivateKey(GMP $secret, Curves $curve, Algorithms $algorithm) : PublicKey {
        return new PublicKey($curve->getGenerator()->multiply($secret), $algorithm);
    }
}