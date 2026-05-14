<?php

namespace Amreljako\SecureCore\Security;

use Hashids\Hashids;

class SecureId
{
    protected $hashids;

    public function __construct()
    {
        $this->hashids = new Hashids(config('app.key'), 10);
    }

    public function encode($id)
    {
        return $this->hashids->encode($id);
    }

    public function decode($hash)
    {
        $result = $this->hashids->decode($hash);
        return isset($result[0]) ? $result[0] : null;
    }
}