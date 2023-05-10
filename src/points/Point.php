<?php

namespace ECDSA\points;

use GMP;

class Point {

    private GMP $x;

    private GMP $y;

    private GMP $z;
    function __construct(GMP $x, GMP $y, GMP $z) {
        $this->x = $x;
        $this->z = $z;
        $this->y = $y;
    }

    /**
     * @return GMP
     */
    public function getX(): GMP
    {
        return $this->x;
    }

    /**
     * @return GMP
     */
    public function getY(): GMP
    {
        return $this->y;
    }

    /**
     * @return GMP
     */
    public function getZ(): GMP
    {
        return $this->z;
    }
}