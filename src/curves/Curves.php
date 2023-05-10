<?php
/**
 * Class Curve
 *
 * @package ecdsa/ecdsaphp
 * @author  Rotaru Andrei <rotaru.andrei98@gmail.com>
 * 
 * Elliptic Curve Equation over a finite field
 * 
 * y^2 = x^3+A*x+B (mod P)
 */


namespace ECDSA\curves;
use ECDSA\ECpoint2;
use ECDSA\PointJacobi2;
use ECDSA\points\ECpoint;
use ECDSA\points\Point;

Class Curves {

	private $A;
	
	private $B;
	
	private $P;
	
	public $N;
	
	public $name;
	
	public $oid;

	public $nistName;

    private Point $generator;

	function __construct($A, $B, $P, $N, $Gx, $Gy, $name, $oid, $nistName=''){
		$this->A = $A;
		$this->B = $B;
		$this->P = $P;
		$this->N = $N;
		$this->generator = new Point($Gx, $Gy, gmp_init(1, 10));
		$this->name = $name;
		$this->oid = $oid;
		$this->nistName = $nistName;
	}

	public function getGenerator(): ECpoint
    {
		return new ECpoint($this->generator, $this);
	}

    public function getNistName() : String {
        return $this->nistName;
    }

    public function getOid() : Array {
        return $this->oid;
    }
	public function getOrder(){
		return $this->N;
	}

	public function a() {
		return $this->A;
	}

	public function p() {
		return $this->P;
	}

	public static function NIST256p(){
		return new Curves(
	        gmp_init("-3"),
	        gmp_init("41058363725152142129326129780047268409114441015993725554835256314039467401291"),
	        gmp_init("115792089210356248762697446949407573530086143415290314195533631308867097853951"),
	        gmp_init("115792089210356248762697446949407573529996955224135760342422259061068512044369"),
	        gmp_init("48439561293906451759052585252797914202762949526041747995844080717082404635286"),
	        gmp_init("36134250956749795798585127919587881956611106672985015071877198253568414405109"),
	        'prime256v1',
	        [1, 2, 840, 10045, 3, 1, 7],
	        'prime256v1'
        );
	}

    public static function TestNIST256p(){
        return new Curves(
            gmp_init("-2"),
            gmp_init("15"),
            gmp_init("23"),
            gmp_init("23"),
            gmp_init("4"),
            gmp_init("5"),
            'prime256v1',
            [1, 2, 840, 10045, 3, 1, 7],
            'prime256v1'
        );
    }

	public static function SECP256k1(){
		return new Curves(
			gmp_init("0"),
			gmp_init("7"),
			gmp_init("115792089237316195423570985008687907853269984665640564039457584007908834671663"),
			gmp_init("115792089237316195423570985008687907852837564279074904382605163141518161494337"),
			gmp_init("55066263022277343669578718895168534326250603453777594175500187360389116729240"),
			gmp_init("32670510020758816978083085130507043184471273380659243275938904335757337482424"),
			'SECP256k1',
			[1, 3, 132, 0, 10],
			'secp256k1'
		);
	}
}

