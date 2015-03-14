<?php
/**
 * A set of methods to provide basic calendar functions beyond
 * the normal datetime.
 * 
 * @author Nicolas Colbert <support@foonster.com>
 * @copyright 2002 Foonster Technology
 * 
 */
class Calendar extends \DateTime
{

    private $vars = array();    
    private $year;
    private $time;
    public $error;

    /**
     * class constructor
     * 
     * @param integer $year [what year to load]
     */
    public function __construct($year = null)
    {                
        empty($year) ? $this->year = date('Y') : $this->year = $year;        
        !is_numeric($this->year) ? $this->year = date('Y') : false;    
        $this->time = strtotime('1/1/' . $this->year); 
        self::loadCalendar();
    }

    /**
     * @ignore
     */
    public function __destruct()
    {


    }

    /**
     * @ignore
     */
    public function __get($index)
    {
        return $this->vars[ $index ];
    }

    /**
     * @ignore
     */
    public function __set($index, $value)
    {
        $this->vars[$index] = $value;
    }

    /**
     * build an asosociative array containing the days and weeks. 
     * additionally, this does NOT use ISO 8601 to make the weeks.
     * 
     * @param  integer $month [number representing the month]
     * @param  integer $year  [number representing the year]
     * @return array
     */
    public function fillByWeek($month, $year) 
    {
        
        $days = array();
        $weeks = array();
        $date = new \stdClass();

        $date->month = strtotime( $month . '/1/' . $year);
        $date->previous = strtotime("-1 month", $date->month);
        $date->next = strtotime("+1 month", $date->month);
        $dayOfWeek = date('w', $date->month);        
        
        if ($dayOfWeek > 0) {
            $n = date('t', $date->previous);
            for ($i = $dayOfWeek; $i > 0; $i--) {   
                $days[] = date('m', $date->previous) . '/' . $n . '/' . date('Y', $date->previous);
                $n--;    
            }
            $days = array_reverse($days);
        }
        
        for ($i = 1; $i <= date('t', $date->month); $i++) {         
            $days[] = date('m', $date->month) . '/' . $i . '/' . date('Y', $date->month);
        }

        $dayOfWeek = date('w', strtotime("-1 day", $date->next));
        if (date('w', strtotime("-1 day", $date->next)) < 6) {
            $n = 1;
            for ($i = date('w', strtotime("-1 day", $date->next)); $i < 6; $i++) {  
                $days[] = date('m', $date->next) . '/' . $n . '/' . date('Y', $date->next);
                $n++;
            }
        }

        // the calendar is built on a non-ISO 8601 - because Sunday is part of the last
        // week.
        $n = 0;
        $count = 0;
        foreach ($days as $day) {
            $count++;
            if ($count <= 7) {
                $weeks[$n][] = $day;    
            } else {
                $n++;                
                $count = 1;
                $weeks[$n][] = $day;    
            }
        }

        return $weeks;
    }

    /**
     * fill array with what would appear on a calendar page.
     * 
     * @param  integer $month [month]
     * @param  integer $year  [year]
     * @return array
     */
    public function fillDays($month, $year) 
    {
        
        $days = array();
        $date = new \stdClass();

        $date->month = strtotime( $month . '/1/' . $year);
        
        for ($i = 1; $i <= date('t', $date->month); $i++) {         
            $days[] = date('m', $date->month) . '/' . $i . '/' . date('Y', $date->month);
        }
        
        return $days;
    }

    /**
     * @ignore
     */
    public function loadCalendar()
    {
        for ($i = 1; $i <= 12; $i++) {   
            $month = self::getMonth($i, $this->year);
            $this->{strtolower($month->long_name)} = $month;        
        }
    }

    /**
     * generate general information about requested month.
     * 
     * @param  integer $month [month]
     * @param  integer $year  [year]
     * @param  boolean $parent [true, will build information about next/previous months as well]
     * @return object
     */
    public function getMonth($month = null, $year = null, $parent = true) 
    {
        is_numeric($month) ? $time = strtotime($month . '/1/' . $year) : $time = strtotime($month . ' 1st, ' . $year);
        $month = array();
        $month['long_name'] = date('F', $time);
        $month['long_number'] = date('m', $time);
        $month['short_name'] = date('F', $time);
        $month['short_number'] = date('n', $time);
        $month['number_of_days'] = date('t', $time);
        $month['year'] = date('Y', $time);
        $month['days'] = self::fillDays($month['short_number'], $year);
        $month['byweek'] = self::fillByWeek($month['short_number'], $year);
        if ($parent) {            
            $previous = strtotime("-1 month", $time);
            $next = strtotime("+1 month", $time);
            $month['previous'] = self::getMonth(date('m', $previous), date('Y', $previous), false);
            $month['next'] = self::getMonth(date('m', $next), date('Y', $next), false);
        }
        return (object) $month;
    }

    /**
     * 
     * return a text string representing the season based on the provided
     * month.
     * 
     * @param string/intger 
     * 
     * @return string
     * 
     */ 
    public function season($string)
    {

        !is_numeric($string) = $string= date('n', strtotime($string . '18th, 1970')) : false;

        $month = intval($string);

        $seasons = array( 
            1 => 'Winter',
            2 => 'Winter',
            3 => 'Spring',
            4 => 'Spring',
            5 => 'Spring',
            6 => 'Summer',
            7 => 'Summer',
            8 => 'Summer',
            9 => 'Fall',
            10 => 'Fall',
            11 => 'Fall',
            12 => 'Winter');

        return $seasons[$month];
    }

    /**
     * change the year assgined to the class, there is no return value but the method
     * will run the loadCalendar function.
     * 
     * @see loadCalendar()
     * 
     * @param integer $year [year]     
     */
    public function setYear($year)
    {
        empty($year) ? $this->year(date('Y')) : $this->year = $year;
        !is_numeric($this->year) ? $this->year = date('Y') : $this->year = $year;    
        self::loadCalendar();

    }

}
