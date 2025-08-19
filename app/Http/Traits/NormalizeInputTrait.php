<?php

namespace App\Http\Traits;

trait NormalizeInputTrait
{
    /**
     * Normalize carrier name to title case
     */
    protected function normalizeCarrierName($name)
    {
        if (!$name) return $name;
        
        // Convert to title case, but handle special cases
        $name = trim($name);
        
        // Split on spaces and capitalize each word
        $words = explode(' ', $name);
        $normalized = [];
        
        foreach ($words as $word) {
            if (empty($word)) continue;
            
            // Handle common abbreviations and special cases
            $upperWord = strtoupper($word);
            if (in_array($upperWord, ['LTD', 'LLC', 'INC', 'CORP', 'CO', 'UK', 'PLC', 'LP', 'LLP'])) {
                $normalized[] = $upperWord;
            } else {
                // Standard title case
                $normalized[] = ucfirst(strtolower($word));
            }
        }
        
        return implode(' ', $normalized);
    }

    /**
     * Normalize vehicle registration to uppercase
     */
    protected function normalizeVehicleRegistration($registration)
    {
        if (!$registration) return $registration;
        
        // Remove extra spaces and convert to uppercase
        return strtoupper(trim(preg_replace('/\s+/', ' ', $registration)));
    }

    /**
     * Normalize container number to uppercase
     */
    protected function normalizeContainerNumber($containerNumber)
    {
        if (!$containerNumber) return $containerNumber;
        
        // Remove extra spaces and convert to uppercase
        return strtoupper(trim(preg_replace('/\s+/', ' ', $containerNumber)));
    }
}