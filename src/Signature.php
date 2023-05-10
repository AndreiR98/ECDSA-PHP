<?php

namespace ECDSA;

use GMP;
class Signature
{
    private GMP $r;

    private GMP $s;

    function __construct(GMP $r, GMP $s){
        $this->r = $r;
        $this->s = $s;
    }

    public function getR() : GMP {
        return $this->r;
    }

    public function getS() : GMP {
        return $this->s;
    }
}