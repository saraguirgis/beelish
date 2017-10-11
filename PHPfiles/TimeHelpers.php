<?php
/**
 * A class containing Time helpers.
 */
class TimeHelpers {
    
    // Weekday values for datetime object
    const Sunday    = 0;
    const Monday    = 1;
    const Tuesday   = 2;
    const Wednesday = 3;
    const Thursday  = 4;
    const Friday    = 5;
    const Saturday  = 6;
    
    /**
     * Returns true if supplied datetime falls on a holiday.
     * @param array $holidayList Array containing list of holidays
     * @param datetime $date The date to be checked as a unix timestamp
     * @return True if date supplied falls on a holiday
     */
    public static function isHoliday($holidayList, $date) {

        foreach($holidayList as $holiday){
            $holidayTimestamp = strtotime($holiday);

            if ($holidayTimestamp == $date) {
                // found a match on a given holiday
                return true;
            }
        }

        // went through all holidays and didn't find a match
        return false;
    }

    /**
     * Returns true if supplied datetime falls on a weekend.
     * @param datetime $date The date to be checked as a unix timestamp
     * @return True if date supplied falls on a weekend
     */
    public static function isWeekend($date) {
        if (date('w', $date) == TimeHelpers::Saturday || date('w', $date) == TimeHelpers::Sunday) {
            return true;
        }

        return false;
    }
}
?>
