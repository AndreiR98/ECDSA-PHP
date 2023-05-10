<?php

namespace ECDSA\points;

use GMP;

interface EllipticPoint
{
    public function add(ECpoint $secondPoint) : ECpoint;

    public function sub(ECpoint $secondPoint) : ECpoint;

    public function multiply(GMP $multiplicator) : ECpoint;

    //public function scale() : ECpoint;
}