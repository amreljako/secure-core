<?php

namespace Amreljako\SecureCore\Logging;

class MaskSensitiveData
{
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->pushProcessor(function ($record) {
                $maskedFields = config('secure-core.logging.masked_fields', []);
                
                foreach ($maskedFields as $field) {
                    if (isset($record['context'][$field])) {
                        $record['context'][$field] = '********';
                    }
                }
                return $record;
            });
        }
    }
}