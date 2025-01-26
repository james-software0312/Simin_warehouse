<?php
/**
 * Determines if setup is complete or not
 */

 function setupStatus()
 {
     try {
         $checkComplete = \App\Models\ConfigurationModel::where('config', 'setup_complete')->first();
         if (!$checkComplete) {
             return false;
         }
         if ($checkComplete['value'] === '0') {
             return false;
         }
         return true;
     } catch (Exception $e) {
         return false;
     }
 }
 
if (!function_exists('format_price')) {
    function format_price($price)
    {
        return number_format($price, 2, '.', ',') ;
    }
}
if (!function_exists('format_ref')) {
    function format_ref($ref) {
        // Use substr() to extract parts of the string
        $prefix = substr($ref, 0, 2);  // "WZ"
        $day = substr($ref, 2, 2);      // "08"
        $month = substr($ref, 4, 2);    // "10"
        $year = substr($ref, 6, 2);     // "24"
        $number = substr($ref, 8, 3);    // "001"

        // Concatenate the formatted parts
        return "{$prefix}/{$day}/{$month}/{$year}/{$number}";
    }
}

if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
