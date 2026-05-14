<?php

namespace Amreljako\SecureCore\Traits;

use Amreljako\SecureCore\Security\SecureId;

trait SecureResource
{
    /**
     * Get the value of the model's route key.
     * Encodes the ID using Hashids.
     */
    public function getRouteKey()
    {
        return app(SecureId::class)->encode($this->getKey());
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $decoded = app(SecureId::class)->decode($value);
        
        return parent::resolveRouteBinding($decoded ?? $value, $field);
    }
}