<?php
    $f = '/Users/bowbaq/Dropbox/Projet/odalisk/categories/raw';
    $raw_categories_array = json_decode(file_get_contents($f));
    
    function _trim($value) {
        return trim($value, " \t\n\r\0\x0B\"'[]()&.");
    }
    
    $replace = array(
        '/\b(and|et)\b/' => '&',
        '/^(Not Av|none|\s+|)$/' => 'N/A',
    );
    
    // Cleanup
    $result = "";
    
    foreach ($raw_categories_array as $raw_categories) {
        // Extract clean categories
                $categories = preg_split('/(,|-|;|\/|\|_)/', $raw_categories);
                foreach ($categories as &$category) {
                    $category = _trim($category);
                    foreach ($replace as $bad => $good) {
                        $category = preg_replace($bad, $good, $category);
                    }
                    $category = _trim($category);
                }
                
                $categories = array_filter($categories);        
        $result .= implode("\n", array_filter($categories)) . "\n";
    }
    
    file_put_contents('/Users/bowbaq/Dropbox/Projet/odalisk/categories/proc', $result);
?>