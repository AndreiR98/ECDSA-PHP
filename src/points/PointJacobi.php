<?php

namespace ECDSA\points;

use GMP;

abstract class PointJacobi
{
    protected function addPoints(ECpoint $secondPoint) : ECpoint {
        //Add 2 points in Jacobi coords, faster method always
        return $this->addPoint($secondPoint);
    }

    protected function subPoints(ECpoint $secondPoint) : ECpoint {
        $order = $this::getCurve()->getOrder();

        //Negate 2nd point
        $newY = $order - $secondPoint->getY();
        $negPoint = new ECpoint(new Point($secondPoint->getX(), $newY, $secondPoint->getZ()), $this::getCurve());

        return $this->addPoint($negPoint);
    }

    protected function multiplyPoints(GMP $multiplicator) : ECpoint {
        //Scale
        $scaledPoint = $this->scalePoint($this);

        $point = new ECpoint(new Point(gmp_init(0, 10), gmp_init(0, 10), gmp_init(1, 10)), $this::getCurve());

        //$point = $this;

        foreach (array_reverse($this->naf($multiplicator)) as $i){
            $point = $point->double();

            if($i < 0){
                $point = $point->addPoint(new ECpoint(new Point($scaledPoint->getX(), -$scaledPoint->getY(), gmp_init(1, 10)), $this::getCurve()));
            } else if ($i > 0) {
                $point = $point->addPoint(new ECpoint(new Point($scaledPoint->getX(), $scaledPoint->getY(), gmp_init(1, 10)), $this::getCurve()));
            }
        }

        return $point;
    }

    public function scalePoint(ECpoint $point) : Point {
        $p = $this->getCurve()->p();

        $Z_inv = gmp_invert($point->getZ(), $p);
        $ZZ_inv = ($Z_inv * $Z_inv) % $p;

        $X = ($point->getX() * $ZZ_inv) % $p;
        $Y = ($point->getY() * $ZZ_inv * $ZZ_inv) % $p;

        return new Point($X, $Y, gmp_init(1, 10));
    }

    private function addPoint(ECpoint $secondPoint) : ECpoint{
        if (($this->getY() == 0)||($this->getZ() == 0)){
            return new ECpoint(new Point($secondPoint->getX(), $secondPoint->getY(), $secondPoint->getZ()), $this->getCurve());
        }

        if (($secondPoint->getY() == 0)||($secondPoint->getZ() == 0)){
            return new ECpoint(new Point($this->getX(), $this->getY(), $this->getZ()), $this->getCurve());
        }

        if ($this->getZ() == $secondPoint->getZ()) {
            if($this->getZ() == 1) {
                return $this->add_with_z_1($secondPoint);
            }
            return $this->add_with_z_eq($secondPoint);
        }

        if ($this->getZ() == 1){
            return $this->add_with_z2_1($secondPoint);
        }

        if ($secondPoint->getZ() == 1){
            return $this->add_with_z2_1($secondPoint);
        }

        return $this->add_with_z_ne($secondPoint);
    }
    private function add_with_z_1(ECpoint $secondPoint) : ECpoint {
        $H = $secondPoint->getX() - $this->getX();
        $HH = $H * $H;
        $I = (4 * $HH) % $this->getCurve()->p();
        $J = $H * $I;
        $r = 2 * ($secondPoint->getY() - $this->getY());

        if(($H == 0) && ($r == 0)){
            return $this->double_with_z_1();
        }

        $V = $this->getX() * $I;
        $X3 = ($r ** 2 - $J - 2 * $V) % $this->getCurve()->p();
        $Y3 = ($r * ($V - $X3) - 2 * $this->getY() * $J) % $this->getCurve()->p();
        $Z3 = (2 * $H) % $this->getCurve()->p();

        return new ECpoint(new Point($X3, $Y3, $Z3), $this->getCurve());
    }

    private function add_with_z_eq(ECpoint $secondPoint) : ECpoint {
        $p = $this->getCurve()->p();

        $A = ($secondPoint->getY() - $this->getY()) ** 2 % $p;
        $B = ($this->getX() * $A) % $p;
        $C = $secondPoint->getX() * $A;
        $D = ($secondPoint->getY() - $this->getY()) ** 2 % $p;

        if (($A == 0)&&($D == 0)){
            return $this->double();
        }

        $X3 = ($D - $B - $C) % $p;
        $Y3 = (($secondPoint->getY() - $this->getY()) * ($B - $X3) -  $this->getY() * ($C -  $B)) % $p;
        $Z3 = ($this->getZ() * ($secondPoint->getX() - $this->getX())) % $p;

        return new ECpoint(new Point($X3, $Y3, $Z3), $this->getCurve());
    }

