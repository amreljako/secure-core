<?php

namespace Amreljako\SecureCore\Facades;

use Illuminate\Support\Facades\Facade;

class SecureCore extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'secure-core';
    }
}