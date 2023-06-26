<?php

namespace ECDSA\keys;

use Brick\Math\BigInteger;
use Brick\Math\Exception\MathException;
use ECDSA\Algorithms;
use ECDSA\curves\Curves;
use ECDSA\points\ECpoint;
use ECDSA\points\Point;

class PublicKey implements PublicInterface
{
    private ECpoint $ECpoint;

    private Curves $curve;

    private Algorithms $algorithm;

    function __construct(ECpoint $ecPoint, Algorithms $algorithm){
        $this->ECpoint = new ECpoint($ecPoint->toAffine(), $ecPoint->getCurve());
        $this->curve = $ecPoint->getCurve();
        $this->algorithm = $algorithm;
    }

    public function getAffine() : Point {
        return $this->ECpoint->toAffine();
    }

    public function getPoint() : ECpoint {
        return $this->ECpoint;
    }

    public function getAlgorithm() : Algorithms {
        return $this->algorithm;
    }

    /**
     * @return BigInteger
     * @throws MathException
     */
    public function getX(): BigInteger
    {
        return BigInteger::of($this->ECpoint->getX());
    }

    /**
     * @return BigInteger
     * @throws MathException
     */
    public function getY(): BigInteger
    {
        return BigInteger::of($this->ECpoint->getY());
    }

    public function getCurve() : Curves {
        return $this->curve;
    }
}