    private function add_with_z2_1(ECpoint $secondPoint) : ECpoint {
        $p = $this->getCurve()->p();

        $Z1Z1 = ($this->getZ() * $this->getZ()) % $p;
        $U2 = ($secondPoint->getX() * $Z1Z1) % $p;
        $S2 = ($secondPoint->getY() * $this->getZ() * $Z1Z1) % $p;
        $H = ($U2 - $this->getX()) % $p;
        $HH = ($H * $H) % $p;

        $I = (4 * $HH) % $p;
        $J = $H * $I;

        $r = 2 * ($S2 - $this->getY()) % $p;

        if (($r == 0)&&($H == 0)){
            return $this->double_with_z_1();
        }

        $V = $this->getX() * $I;
        $X3 = ($r * $r - $J - 2 * $V) % $p;
        $Y3 = ($r * ($V - $X3) - 2 * $this->getY() * $J) % $p;
        $Z3 = (($this->getZ() + $H) ** 2 - $Z1Z1 - $HH) % $p;

        return new ECpoint(new Point($X3, $Y3, $Z3), $this->getCurve());
    }

    private function add_with_z_ne(ECpoint $secondPoint) : ECpoint {
        $p = $this->getCurve()->p();

        $Z1Z1 = ($this->getZ() * $this->getZ()) % $p;
        $Z2Z2 = ($secondPoint->getZ() * $secondPoint->getZ()) % $p;
        $U1 = ($this->getX() * $Z2Z2) % $p;
        $U2 = ($secondPoint->getX() * $Z1Z1) % $p;
        $S1 = ($this->getY() * $secondPoint->getY() * $Z2Z2) % $p;
        $S2 = ($secondPoint->getY() * $this->getZ() * $Z1Z1) % $p;
        $H = $U2 - $U1;
        $I = (4 * ($H * $H)) % $p;
        $J = ($H * $I) % $p;

        $r = 2 * ($S2 - $S1) % $p;

        if (($H == 0)&&($r == 0)){
            return $this->double();
        }
        $V = $U1 * $I;
        $X3 = ($r * $r - $J - 2 * $V) % $p;
        $Y3 = ($r * ($V - $X3) - 2 * $S1 * $J) % $p;
        $Z3 = (($this->getZ() + $secondPoint->getZ()) ** 2 - $Z1Z1 - $Z2Z2) % $p;

        return new ECpoint(new Point($X3, $Y3, $Z3), $this->getCurve());
    }

    public function double() : ECpoint {
        $p = $this->getCurve()->p();
        $a = $this->getCurve()->a();

        if ($this->getZ() == 1){
            return $this->double_with_z_1();
        }

        if (($this->getY() == 0)||($this->getZ() == 0)) {
            return new ECpoint(new Point(gmp_init(0, 10), gmp_init(0, 10), gmp_init(1, 10)), $this->getCurve());
        }

        $XX = ($this->getX() * $this->getX()) % $p;
        $YY = ($this->getY() * $this->getY()) % $p;

        if ($YY == 0){
            return new ECpoint(new Point(gmp_init(0, 10), gmp_init(0, 10), gmp_init(1, 10)), $this->getCurve());
        }

        $YYYY = ($YY * $YY) % $p;
        $ZZ = ($this->getZ() * $this->getZ()) % $p;

        $S = 2 * (($this->getX() + $YY) ** 2 - $XX - $YYYY) % $p;
        $M = (3 * $XX + $a * $ZZ * $ZZ) % $p;
        $T = ($M * $M - 2 * $S) % $p;
        $Y3 = ($M * ($S- $T) - 8 * $YYYY) % $p;
        $Z3 = (($this->getY() + $this->getZ()) ** 2 - $YY - $ZZ) % $p;

        return new ECpoint(new Point($T, $Y3, $Z3), $this->getCurve());
    }

    public function double_with_z_1() : ECpoint {
        $p = $this->getCurve()->p();
        $a = $this->getCurve()->a();

        $XX = ($this->getX() * $this->getX()) % $p;
        $YY = ($this->getY() * $this->getY()) % $p;

        if ($YY == 0){
            return new ECpoint(new Point(gmp_init(0, 10), gmp_init(0, 10), gmp_init(1, 10)), $this->getCurve());
        }

        $YYYY = ($YY * $YY) % $p;
        $S = 2 * (($this->getX() + $YY) ** 2 - $XX - $YYYY) % $p;
        $M = 3 * $XX + $a;
        $T = ($M * $M - 2 * $S) % $p;
        $Y3 = ($M * ($S - $T) - 8 * $YYYY) % $p;
        $Z3 = 2 * $this->getY() % $p;

        return new ECpoint(new Point($T, $Y3, $Z3), $this->getCurve());
    }

    private function naf(GMP $mult): array
    {
        $ret = [];

        while ($mult > 0){
            if ($mult % 2 > 0) {
                $nb = $mult % 4;
                if ($nb >= 2){
                    $nb -= 4;
                }
                array_push($ret, $nb);
                $mult -= $nb;
            }else{
                array_push($ret, 0);
            }
            $mult = gmp_div($mult, 2);
        }
        return $ret;
    }
}