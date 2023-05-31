<?php
namespace ECDSA;

use Brick\Math\BigInteger;
use ECDSA\curves\Curves;
use ECDSA\keys\KeyComponentInterface;
use ECDSA\keys\PrivateKey;
use ECDSA\keys\PublicKey;
use ECDSA\points\ECpoint;
use ECDSA\points\Point;
use Exception;

class Key implements KeyComponentInterface {

	private PrivateKey $privateKey;

    private PublicKey $publicKey;

    private Algorithms $algorithm;

    private Curves $curve;

    private String $kID;

	public function __construct(Curves $curve, Algorithms $algorithm, $kID=''){
        try {
            if($kID != null || $kID != ''){
                $this->kID = $kID;
            }

            if($curve != null) {
                $this->curve = $curve;
            }

            if($algorithm != null) {
                $this->algorithm = $algorithm;
            }
        }catch (Exception $exception){}
	}

    public function generateRandomKey(): void
    {
        // TODO: Implement generateRandomKey() method.
    }

    public function getCurve() : Curves {
        return $this->curve;
    }

    public function getAlgorithm() : Algorithms {
        return $this->algorithm;
    }

    public function getPrivateKey(): PrivateKey
    {
        return $this->privateKey;
    }

    public function getPublicKey(): PublicKey
    {
        return $this->publicKey;
    }

    public function fromPemFormat(String $pem): void
    {
        try{
            if(openssl_pkey_get_private($pem)){
                $res = openssl_pkey_get_private($pem);

                $key_res = openssl_pkey_get_details($res)['ec'];

                if($key_res['curve_name'] == $this->curve->getNistName()){
                    $this->privateKey = new PrivateKey(Math::hexlify($key_res['d']), $this->curve, $this->algorithm);

                    if(($this->publicKey->getAffine()->getX() == Math::hex2int(Math::hexlify($key_res['x']))) &&
                        ($this->publicKey->getAffine()->getY() == Math::hex2int(Math::hexlify($key_res['y'])))){
                        $this->publicKey = $this->getPrivateKey()->getPublicKey();
                    }
                }
            } else {
                try{
                    $res = openssl_pkey_get_public($pem);

                    $key_res = openssl_pkey_get_details($res)['ec'];

                    $point = new Point(
                        gmp_init(Math::hexlify($key_res['x']), 16),
                        gmp_init(Math::hexlify($key_res['y']), 16),
                        gmp_init(1, 10)
                    );

                    $ecPoint = new ECpoint($point, $this->curve);

                    $publicKey = new PublicKey($ecPoint, $this->algorithm);

                    $this->publicKey = $publicKey;
                }catch (Exception $exception){}
            }
        }catch (Exception $exception){}
    }

    public function fromHexFormat(String $hexValue): void
    {
        if($hexValue != null) {
            $point = $this->curve->getGenerator()->multiply(gmp_init($hexValue, 16));

            $ecPoint = new ECpoint($point->toAffine(), $this->curve);

            $privateKey = new PrivateKey($hexValue, $this->curve, $this->algorithm);

            $publicKey = new PublicKey($ecPoint, $this->algorithm);

            $this->privateKey = $privateKey;
            $this->publicKey = $publicKey;
        }
    }
    public function setPublicKey(PublicKey $publicKey): void
    {
        $this->publicKey = $publicKey;
    }

    public function setPrivateKey(PrivateKey $privateKey): void
    {
        $this->privateKey = $privateKey;
    }

    public function getKID() : String {
        return $this->getKID();
    }
}
?>