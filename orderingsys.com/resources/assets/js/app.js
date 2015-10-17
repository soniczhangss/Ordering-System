/**
 * Variable Declarations.
 * ############################################################################
 */
var isMouseDown             = false;
var isOrdering              = false;
var leftMouseButton         = 1;
var rightMouseButton        = 3;

var style_class_ordering    = "selected";
var style_class_highlighted = "highlighted";

/**
 * Foundation JS init.
 * ############################################################################
 */
$(document).foundation();

$(document).ready( function () {

/**
 * Home page events.
 * ############################################################################
 */
  $('.switch_month_year').click(function () {

    switch ( $(this).text() ) {

     case 'Month':
        $( "#container" ).load( "ajax/calendar/month", function () {
          initCalendarEventBinding();
        });
        break;

     case 'Year':
        $( "#container" ).load( "ajax/calendar/year", function () {
          initCalendarEventBinding();
        });
        break;

     default:
        break;
        
   }

  });

  $(document).contextMenu({
      selector: "li.week.selected",
      build: function($trigger, e) {

        if ($(e.target).hasClass("ordered")) {

          return {
            items: {
                foo: {
                  name: "Delete", callback: function (key, opt) {
                    $( "#reveal-modal-container" ).load( "ajax/order/rm/delete", function () {

                      initRevealModalEventBinding();

                      $('#modalTitle').text("Are you sure to delete the order?");

                      $( "input[name|='from']" ).val( function () {
                        return $( ".selected" ).children().first().attr( "title" );
                      });

                      $( "input[name|='to" ).val( function () {
                        return $( ".selected" ).children().last().attr( "title" );
                      });

                      $('#myModal').foundation('reveal', 'open');
                      
                    });
                  }
                }
            }
          }

        } else {

          return {
            items: {
                foo: {
                  name: "Order", callback: function (key, opt) {
                    $( "#reveal-modal-container" ).load( "ajax/order/rm/create", function () {

                      initRevealModalEventBinding();

                      $('#modalTitle').text("Are you sure to place the order?");

                      $( "input[name|='from']" ).val( function () {
                        return $( ".selected" ).children().first().attr( "title" );
                      });

                      $( "input[name|='to" ).val( function () {
                        return $( ".selected" ).children().last().attr( "title" );
                      });

                      $('#myModal').foundation('reveal', 'open');

                    });
                  }
                }
            }
          };

        }

      }
  });

/**
 * This function is for binding events to the calendar loaded by AJAX.
 * ############################################################################
 */
  function initCalendarEventBinding() {

    $( "li.week > div" )

      .mousedown( function (event) {
        if (event.which == leftMouseButton) {
          isMouseDown = true;
          $(this).parent().toggleClass(style_class_ordering);
          return false; // prevent text selection

          // if the class is toggled
            // get the date range within the selected area
          // else clear the date selected
        }
      })

      .mouseover( function () {
        if (isMouseDown) {
          $(this).parent().toggleClass(style_class_ordering);
        }
      })

      .bind("selectstart", function () {
            return false; // prevent text selection in IE
      });

    $(document).mouseup( function () {
      isMouseDown = false;
    });

    $( "li.week" )

      .mouseover ( function() {
          $( this ).addClass( style_class_highlighted );
        })

      .mouseout ( function() {
          $( this ).removeClass( style_class_highlighted );
      });

  }

/**
 * This function is for binding events to the calendar loaded by AJAX.
 * ############################################################################
 */
  function initRevealModalEventBinding() {

    $('.my-close-reveal-modal').click( function() {
      $('#myModal').foundation('reveal', 'close');
    });

    $(document).on('close.fndtn.reveal', '[data-reveal]', function () {
      $(document).removeClass(style_class_ordering);
    });

  }

});