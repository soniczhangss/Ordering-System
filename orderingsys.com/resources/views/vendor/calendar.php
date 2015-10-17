<?php
/**
 * Calendar Generation Class
 *
 * This class provides a simple reuasable means to produce month calendars in valid html
 *
 * @version 2.8
 * @author Jim Mayes <jim.mayes@gmail.com>
 * @link http://style-vs-substance.com
 * @copyright Copyright (c) 2008, Jim Mayes
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt GPL v2.0
 *         
 */
class Calendar {
  var $date;
  var $year;
  var $month;
  var $day;
  var $orders;
  var $ordered;
  var $week_start_on = FALSE;
  var $week_start = 1;// monday
  
  var $link_days = TRUE;
  var $link_to;
  var $formatted_link_to;
  
  var $mark_today = TRUE;
  var $today_date_class = 'today';
  
  var $mark_selected = TRUE;
  var $selected_date_class = 'selected';
  
  var $mark_passed = TRUE;
  var $passed_date_class = 'passed';
  
  var $highlighted_dates;
  var $default_highlighted_class = 'highlighted';
  
  
  /* CONSTRUCTOR */
  function Calendar($date = NULL, $year = NULL, $month = NULL, $orders = NULL){
    $self = htmlspecialchars($_SERVER['PHP_SELF']);
    $this->link_to = $self;
    
    if( is_null($year) || is_null($month) ){
      if( !is_null($date) ){
        //-------- strtotime the submitted date to ensure correct format
        $this->date = date("Y-m-d", strtotime($date));
      } else {
        //-------------------------- no date submitted, use today's date
        $this->date = date("Y-m-d");
      }
      $this->set_date_parts_from_date($this->date);
    } else {
      $this->year   = $year;
      $this->month  = str_pad($month, 2, '0', STR_PAD_LEFT);
    }

    if (!is_null($orders)) {
      $this->orders = $orders;
    }
  }

  function formated_date($date) {
    $dt = \Carbon\Carbon::parse($date);
    $carbon = \Carbon\Carbon::instance($dt);
    return $carbon->toDateString();
  }

  function ordered($date_from_user) {
    $date_from_user = new Carbon\Carbon($this->formated_date($date_from_user));
    foreach ($this->orders as $order) {
      $from = new Carbon\Carbon($this->formated_date($order->from));
      $to = new Carbon\Carbon($this->formated_date($order->to));
      if ($date_from_user->between($from, $to)) {
        return "ordered";
      }
    }
    // foreach ($this->orders as $booking) {
    //   if ($this->check_in_range($booking->from, $booking->to, $date_from_user)) {
    //     return "ordered";
    //   }
    // }
  }

  // function check_in_range($start_date, $end_date, $date_from_user)
  // {
  //   // Convert to timestamp
  //   $start_ts = strtotime($start_date);
  //   $end_ts = strtotime($end_date);
  //   $user_ts = strtotime($date_from_user);

  //   // Check that user date is between start & end
  //   return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
  // }
  
  function set_date_parts_from_date($date){
    $this->year   = date("Y", strtotime($date));
    $this->month  = date("m", strtotime($date));
    $this->day    = date("d", strtotime($date));
  }
  
  function day_of_week($date){
    $day_of_week = date("N", $date);
    if( !is_numeric($day_of_week) ){
      $day_of_week = date("w", $date);
      if( $day_of_week == 0 ){
        $day_of_week = 7;
      }
    }
    return $day_of_week;
  }
  
