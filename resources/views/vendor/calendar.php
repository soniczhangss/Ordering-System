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

  var $location_capacity;
  
  
  /* CONSTRUCTOR */
  function Calendar($date = NULL, $year = NULL, $month = NULL, $orders = NULL, $location_capacity = NULL){
    $self = htmlspecialchars($_SERVER['PHP_SELF']);
    $this->link_to = $self;
    
    if( is_null($year) || is_null($month) ){
      if( !is_null($date) ){
        //-------- strtotime the submitted date to ensure correct format
        $this->date = date("d-m-Y", strtotime($date));
      } else {
        //-------------------------- no date submitted, use today's date
        $this->date = date("d-m-Y");
      }
      $this->set_date_parts_from_date($this->date);
    } else {
      $this->year   = $year;
      $this->month  = str_pad($month, 2, '0', STR_PAD_LEFT);
    }

    if (!is_null($orders)) {
      $this->orders = $orders;
    }

    if (!is_null($location_capacity)) {
      $this->location_capacity = $location_capacity;
    }
  }

  function formated_date($date) {
    $dt = \Carbon\Carbon::parse($date);
    $carbon = \Carbon\Carbon::instance($dt);
    return $carbon->toDateString();
  }

  function ordered($date_from_user) {
    $arr = [];
    $date_from_user = new Carbon\Carbon($this->formated_date($date_from_user));
    if (!is_null($this->orders)) {
      foreach ($this->orders as $order) {
        $from = new Carbon\Carbon($this->formated_date($order->from));
        $to = new Carbon\Carbon($this->formated_date($order->to));
        if ($date_from_user->between($from, $to)) {
          //$order = \App\Order::where('from', $from)->where('to', $to)->first();
          $user = \App\User::findOrFail($order->user_id);
          $name = $user->name;
          $company = \App\Company::findOrFail($user->company_id);
          $updated_time = $order->updated_at;
          // return array('name'=>$name, 'updated_at'=>$updated_time->toDateTimeString(), 'company'=>$company->name);
          array_push($arr, array('name'=>$name, 'updated_at'=>$updated_time->toDateTimeString(), 'company'=>$company->name));
        }
      }
    }
    return $arr;
    //print_r($arr);
    //echo count($arr);
  }
  
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

  /**
     * Get the role of the user.
     *
     * @param  array  $data
     * @return User
     */
    function myRole() {
        $user = \Auth::user();
        $role = \App\Role::findOrFail($user['role_id']);
        return $role['name'];
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

    $output .= "<li class=\"day-header\"><div class=\"small-1 medium-1 large-1 day\"><span class=\"show-for-medium-up\">wk</span></div>";
    for( $i=1,$j=$this->week_start,$t=(3+$this->week_start)*86400; $i<=7; $i++,$j++,$t+=86400 ) {
      $localized_day_name = gmstrftime('%a',$t);
      switch ($localized_day_name) {
        case 'Mon':
        $localized_day_name = "M";
        break;

        case 'Tue':
        $localized_day_name = "T";
        break;

        case 'Wed':
        $localized_day_name = "W";
        break;

        case 'Thu':
        $localized_day_name = "T";
        break;

        case 'Fri':
        $localized_day_name = "F";
        break;

        case 'Sat':
        $localized_day_name = "S";
        break;

        case 'Sun':
        $localized_day_name = "S";
        break;
        
        default:
          break;
      }
      //$col .= "<col class=\"" . strtolower($localized_day_name) ."\" />\n";
      $th = "<span class=\"show-for-medium-up\">" . $localized_day_name ."</span>\n";
      $output .= "<div class=\"small-1 medium-1 large-1 day\">\n";
      $output .= $th;
      $output .= "</div>\n";
      //$th .= "\t<th date=\"" . ucfirst($localized_day_name) ."\">" . strtoupper($localized_day_name{0}) ."</th>\n";
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
    /*$daylm=$carbon->day - $prepend + 1;
    $day_date = $carbon->year . "-" . $carbon->month . "-" . $daylm;

    $output .= "<div class=\"week-num\">$weekNum</div><li class=\"week\">\n";*/
    //$output .= "<tbody>\n";
    //$output .= "<tr>\n";
    
    //---------------------------------------------- initialize week counter
    $weeks = 1;
    
    //--------------------------------------------------- pad start of month
    
    //------------------------------------ adjust for week start on saturday
    $carbon = new Carbon\Carbon('last day of ' . $year. '-' . ($month-1));
    $title_format = (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')? "%A, %B %#d, %Y": "%A, %B %e, %Y";

    $wn_carbon = new Carbon\Carbon('first day of ' . $year. '-' . $month);
    $wn_day_date = $wn_carbon->year . "-" . $wn_carbon->month . "-" . $wn_carbon->day;
    $weekNum = \Carbon\Carbon::parse($wn_day_date)->format('W');
    $output .= "<div class=\"week-num\">$weekNum</div><li class=\"week\">\n";

    for($i=1,$daylm=$carbon->day - $prepend + 1;$i<=$prepend;$i++,$daylm++) {
      $day_date = $carbon->year . "-" . $carbon->month . "-" . $daylm;
      /*if ($i==1) {
        $weekNum = \Carbon\Carbon::parse($day_date)->format('W');
        $output .= "<div class=\"week-num\">$weekNum</div>";
      }*/
      $ordered = $this->ordered(strftime($title_format, strtotime($day_date)));
      $title = '';
      $myRole = $this->myRole();
      if (count($ordered) == $this->location_capacity) {
        if ($myRole == 'staff') {
          // show the order is placed by which colleague
          // e.g. Ordered by Xulong Zhang at 01:01:07 09-09-2015
          $title = "Ordered by " . array_get($ordered[0], 'name') . " at " . \Carbon\Carbon::parse(array_get($ordered[0], 'updated_at'))->format('H:i:s d-m-Y');
          $ordered = 'ordered';
        } elseif ($myRole == 'admin' || $myRole == 'root') {
          // show the order is placed by which colleague of which company
          // e.g. Ordered by Xulong Zhang from Netbay Wifi at 01:01:07 09-09-2015
          for ($j=0; $j < count($ordered); $j++) {
            if ($title != '') {
              $title .= "\n";
            }
            $title .= "\nOrdered by " . array_get($ordered[$j], 'name') . " from " . array_get($ordered[$j], 'company') . " at " . \Carbon\Carbon::parse(array_get($ordered[$j], 'updated_at'))->format('H:i:s d-m-Y');
          }
          $ordered = 'ordered';
        }
        $title = "title=\"$title\"";
      } elseif (count($ordered) > 0) {
        if ($myRole == 'staff') {
          // show the order is placed by which colleague
          // e.g. Ordered by Xulong Zhang at 01:01:07 09-09-2015
          $title = "Ordered by " . array_get($ordered[0], 'name') . " at " . \Carbon\Carbon::parse(array_get($ordered[0], 'updated_at'))->format('H:i:s d-m-Y');
          $ordered = 'available';
        } elseif ($myRole == 'admin' || $myRole == 'root') {
          // show the order is placed by which colleague of which company
          // e.g. Ordered by Xulong Zhang from Netbay Wifi at 01:01:07 09-09-2015
          for ($j=0; $j < count($ordered); $j++) {
            if ($title != '') {
              $title .= "\n";
            }
            $title .= "\nOrdered by " . array_get($ordered[$j], 'name') . " from " . array_get($ordered[$j], 'company') . " at " . \Carbon\Carbon::parse(array_get($ordered[$j], 'updated_at'))->format('H:i:s d-m-Y');
          }
          $ordered = 'available';
        }
        $title = "title=\"$title\"";
        //$ordered = 'available';
      } else {
        $ordered = '';
      }
      $output .= "<div class=\"small-1 medium-1 large-1 day $ordered previous-month\" date=\"" . preg_replace('/\s+/', ' ', ucwords(strftime($title_format, strtotime($day_date)))) . "\" $title>". $daylm . "</div>\n";
      //$output .= "\t<td class=\"pad\">&nbsp;</td>\n";
    }
    
    //--------------------------------------------------- loop days of month
    for($day=1,$cell=$prepend+1; $day<=$days_in_month; $day++,$cell++) {
      $day = str_pad($day, 2, '0', STR_PAD_LEFT);
      $day_date = $year . "-" . $month . "-" . $day;
      /*
      if this is first cell and not also the first day, end previous row
      */
      if( $cell == 1 && $day != 1 ) {
        $weekNum = \Carbon\Carbon::parse($day_date)->format('W');
        $output .= "<div class=\"week-num\">$weekNum</div><li class=\"week\">\n";
        //$output .= "<tr>\n";
      }
      
      //-------------- zero pad day and create date string for comparisons
      /*$day = str_pad($day, 2, '0', STR_PAD_LEFT);
      $day_date = $year . "-" . $month . "-" . $day;*/
      
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
      $title = '';
      $myRole = $this->myRole();
      if (count($ordered) >= $this->location_capacity) {
        if ($myRole == 'staff') {
          // show the order is placed by which colleague
          // e.g. Ordered by Xulong Zhang at 01:01:07 09-09-2015
          $title = "Ordered by " . array_get($ordered[0], 'name') . " at " . \Carbon\Carbon::parse(array_get($ordered[0], 'updated_at'))->format('H:i:s d-m-Y');
          $ordered = 'ordered';
        } elseif ($myRole == 'admin' || $myRole == 'root') {
          // show the order is placed by which colleague of which company
          // e.g. Ordered by Xulong Zhang from Netbay Wifi at 01:01:07 09-09-2015
          for ($j=0; $j < count($ordered); $j++) {
            if ($title != '') {
              $title .= "\n";
            }
            $title .= "\nOrdered by " . array_get($ordered[$j], 'name') . " from " . array_get($ordered[$j], 'company') . " at " . \Carbon\Carbon::parse(array_get($ordered[$j], 'updated_at'))->format('H:i:s d-m-Y');
          }
          $ordered = 'ordered';
        }
        $title = "title=\"$title\"";
      } elseif (count($ordered) > 0) {
        if ($myRole == 'staff') {
          // show the order is placed by which colleague
          // e.g. Ordered by Xulong Zhang at 01:01:07 09-09-2015
          $title = "Ordered by " . array_get($ordered[0], 'name') . " at " . \Carbon\Carbon::parse(array_get($ordered[0], 'updated_at'))->format('H:i:s d-m-Y');
          $ordered = 'available';
        } elseif ($myRole == 'admin' || $myRole == 'root') {
          // show the order is placed by which colleague of which company
          // e.g. Ordered by Xulong Zhang from Netbay Wifi at 01:01:07 09-09-2015
          for ($j=0; $j < count($ordered); $j++) {
            if ($title != '') {
              $title .= "\n";
            }
            $title .= "\nOrdered by " . array_get($ordered[$j], 'name') . " from " . array_get($ordered[$j], 'company') . " at " . \Carbon\Carbon::parse(array_get($ordered[$j], 'updated_at'))->format('H:i:s d-m-Y');
          }
          $ordered = 'available';
        }
        $title = "title=\"$title\"";
        //$ordered = 'available';
      } else {
        $ordered = '';
      }
      $output .= "<div class=\"small-1 medium-1 large-1 day $ordered \" date=\"" . preg_replace('/\s+/', ' ', ucwords(strftime($title_format, strtotime($day_date)))) . "\" $title>";
      //$output .= "\t<td" . $day_class . " date=\"" . ucwords(strftime($title_format, strtotime($day_date))) . "\">";
      
      //----------------------------------------- unset to keep loop clean
      //unset($day_class, $classes);
      
      //-------------------------------------- conditional, start link tag 
      //switch( $this->link_days ){
      switch( 0 ){
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
        $title = '';
        $myRole = $this->myRole();
        if (count($ordered) >= $this->location_capacity) {
          if ($myRole == 'staff') {
            // show the order is placed by which colleague
            // e.g. Ordered by Xulong Zhang at 01:01:07 09-09-2015
            $title = "Ordered by " . array_get($ordered[0], 'name') . " at " . \Carbon\Carbon::parse(array_get($ordered[0], 'updated_at'))->format('H:i:s d-m-Y');
            $ordered = 'ordered';
          } elseif ($myRole == 'admin' || $myRole == 'root') {
            // show the order is placed by which colleague of which company
            // e.g. Ordered by Xulong Zhang from Netbay Wifi at 01:01:07 09-09-2015
            for ($j=0; $j < count($ordered); $j++) {
              if ($title != '') {
                $title .= "\n";
              }
              $title .= "\nOrdered by " . array_get($ordered[$j], 'name') . " from " . array_get($ordered[$j], 'company') . " at " . \Carbon\Carbon::parse(array_get($ordered[$j], 'updated_at'))->format('H:i:s d-m-Y');
            }
            $ordered = 'ordered';
          }
          $title = "title=\"$title\"";
        } elseif (count($ordered) > 0) {
          if ($myRole == 'staff') {
            // show the order is placed by which colleague
            // e.g. Ordered by Xulong Zhang at 01:01:07 09-09-2015
            $title = "Ordered by " . array_get($ordered[0], 'name') . " at " . \Carbon\Carbon::parse(array_get($ordered[0], 'updated_at'))->format('H:i:s d-m-Y');
            $ordered = 'available';
          } elseif ($myRole == 'admin' || $myRole == 'root') {
            // show the order is placed by which colleague of which company
            // e.g. Ordered by Xulong Zhang from Netbay Wifi at 01:01:07 09-09-2015
            for ($j=0; $j < count($ordered); $j++) {
              if ($title != '') {
                $title .= "\n";
              }
              $title .= "\nOrdered by " . array_get($ordered[$j], 'name') . " from " . array_get($ordered[$j], 'company') . " at " . \Carbon\Carbon::parse(array_get($ordered[$j], 'updated_at'))->format('H:i:s d-m-Y');
            }
            $ordered = 'available';
          }
          $title = "title=\"$title\"";
          //$ordered = 'available';
        } else {
          $ordered = '';
        }
        $output .= "<div class=\"small-1 medium-1 large-1 day $ordered next-month\" date=\"" . preg_replace('/\s+/', ' ', ucwords(strftime($title_format, strtotime($day_date)))) . "\" $title>". $daynm . "</div>\n";
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