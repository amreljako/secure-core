<?php

namespace Amreljako\SecureCore\Traits;

use Illuminate\Support\Facades\Crypt;

trait HasSecureAttributes
{
    protected static function bootHasSecureAttributes()
    {
        static::saving(function ($model) {
            foreach ($model->getEncryptableAttributes() as $attribute) {
                $value = $model->attributes[$attribute] ?? null;

                if ($value && !static::isAlreadyEncrypted($value)) {
                    $model->attributes[$attribute] = Crypt::encryptString($value);
                }
            }
        });
    }

    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->getEncryptableAttributes()) && !empty($value)) {
            if (static::isAlreadyEncrypted($value)) {
                try {
                    return Crypt::decryptString($value);
                } catch (\Exception $e) {
                    return $value; 
                }
            }
        }

        return $value;
    }


    protected static function isAlreadyEncrypted($value): bool
    {
        if (!is_string($value)) return false;

        return str_starts_with($value, 'eyJpdiI6');
    }

    public function getEncryptableAttributes(): array
    {
        return property_exists($this, 'encryptable') ? $this->encryptable : [];
    }
}