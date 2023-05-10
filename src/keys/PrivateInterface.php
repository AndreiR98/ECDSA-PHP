<?php

namespace ECDSA\keys;

use Brick\Math\BigInteger;
use GMP;

interface PrivateInterface extends KeyInterface{
    public function getPublicKey() : PublicKey;

    public function getSecret() : GMP;
}