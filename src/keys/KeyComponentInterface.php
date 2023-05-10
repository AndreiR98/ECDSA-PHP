<?php

namespace ECDSA\keys;

use Brick\Math\BigInteger;
use ECDSA\Algorithms;

interface KeyComponentInterface
{
    public function generateRandomKey() : void;

    public function getPrivateKey() : PrivateKey;

    public function setPrivateKey(PrivateKey $privateKey) : void;

    public function getPublicKey() : PublicKey;

    public function setPublicKey(PublicKey $publicKey) : void;

    public function fromPemFormat(String $pem) : void;

    public function fromHexFormat(String $hexValue) : void;
}