  function output_calendar($year = NULL, $month = NULL, $calendar_class = 'calendar'){

    if( $this->week_start_on !== FALSE ){
      echo "The property week_start_on is replaced due to a bug present in version before 2.6. of this class! Use the property week_start instead!";
      exit;
    }
    
    //--------------------- override class methods if values passed directly
    $year = ( is_null($year) )? $this->year : $year;
    $month = ( is_null($month) )? $this->month : str_pad($month, 2, '0', STR_PAD_LEFT);

    //------------------------------------------- create first date of month
    $month_start_date = strtotime($year . "-" . $month . "-01");
    //------------------------- first day of month falls on what day of week
    $first_day_falls_on = $this->day_of_week($month_start_date);
    //----------------------------------------- find number of days in month
    $days_in_month = date("t", $month_start_date);
    //-------------------------------------------- create last date of month
    $month_end_date = strtotime($year . "-" . $month . "-" . $days_in_month);
    //----------------------- calc offset to find number of cells to prepend
    $start_week_offset = $first_day_falls_on - $this->week_start;
    $prepend = ( $start_week_offset < 0 )? 7 - abs($start_week_offset) : $first_day_falls_on - $this->week_start;
    //-------------------------- last day of month falls on what day of week
    $last_day_falls_on = $this->day_of_week($month_end_date);

    //------------------------------------------------- start table, caption
    $output  = "<ul class=\"" . $calendar_class . "\">\n";
    // $output  = "<table class=\"" . $calendar_class . "\">\n";
    $output .= "<li class=\"my-day-header\">" . ucfirst(strftime("%B %Y", $month_start_date)) . "</li>\n";
    //$output .= "<caption>" . ucfirst(strftime("%B %Y", $month_start_date)) . "</caption>\n";
    
    //$col = '';
    $th = '';

    $output .= "<li class=\"day-header\">";
    for( $i=1,$j=$this->week_start,$t=(3+$this->week_start)*86400; $i<=7; $i++,$j++,$t+=86400 ) {
      $localized_day_name = gmstrftime('%A',$t);
      //$col .= "<col class=\"" . strtolower($localized_day_name) ."\" />\n";
      $th = "<span class=\"show-for-medium-up\">" . strtoupper($localized_day_name) ."</span>\n";
      $output .= "<div class=\"small-1 medium-1 large-1 day\">\n";
      $output .= $th;
      $output .= "</div>\n";
      //$th .= "\t<th title=\"" . ucfirst($localized_day_name) ."\">" . strtoupper($localized_day_name{0}) ."</th>\n";
      $j = ( $j == 7 )? 0 : $j;
    }

    $output .= "</li>";
    //------------------------------------------------------- markup columns
    //$output .= $col;
    
    //----------------------------------------------------------- table head
    //$output .= "<thead>\n";
    //$output .= "<div class=\"small-1 medium-1 large-1 day\">\n";
    //$output .= "<tr>\n";
    
    //$output .= $th;
    
    //$output .= "</div>\n";
    
    //---------------------------------------------------------- start tbody
    $output .= "<li class=\"week\">\n";
    //$output .= "<tbody>\n";
    //$output .= "<tr>\n";
    
    //---------------------------------------------- initialize week counter
    $weeks = 1;
    
    //--------------------------------------------------- pad start of month
    
    //------------------------------------ adjust for week start on saturday
    $carbon = new Carbon\Carbon('last day of ' . $year. '-' . ($month-1));
    $title_format = (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')? "%A, %B %#d, %Y": "%A, %B %e, %Y";

    for($i=1,$daylm=$carbon->day - $prepend + 1;$i<=$prepend;$i++,$daylm++) {
      $day_date = $carbon->year . "-" . $carbon->month . "-" . $daylm;
      $ordered = $this->ordered(strftime($title_format, strtotime($day_date)));
      $output .= "<div class=\"small-1 medium-1 large-1 day $ordered previous-month\" title=\"" . ucwords(strftime($title_format, strtotime($day_date))) . "\">". $daylm . "</div>\n";
      //$output .= "\t<td class=\"pad\">&nbsp;</td>\n";
    }
    
    //--------------------------------------------------- loop days of month
    for($day=1,$cell=$prepend+1; $day<=$days_in_month; $day++,$cell++) {

      /*
      if this is first cell and not also the first day, end previous row
      */
      if( $cell == 1 && $day != 1 ) {
        $output .= "<li class=\"week\">\n";
        //$output .= "<tr>\n";
      }
      
      //-------------- zero pad day and create date string for comparisons
      $day = str_pad($day, 2, '0', STR_PAD_LEFT);
      $day_date = $year . "-" . $month . "-" . $day;
      
      //-------------------------- compare day and add classes for matches
      // if( $this->mark_today == TRUE && $day_date == date("Y-m-d") ){
      //   $classes[] = $this->today_date_class;
      // }
      
      // if( $this->mark_selected == TRUE && $day_date == $this->date ){
      //   $classes[] = $this->selected_date_class;
      // }
      
      // if( $this->mark_passed == TRUE && $day_date < date("Y-m-d") ){
      //   $classes[] = $this->passed_date_class;
      // }
      
      // if( is_array($this->highlighted_dates) ){
      //   if( in_array($day_date, $this->highlighted_dates) ){
      //     $classes[] = $this->default_highlighted_class;
      //   }
      // }
      
      //----------------- loop matching class conditions, format as string
      /* if( isset($classes) ){
        $day_class = ' class="';
        foreach( $classes AS $value ){
          $day_class .= $value . " ";
        }
        $day_class = substr($day_class, 0, -1) . '"';
      } else {
        $day_class = '';
      } */
      
      //---------------------------------- start table cell, apply classes
      // detect windows os and substitute for unsupported day of month modifer
      $title_format = (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')? "%A, %B %#d, %Y": "%A, %B %e, %Y";
      $ordered = $this->ordered(strftime($title_format, strtotime($day_date)));
      $output .= "<div class=\"small-1 medium-1 large-1 day $ordered \" title=\"" . ucwords(strftime($title_format, strtotime($day_date))) . "\">";
      //$output .= "\t<td" . $day_class . " title=\"" . ucwords(strftime($title_format, strtotime($day_date))) . "\">";
      
      //----------------------------------------- unset to keep loop clean
      //unset($day_class, $classes);
      
      //-------------------------------------- conditional, start link tag 
      switch( $this->link_days ){
        case 0 :
        $output .= $day;
        break;
        
        case 1 :
        if( empty($this->formatted_link_to) ){
          $output .= "<a href=\"" . $this->link_to . "?date=" . $day_date . "\">" . $day . "</a>";
        } else {
          $output .= "<a href=\"" . strftime($this->formatted_link_to, strtotime($day_date)) . "\">" . $day . "</a>";
        }
        break;
        
        case 2 :
        if( is_array($this->highlighted_dates) ){
          if( in_array($day_date, $this->highlighted_dates) ){
            if( empty($this->formatted_link_to) ){
              $output .= "<a href=\"" . $this->link_to . "?date=" . $day_date . "\">";
            } else {
              $output .= "<a href=\"" . strftime($this->formatted_link_to, strtotime($day_date)) . "\">";
            }
          }
        }

        $output .= $day;

        if( is_array($this->highlighted_dates) ){
          if( in_array($day_date, $this->highlighted_dates) ){
            if( empty($this->formatted_link_to) ){
              $output .= "</a>";
            } else {
              $output .= "</a>";
            }
          }
        }
        break;
      }
      
      //------------------------------------------------- close table cell
      $output .= "</div>\n";
      //$output .= "</td>\n";
      
      //------- if this is the last cell, end the row and reset cell count
      if( $cell == 7 ){
        $output .= "</li>\n";
        //$output .= "</tr>\n";
        $cell = 0;
      }
      
    }
    
    //----------------------------------------------------- pad end of month
    if( $cell > 1 ){
      $carbon = NULL;
      if ($month+1 > 12) {
        $carbon = new Carbon\Carbon('first day of ' . ($year+1). '-' . ($month+1)%12);
      } else {
        $carbon = new Carbon\Carbon('first day of ' . $year. '-' . ($month+1));
      }
      
      //$carbon = new Carbon\Carbon('first day of next month');
      $title_format = (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')? "%A, %B %#d, %Y": "%A, %B %e, %Y";
      for($i=$cell,$daynm=1;$i<=7;$i++,$daynm++){
        $daynm = str_pad($daynm, 2, '0', STR_PAD_LEFT);
        $day_date = $carbon->year . "-" . $carbon->month . "-" . $daynm;
        $ordered = $this->ordered(strftime($title_format, strtotime($day_date)));
        $output .= "<div class=\"small-1 medium-1 large-1 day $ordered next-month\" title=\"" . ucwords(strftime($title_format, strtotime($day_date))) . "\">". $daynm . "</div>\n";
        //$output .= "\t<td class=\"pad\">&nbsp;</td>\n";
      }
      $output .= "</li>\n";
      //$output .= "</tr>\n";
    }
    
    //--------------------------------------------- close last row and table
    //$output .= "</tbody>\n";
    $output .= "</ul>\n";
    //$output .= "</table>\n";
    
    //--------------------------------------------------------------- return
    return $output;
    
  }
  
}
?>