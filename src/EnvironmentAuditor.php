<?php

namespace Amreljako\SecureCore;

class EnvironmentAuditor
{
    public function getSensitiveEnvKeys(array $customFields = []): array
    {
        $keywords = array_merge(
            ['KEY', 'PASSWORD', 'SECRET', 'TOKEN', 'DATABASE', 'DB_', 'MAIL_', 'STRIPE_', 'AWS_'], 
            $customFields
        );

        $maskedKeys = [];
        foreach ($keywords as $keyword) {
            foreach ([$_ENV, $_SERVER] as $globalArray) {
                foreach ($globalArray as $key => $value) {
                    if (str_contains(strtoupper($key), strtoupper($keyword))) {
                        $maskedKeys[] = $key;
                    }
                }
            }
        }

        return array_unique($maskedKeys);
    }
}