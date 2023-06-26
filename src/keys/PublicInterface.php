<?php

namespace ECDSA\keys;

use Brick\Math\BigInteger;
use ECDSA\points\ECpoint;

interface PublicInterface
{
    public function getPoint() : ECpoint;

    public function getX(): BigInteger;

    public function getY(): BigInteger;
}