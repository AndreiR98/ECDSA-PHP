<?php

namespace ECDSA\points;

use ECDSA\curves\Curves;
use ECDSA\PointJacobi2;
use GMP;

class ECpoint extends PointJacobi implements EllipticPoint {

    private GMP $x;

    private GMP $y;

    private GMP $z;

    private Curves $curve;

    function __construct(Point $point, Curves $curve){
        $this->x = $point->getX();
        $this->z = $point->getZ();
        $this->y = $point->getY();
        $this->curve = $curve;
    }

    public function toAffine() : Point {
        return new Point($this->scaleX(), $this->scaleY(), gmp_init(1, 10));
    }

    private function scaleX() : GMP {
        if($this->z == 1) {
            return $this->x;
        }

        $z = gmp_invert($this->z , $this::getCurve()->p());
        return ($this->x * $z ** 2) % $this::getCurve()->p();
    }

    private function scaleY() : GMP {
        if($this->z == 1) {
            return $this->y;
        }

        $z = gmp_invert($this->z, $this::getCurve()->p());
        return ($this->y * $z ** 3) % $this::getCurve()->p();
    }

    public function getCurve() : Curves {
        return $this->curve;
    }

    public function getX() : GMP {
        return $this->x;
    }

    public function getY() : GMP {
        return $this->y;
    }

    public function getZ() : GMP {
        return $this->z;
    }

    public function add(ECpoint $secondPoint): ECpoint
    {
        return parent::addPoints($secondPoint);
    }

    public function sub(ECpoint $secondPoint): ECpoint
    {
        return parent::subPoints($secondPoint);
    }

    public function multiply(GMP $multiplicator): ECpoint
    {
        return parent::multiplyPoints($multiplicator);
    }

    public function negate(): ECpoint {
        return parent::negate();
    }
}