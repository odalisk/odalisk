<?php

namespace Odalisk\Scraper\Tools\Normalize;

class DateNormalizer
{
    /**
     * Match some regex to known date formats. Order is IMPORTANT!
     *
     * @var string
     */
    protected $correctDates = array(
        '/^[0-9]{4}(.[0-9]{1,2}(.[0-9]{1,2}( [0-9]{2}(:[0-9]{2}(:[0-9]{2})?)?)?)?)?$/' =>  array(
            '!Y',
            '!Y*m',
            '!Y*m*d',
            '!Y*m*d H',
            '!Y*m*d H:i',
            '!Y*m*d H:i:s',
        ),
        '/^(([0-9]{1,2}.)?[0-9]{1,2}.)?[0-9]{4}( [0-9]{2}(:[0-9]{2}(:[0-9]{2})?)?)?$/' => array(
            '!Y',
            '!m*Y',
            '!d*m*Y',
            '!d*m*Y H',
            '!d*m*Y H:i',
            '!d*m*Y H:i:s',
        ),
        '/^(([0-9]{1,2}.)?[0-9]{1,2}.)?[0-9]{2}( [0-9]{2}(:[0-9]{2}(:[0-9]{2})?)?)?$/' => array(
            '!y',
            '!m*y',
            '!d*m*y',
            '!d*m*y H',
            '!d*m*y H:i',
            '!d*m*y H:i:s',
        ),
        '/^(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec) ([0-9]{1,2}[^0-9]+)?[0-9]{4}$/' => array(
            '!M*Y',
            '!M*d?*Y',
        ),
        '/^([0-9]{1,2}.)?(?:January|February|March|April|May|June|July|August|September|October|November|December).[0-9]{4}$/' => array(
            '!F?Y',
            '!d?F?Y',
        ),
        '/^([0-9]{1,2}.)?(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec).[0-9]{2}$/' => array(
            '!M?y',
            '!d?M?y',
        ),
        '/^([0-9]{1,2}.)?(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec).[0-9]{4}?$/' => array(
            '!M?Y',
            '!d?M?Y',
        ),
    );
    
    /**
     * Values for date fields that are considered equivalent to empty
     *
     * @var array $emptyDates
     */
    protected $emptyDates = array('N/A', 'n/a', 'TBC', 'not known', '');
    
    /**
     * The date type fields we need to process so we can transform them into DateTime objects
     *
     * @var array $dateFields
     */
    protected $dateFields = array('setReleasedOn', 'setLastUpdatedOn');
    
    
    public function normalize(array &$data) {
        // We transform dates strings in datetime.
        foreach ($this->dateFields as $field) {
            if (array_key_exists($field, $data)) {
                $date = $data[$field];
                // Try to match the date against something we know
                foreach($this->correctDates as $regex => $formats) {
                    // Check if we have a match
                    if(preg_match($regex, $date, $m)) {
                        // Depending on how many matches we have, we know which format to pick
                        $data[$field] = \Datetime::createFromFormat($formats[count($m)-1], $date)->format("d-m-Y H:i");
                        if(false === $data[$field]) {
                            error_log('[>>> False positive ] ' . $date . ' with ' . $regex . ' (count = ' . (count($m)-1) .')');
                            $data[$field] = null;
                        }
                        // Check out the next field directly
                        continue 2;
                    }
                }
                // This is executed only if we have no match
                // Check if it is a known empty-ish value
                if(in_array($date, $this->emptyDates)) {
                    $data[$field] = null;
                } else {
                    // Not something we recognize
                    error_log('[Unknown date format ] ' . $date);
                    $data[$field] = $date;
                }
                $date = null;
            }
        }
    }
}