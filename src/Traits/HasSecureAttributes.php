<?php

namespace Amreljako\SecureCore\Traits;

use Illuminate\Support\Facades\Crypt;

trait HasSecureAttributes
{

    protected static function bootHasSecureAttributes()
    {
        if (! config('secure-core.encryption.enabled', true)) {
        return;
        }
        static::saving(function ($model) {
            foreach ($model->getEncryptableAttributes() as $attribute) {
                if ($model->{$attribute}) {
                    $model->{$attribute} = Crypt::encryptString($model->{$attribute});
                }
            }
        });
    }

    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->getEncryptableAttributes()) && $value) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return $value; 
            }
        }

        return $value;
    }

    protected function getEncryptableAttributes(): array
    {
        return property_exists($this, 'encryptable') ? $this->encryptable : [];
    }

    public function __debugInfo()
    {
        if (! config('secure-core.encryption.enabled', true)) {
        return;
        }
        $attributes = parent::toArray(); 

        foreach ($this->getEncryptableAttributes() as $attribute) {
            if (isset($attributes[$attribute])) {
                $attributes[$attribute] = '******** (Encrypted)';
            }
        }

        return $attributes;
    }
}