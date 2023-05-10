<?php

namespace ECDSA\keys;

interface KeyInterface{
    public function getKeyType() : KeyTypes;
}