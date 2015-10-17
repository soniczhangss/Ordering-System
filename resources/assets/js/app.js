/**
 * Variable Declarations.
 * ############################################################################
 */
var isMouseDown             = false;
var isOrdering              = false;
var leftMouseButton         = 1;
var rightMouseButton        = 3;
var userGroups              = ["guest", "staff", "admin", "root"];
var role                    = '';
var today                   = new Date($.now());
var isCtrlDown              = false;
var isShiftDown             = false;
var lastEl                  = null;
var contextMenuOn           = false;
var isOrdered               = false;

var style_class_ordering    = "selected";
var style_class_highlighted = "highlighted";

/**
 * Foundation JS init.
 * ############################################################################
 */
$(document).foundation();

$(document).ready( function () {
/**
 * Global events.
 * ############################################################################
 */
 $.getJSON( "/ajax/whoami", function( data ) {
    role = data.name;
  });

  initCalendarNavEventBinding();
  
  $( ".switch_month_year:eq( 1 )" ).click();

  $(".fi-power").click( function() {
     window.location = "/auth/logout";
  });

  $("#sign-up").click( function() {
    window.location = "/auth/register";
  });

  $(".location").click( function() {
    var location = $( this ).text();
    location_url = location.split(" ").join("_");
    date = 1 + "-" + (today.getMonth() + 1) + "-" + today.getFullYear();
    if ( !$( "#container" ).length ) {
      $( "#content-container" ).load( "ajax/calendar/nav", function() {
        initCalendarNavEventBinding();
        $( "#container" ).load( "ajax/calendar/year/" + date + "/" + location_url, function () {
          initCalendarEventBinding('year');
          $("#bc-location").text(location);
        });
      });
    } else {
      $( "#container" ).load( "ajax/calendar/year/" + date + "/" + location_url, function () {
        initCalendarEventBinding('year');
        $("#bc-location").text(location);
      });
    }
    
  });

  $("#settings").click( function() {
    $( "#content-container" ).load( "/ajax/settings", function() {
      $("#bc-location").text("Settings");
    });
  });

  $(document).on( 'keydown', function( event ) {

    if (event.ctrlKey) {
      isCtrlDown = true;
    }

    if (event.shiftKey) {
      isShiftDown = true;
    }

  });

  $(document).on( 'keyup', function( event ) {
    isCtrlDown = false;
    isShiftDown = false;
  });

  $(document).click(function ( event ) {
    if (!$(event.target).is("li.week > div") && !contextMenuOn && !$(event.target).is(".button.success")) {
      if (!$(event.target).is("table tbody tr td"))
        $("li.week").removeClass(style_class_ordering);
      lastEl = null;
    }
  });

  $("#location-management").click( function() {
    $( "#content-container" ).load( "/ajax/locationmanagement", function() {
      $("#bc-location").text("Location Management");

      $('#location-table').DataTable( {
        "dom": 'Btp',
          "buttons": [
              {
                  text: 'Add',
                  action: function ( e, dt, node, config ) {
                    $( "#reveal-modal-container" ).load( "ajax/locationmanagement/rm", function () {

                      $('#modalTitle').text("New Location");

                      $('#myModal').foundation('reveal', 'open');

                      $('.my-close-reveal-modal').click( function() {
                        $('#myModal').foundation('reveal', 'close');
                      });

                      $('#myModal .button.success').click( function() {
                        var location = $("#location").val();
                        var allowance = $("#allowance").val();
                        var data = '{"data":[{"location": "' + location + '", "allowance": "' + allowance + '"}]}';
                        
                        $.ajax({
                          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                          method: "POST",
                          url: "/ajax/location/store",
                          dataType: "json",
                          data: data
                        }).complete(function() {
                          $('#myModal').foundation('reveal', 'close');
                          //window.location = "/";
                          $("#location-management").click();
                        });
                      });
                    });
                  }
              }
          ],
          "ajax": {
              url: '/ajax/locationmanagement/json',
              dataSrc: ''
          },
          "columnDefs": [{
              "render": function ( data, type, row ) {
                return '<input class="location" type="text" value="' + data + '" />';
              },
              "targets": 0
          },{
              "render": function ( data, type, row ) {
                return '<input class="allowance" type="text" value="' + data + '" />';
              },
              "targets": 1
          },
          {
            "orderable": false,
            "targets": 1
          }],
          "columns": [{ data: 'name' },
                      { data: 'capacity' }]   
      } );

      $( document ).on("focus", "input.location, input.allowance", function() {
        var locationOld;
        var allowanceOld;
        if ($(this).attr("class") == "allowance") {
          locationOld = $(this).parent().prev().children().first().val();
          allowanceOld = $( this ).val();
        } else {
          locationOld = $(this).parent().prev().children().first().val();
        }

        $( document ).on("focusout", "input.location, input.allowance", function() {
          if ($(this).attr("class") == "allowance") {
            var location = $(this).parent().prev().children().first().val();
            var allowance = $( this ).val();
            if (locationOld != location || allowanceOld != allowance) {
              var data = '{"data":[{"locationOld": "' + locationOld + '", "location": "' + location + '", "allowance": "' + allowance + '"}]}';
              $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                method: "POST",
                url: "/ajax/location/update",
                dataType: "json",
                data: data
              });
            }
          } else {
            var location = $(this).parent().prev().children().first().val();
            if (locationOld != location) {
              var data = '{"data":[{"locationOld": "' + locationOld + '", "location": "' + location + '"}]}';
              $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                method: "POST",
                url: "/ajax/location/update",
                dataType: "json",
                data: data
              });
            }
          }
        });
      });
      
    });
  });

  $("#company-management").click( function() {
    $( "#content-container" ).load( "/ajax/companymanagement", function() {
      $("#bc-location").text("Company Management");

      $('#company-table').DataTable( {
          "dom": 'Btp',
          "buttons": [
              {
                  text: 'Add',
                  action: function ( e, dt, node, config ) {
                    $( "#reveal-modal-container" ).load( "ajax/companymanagement/rm", function () {

                      $('#modalTitle').text("New Company");

                      $('#myModal').foundation('reveal', 'open');

                      $('.my-close-reveal-modal').click( function() {
                        $('#myModal').foundation('reveal', 'close');
                      });

                      $('#myModal .button.success').click( function() {
                        var company = $("#company").val();
                        var data = '{"data":[{"company": "' + company + '"}]}';
                        
                        $.ajax({
                          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                          method: "POST",
                          url: "/ajax/company/store",
                          dataType: "json",
                          data: data
                        }).complete(function() {
                          $('#myModal').foundation('reveal', 'close');
                          //window.location = "/";
                          $("#company-management").click();
                        });
                      });
                    });
                  }
              }
          ],
          "ajax": {
              url: '/ajax/companymanagement/json',
              dataSrc: ''
          },
          "columnDefs": [{
              "render": function ( data, type, row ) {
                return '<input class="company" type="text" value="' + data + '" />';
              },
              "targets": 0
          }],
          "columns": [{ data: 'name' }]   
      } );

      $( document ).on("focus", "input.company", function() {
        var companyOld = $(this).val();

        $( document ).on("focusout", "input.company", function() {
          var company = $(this).val();
          if (companyOld != company) {
            var data = '{"data":[{"companyOld": "' + companyOld + '", "company": "' + company + '"}]}';
            $.ajax({
              headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
              method: "POST",
              url: "/ajax/company/update",
              dataType: "json",
              data: data
            });
          }
        });
      });
      
    });
  });

  $("#account-management").click( function() {
    $( "#content-container" ).load( "/ajax/accountmanagement", function() {
      $("#bc-location").text("Account Management");

      $('#user-table').DataTable( {
          "ajax": {
              url: '/ajax/accountmanagement/json',
              dataSrc: ''
          },
          "columnDefs": [{
              "render": function ( data, type, row ) {
                // append option if the value equals to data then set selected
                var list = '<select class="target-role">';
                $.each(userGroups, function( index, value ) {
                  if (value == data) {
                    list += '<option value="' + value + '" selected>' + value + '</option>';
                  } else {
                    list += '<option value="' + value + '">' + value + '</option>';
                  }
                });
                return list + '</select>';
              },
              "targets": 3
          },
          {
            "orderable": false,
            "targets": -1
          }],
          "columns": [{ data: 'username' },
                    { data: 'email' },
                    { data: 'company' },
                    { data: 'name' }]
      } );

      $( document ).on( 'change', '.target-role', function ( event ) {
        var tds = $(this).parent().siblings();
        var username = tds.first().text();
        var email = tds.eq(1).text();
        var company = tds.last().text();
        var role = $(this).val();

        $.post( "/ajax/accountmanagement/userupdate", { name: username, email: email, company: company, role: role, _token:$('meta[name="csrf-token"]').attr('content') })
          .done(function() {
            //alert( "second success" );
          })
          .fail(function() {
            alert( "error" );
          })
          .always(function() {
            //alert( "finished" );
        });
      });
      
    });
  });

  $("#report").click( function() {
    $( "#content-container" ).load( "/ajax/report", function() {
      $("#bc-location").text("Report");

      $('#report-table').DataTable( {
          "dom": 'Btp',
          "buttons": [
              'excel', 'pdf', 'copy', 'print'
          ],
          "ajax": {
              url: '/ajax/report/json',
              dataSrc: ''
          },
          "columnDefs": [{
              "render": function ( data, type, row ) {
                return moment(data).format('DD-MM-YYYY');
              },
              "targets": 3
          },
          {
              "render": function ( data, type, row ) {
                return moment(data).format('DD-MM-YYYY');
              },
              "targets": 4
          },
          {
              "render": function ( data, type, row ) {
                return moment(data).format('HH:mm:ss DD-MM-YYYY');
              },
              "targets": 6
          }],
          "columns": [{ data: 'id' },
                    { data: 'customer' },
                    { data: 'company' },
                    { data: 'from' },
                    { data: 'to' },
                    { data: 'location' },
                    { data: 'created_at' }]
      } );
      
    });
  });

  $(document).contextMenu({
      selector: "li.week.selected",
      position: function(opt, x, y) {
        opt.$menu.css('display', 'block').position({ my: "center top", at: "center bottom", of: this, offset: "0 5"}).css('display', 'none');
      },
      build: function($trigger, e) {
        
        if ($(e.target).hasClass("ordered")) {

          return {
            items: {
                delete: {
                  name: "Delete", callback: function (key, opt) {
                    contextMenuOn = true;
                    $( "#reveal-modal-container" ).load( "ajax/order/rm/delete", function () {
                      myAccordion();
                      $(document).foundation();
                      initRevealModalEventBinding('delete');
                      //$('#modalTitle').text("Are you sure to delete the order?");
                      $('#myModal').removeClass('tiny');
                      $('#myModal').foundation('reveal', 'open');
                      contextMenuOn = false;
                    });
                  },
                  disabled: function () {
                    if (role == 'admin' || role == 'root') {
                      return false;
                    } else {
                      return true;
                    }
                  }
                }
            }
          }

        } else if ($(e.target).hasClass("available")) {
          return {
                items: {
                  Order: {
                      name: "Order",
                      callback: function (key, opt) {
                      contextMenuOn = true;
                        $( "#reveal-modal-container" ).load( "ajax/order/rm/create", function () {

                          initRevealModalEventBinding('create');

                          $('#modalTitle').text("Are you sure to place the order?");

                          $('#myModal').foundation('reveal', 'open');

                          contextMenuOn = false;
                        });
                      },
                      disabled: function () {
                        if (role != 'guest') {
                          return false;
                        } else {
                          return true;
                        }
                      }
                    },
                delete: {
                  name: "Delete", callback: function (key, opt) {
                    contextMenuOn = true;
                    $( "#reveal-modal-container" ).load( "ajax/order/rm/delete", function () {
                      myAccordion();
                      $(document).foundation();
                      initRevealModalEventBinding('delete');
                      $('#myModal').removeClass('tiny');
                      //$('#modalTitle').text("Are you sure to delete the order?");
                      $('#myModal').foundation('reveal', 'open');
                      
                      contextMenuOn = false;
                    });
                  },
                  disabled: function () {
                    if (role == 'admin' || role == 'root') {
                      return false;
                    } else {
                      return true;
                    }
                  }
                }
            }
          }
        } else {

          return {
            items: {
                Order: {
                  name: "Order",
                  callback: function (key, opt) {
                  contextMenuOn = true;
                    $( "#reveal-modal-container" ).load( "ajax/order/rm/create", function () {

                      initRevealModalEventBinding('create');

                      $('#modalTitle').text("Are you sure to place the order?");

                      $('#myModal').foundation('reveal', 'open');

                      contextMenuOn = false;
                    });
                  },
                  disabled: function () {
                    if (role != 'guest') {
                      return false;
                    } else {
                      return true;
                    }
                  }
                }
            }
          }
        };

        }
  });

/**
 * This function is for binding events to the calendar loaded with AJAX.
 * ############################################################################
 */
  function initCalendarEventBinding(type) {

    $( "li.week > div" ).click( function ( event ) {
      // One selection limitation
      if (!isCtrlDown && !isShiftDown) {
        $("li.week").removeClass(style_class_ordering);
        lastEl = null;
      }

      if (!isShiftDown) {
        lastEl = null;
      }

      var backwards = true;
      if (!lastEl && !isShiftDown) {
        lastEl = $(this);
      } else if (new Date(lastEl.attr("date")) > new Date($(this).attr("date"))) {
          backwards = false;
      }

      if ($(this).hasClass("ordered") && !isShiftDown) {
        isOrdered = true;
      } else if (!$(this).hasClass("ordered") && !isShiftDown) {
        isOrdered = false;
      }

      if (isCtrlDown) {
        if (event.which == leftMouseButton) {
          if ($(this).hasClass("ordered")) {
            if ($('.calendar').find('.selected .ordered').length == 0) {
              //alert("not allowed");
            } else {
              $(".calendar div[date='" + $(this).attr('date') + "']").parent().toggleClass(style_class_ordering);
              //$(this).parent().toggleClass(style_class_ordering);
            }
          }
          if (!$(this).hasClass("ordered")) {
            if ($('.calendar').find('.selected .ordered').length == 0) {
              $(".calendar div[date='" + $(this).attr('date') + "']").parent().toggleClass(style_class_ordering);
              //$(this).parent().toggleClass(style_class_ordering);
            } else {
              //alert("not allowed");
            }
          }
          return false;
        }
      }

      if (backwards) {

        var thiEl = $(this).parent();
        $(".calendar .week div[date='" + $(this).attr('date') + "']").parent().addClass(style_class_ordering);
        if (isShiftDown && lastEl) {
          var nextEl;
          if (thiEl.is('li:nth-child(4)')) {
            nextEl = thiEl.parent().parent().prev().children().first().children().last();
          } else {
            nextEl = thiEl.prev().prev();
          }
          while (new Date(nextEl.children().first().attr('date')) > new Date(lastEl.attr('date'))) {
            if (isOrdered) {
              if (nextEl.children().first().hasClass("ordered")) {
                nextEl.addClass(style_class_ordering);
                if (nextEl.is('li:nth-child(4)')) {
                  nextEl = nextEl.parent().parent().prev().children().first().children().last();
                } else {
                  nextEl = nextEl.prev().prev();
                }
              } else if (nextEl.is('li:nth-child(4)')) {
                nextEl = nextEl.parent().parent().prev().children().first().children().last();
              } else {
                nextEl = nextEl.prev().prev();
              }
            } else if (!nextEl.children().first().hasClass("ordered")) {
              nextEl.addClass(style_class_ordering);
              if (nextEl.is('li:nth-child(4)')) {
                nextEl = nextEl.parent().parent().prev().children().first().children().last();
              } else {
                nextEl = nextEl.prev().prev();
              }
            } else {
              if (nextEl.is('li:nth-child(4)')) {
                nextEl = nextEl.parent().parent().prev().children().first().children().last();
              } else {
                nextEl = nextEl.prev().prev();
              }
            }
          }
          if (isOrdered) {
            if (!$(this).hasClass("ordered")) {
              //thiEl.removeClass("selected");
              $(".calendar div[date='" + $(this).attr('date') + "']").parent().removeClass(style_class_ordering);
            }
          } else {
            if ($(this).hasClass("ordered")) {
              //thiEl.removeClass("selected");
              $(".calendar div[date='" + $(this).attr('date') + "']").parent().removeClass(style_class_ordering);
            }
          }
        }

      } else {

        var thiEl = $(this).parent();
        $(".calendar div[date='" + $(this).attr('date') + "']").parent().addClass(style_class_ordering);
        //thiEl.addClass(style_class_ordering);
        if (isShiftDown && lastEl) {
          //var nextEl = thiEl.next();
          var nextEl;
          if (thiEl.is('li:last-child')) {
            nextEl = thiEl.parent().parent().next().children().children('li.week').first();
          } else {
            nextEl = thiEl.next().next();
          }
          while (new Date(nextEl.children().last().attr('date')) < new Date(lastEl.attr('date'))) {
            if (isOrdered) {
              if (nextEl.children().first().hasClass("ordered")) {
                nextEl.addClass(style_class_ordering);
                if (nextEl.is('li:last-child')) {
                  nextEl = nextEl.parent().parent().next().children().children('li.week').first();
                } else {
                  nextEl = nextEl.next().next();
                }
              } else {
                if (nextEl.is('li:last-child')) {
                  nextEl = nextEl.parent().parent().next().children().children('li.week').first();
                } else {
                  nextEl = nextEl.next().next();
                }
              }
            } else if (!nextEl.children().first().hasClass("ordered")) {
              nextEl.addClass(style_class_ordering);
              if (nextEl.is('li:last-child')) {
                nextEl = nextEl.parent().parent().next().children().children('li.week').first();
              } else {
                nextEl = nextEl.next().next();
              }
            } else {
              if (nextEl.is('li:last-child')) {
                  nextEl = nextEl.parent().parent().next().children().children('li.week').first();
                } else {
                  nextEl = nextEl.next().next();
                }
            }
          }
          if (isOrdered) {
            if (!$(this).hasClass("ordered")) {
              //thiEl.removeClass("selected");
              $(".calendar div[date='" + $(this).attr('date') + "']").parent().removeClass(style_class_ordering);
            }
          } else {
            if ($(this).hasClass("ordered")) {
              //thiEl.removeClass("selected");
              $(".calendar div[date='" + $(this).attr('date') + "']").parent().removeClass(style_class_ordering);
            }
          }
        }

      }
    });

    /*$( "li.week" )

      .mouseover ( function() {
          $( this ).addClass( style_class_highlighted ).animate({transition: "0.5s"});
        })

      .mouseout ( function() {
          $( this ).removeClass( style_class_highlighted ).animate({transition: "0.5s"});
      });*/

    $( "label.nav-date" ).text( function() {
      date = $( "li.my-day-header" ).first().text();
      switch(type) {
        case 'month':
          $( ".calendar .week .day" ).addClass("month_day_height");
          return date;
          break;
        case 'year':
          return date.split(" ")[1];
          break;
        default: break;
      }
    });

  }

/**
 * This function is for binding events to the calendar nav bar loaded with AJAX.
 * ############################################################################
 */
  function initCalendarNavEventBinding() {
    $('.switch_month_year').click( function () {
      var date = $( "label.nav-date" ).text();
      if (date == '') {
        date = today.getDate() + "-" + (today.getMonth() + 1) + "-" + today.getFullYear();
      // year
      } else if ($.isNumeric(date)) {
        date = 1 + "-" + (today.getMonth() + 1) + "-" + date;
      // month year
      } else {
        date = 1 + "-" + date.split(" ")[0] + "-" + date.split(" ")[1];
        date = new Date(date);
        date = 1 + "-" + (date.getMonth() + 1) + "-" + date.getFullYear();
      }

      switch ( $(this).text() ) {

        case 'Month':
          $( "#container" ).load( "ajax/calendar/month/" + date, function () {
            initCalendarEventBinding('month');
          });
          break;

        case 'Year':
          $( "#container" ).load( "ajax/calendar/year/" + date, function () {
            initCalendarEventBinding('year');
          });
          break;

        default:
          break;
          
      }

    });

    $('.fi-arrow-left').click( function() {
      date = $( "label.nav-date" ).text();
      // year
      if ($.isNumeric(date)) {
        date = date - 1;
        date = 1 + "-" + (today.getMonth() + 1) + "-" + date;
        $( "#container" ).load( "ajax/calendar/year/" + date, function () {
            initCalendarEventBinding('year');
          });
      // month year
      } else {
        date = new Date(1 + "-" + date.split(" ")[0] + "-" + date.split(" ")[1]);
        var month = date.getMonth();
        var year = date.getFullYear();
        if (month < 0) {
          year = year - 1;
        }
        date = 1 + "-" + month + "-" + year;
        $( "#container" ).load( "ajax/calendar/month/" + date, function () {
            initCalendarEventBinding('month');
          });
      }
    });

    $('.fi-arrow-right').click( function() {
      date = $( "label.nav-date" ).text();
      // year
      if ($.isNumeric(date)) {
        date = Number(date) + 1;
        date = 1 + "-" + (today.getMonth() + 1) + "-" + date;
        $( "#container" ).load( "ajax/calendar/year/" + date, function () {
            initCalendarEventBinding('year');
          });
      // month year
      } else {
        date = new Date(1 + "-" + date.split(" ")[0] + "-" + date.split(" ")[1]);
        var month = date.getMonth() + 2;
        var year = date.getFullYear();
        if (month > 12) {
          month = 1;
          year = year + 1;
        }
        date = 1 + "-" + month + "-" + year;
        $( "#container" ).load( "ajax/calendar/month/" + date, function () {
            initCalendarEventBinding('month');
          });
      }
    });
  }

/**
 * This function is for binding events to the calendar loaded with AJAX.
 * ############################################################################
 */
  function initRevealModalEventBinding(intent) {

    $('#myModal .button.success').click( function() {
      var found = {};
      var data = '{"data":[';
      var selectedEls = $('.calendar').find('.selected');
      if (selectedEls.length > 0 ) {
        selectedEls.each(function () {
          var from = $(this).children().first().attr( "date" );
          var to = $(this).children().last().attr( "date" );
          var location = $("#bc-location").text();
          var from_to = '';
          if(found[$(this).children().first().attr( "date" )]) {
            //$(this).remove();
          } else {
            found[$(this).children().first().attr( "date" )] = true;
            from_to = '{"from":"' + from + '","to":"' + to + '","location":"' + location + '"}';
          }
          
          //var from_to = '{"from":"' + from + '","to":"' + to + '","location":"' + location + '"}';
          if (from_to != '') {
            if (data != '{"data":[') {
              data += "," + from_to;
            } else {
                data += from_to;
            }
          }
        });
        data += ']}';
      }

      if (intent === 'delete') {
        // get checked data
        data = checkedRecords();
        mySubmit(intent, data);

        //$( ".selected" ).children().removeClass("ordered");
        // todo: remove tooltips
        //$( ".selected" ).removeClass("selected");
        $('#myModal').foundation('reveal', 'close');
      } else if (intent === 'create') {
        mySubmit(intent, data);

        // send all selected orderables(anything not ordered) in Json to server
        // on the server side, check each week if it has reached capacity
        // Json from server should look sth like {"data": [{"from": xxx, "to": xxx, "status": ordered/availavle}]}
        /*$.getJSON( "ajax/location/capacity/json", function( data ) {
          alert(data);
        });*/

        //$( ".selected" ).children().addClass("ordered");
        // todo: add tooltips
        //$( ".selected" ).removeClass("selected");
        $('#myModal').foundation('reveal', 'close');
      }
    });

    $('.my-close-reveal-modal').click( function() {
      $('#myModal').foundation('reveal', 'close');
    });

    $(document).on('close.fndtn.reveal', '[data-reveal]', function () {
      $(document).removeClass(style_class_ordering);
    });

  }
/**
 * This function is for feteching data from checked rows and return in Json format.
 * ############################################################################
 */
 function checkedRecords() {
  var data = '{"data":[';
  var found = $('.selected4D');
  if (found.length > 0) {
    found.each(function () {
      var id = '';
      id = '{"id":"' + $(this).children().first().text() + '"}';
      if (id != '') {
        if (data != '{"data":[') {
          data += "," + id;
        } else {
            data += id;
        }
      }
    });
    data += ']}';
    return data;
  } else {
    return false;
  }
  
 }

/**
 * This function is for submitting the ordering data.
 * ############################################################################
 */
  function mySubmit(intent, data) {
    if (intent === 'delete') {
        $.ajax({
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          method: "POST",
          url: "/ajax/order/d",
          dataType: "json",
          data: data
        })
          .success(function( msg ) {
            var data = '{"data":[';
            var location = $("#bc-location").text();
            var from_to = '';
            for (var i = 0; i < msg.data.length; i++) {
              var thisItem = msg.data[i];
              if (parseInt(thisItem.status, 10) == 1) {
                $(".calendar li.week div[date='" + moment(thisItem.from, 'DD-MM-YYYY').format('dddd, MMMM D, YYYY') + "']").siblings().andSelf().removeClass("ordered").toggleClass("available");
              } else if (parseInt(thisItem.status, 10) == -1) {
                $(".calendar li.week div[date='" + moment(thisItem.from, 'DD-MM-YYYY').format('dddd, MMMM D, YYYY') + "']").siblings().andSelf().removeClass("available").removeClass("ordered");
              }

              from_to = '{"from":"' + thisItem.from + '","to":"' + thisItem.to + '","location":"' + location + '"}';
              if (from_to != '') {
                if (data != '{"data":[') {
                  data += "," + from_to;
                } else {
                    data += from_to;
                }
              }
            }
            data += ']}';
            loadToolTips(data);
            $( ".selected" ).removeClass("selected");
          });
      } else if (intent === 'create') {
        $.ajax({
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          method: "POST",
          url: "/ajax/order",
          dataType: "json",
          data: data
        })
          .success(function( msg ) {
            //alert(msg.data.length);
            var data = '{"data":[';
            var location = $("#bc-location").text();
            var from_to = '';
            for (var i = 0; i < msg.data.length; i++) {
              var thisItem = msg.data[i];
              //alert(moment(thisItem.from, 'DD-MM-YYYY').format('dddd, MMMM D, YYYY'));
              if (parseInt(thisItem.status, 10)) {
                $(".calendar .selected div[date='" + moment(thisItem.from, 'DD-MM-YYYY').format('dddd, MMMM D, YYYY') + "']").siblings().andSelf().toggleClass("available");
                //$( ".selected:eq( " + i + " )" ).children().addClass("available");
              } else {
                $(".calendar .selected div[date='" + moment(thisItem.from, 'DD-MM-YYYY').format('dddd, MMMM D, YYYY') + "']").siblings().andSelf().removeClass("available").toggleClass("ordered");
                //$( ".selected:eq( " + i + " )" ).children().removeClass("available").addClass("ordered");
              }

              from_to = '{"from":"' + thisItem.from + '","to":"' + thisItem.to + '","location":"' + location + '"}';
              if (from_to != '') {
                if (data != '{"data":[') {
                  data += "," + from_to;
                } else {
                    data += from_to;
                }
              }
            }
            data += ']}';
            loadToolTips(data);
            $( ".selected" ).removeClass("selected");
          });
      }
  }

/**
* This function is for displaying the interface of deleting orders.
* ############################################################################
*/
  function myAccordion() {
    var found = {};
    var i = 0;
    var generated = '';
    var data = '{"data":[';
    var selectedEls = $('.calendar').find('.selected');
    if (selectedEls.length > 0 ) {
      selectedEls.each(function () {
        if(found[$(this).children().first().attr( "date" )]) {
        } else {
          found[$(this).children().first().attr( "date" )] = true;

          var dayHeader = $(this).prevAll(".day-header");
          var myDayHeader = $(this).prevAll(".my-day-header");
          var temp = '<li class="accordion-navigation"><a href="#panel' + i + 'a"><div><ul class="calendar">';
          temp += myDayHeader.wrap('<p/>').parent().html();
          myDayHeader.unwrap();
          temp += dayHeader.wrap('<p/>').parent().html();
          dayHeader.unwrap();
          var wk = $(this).prev('div');
          temp += wk.wrap('<p/>').parent().html();
          wk.unwrap();
          temp += $(this).wrap('<p/>').parent().html();
          $(this).unwrap();
          var orderingDataTable =
          '<table id="table' + i + '"><thead><tr><th>Order No.</th><th>Customer</th><th>Company</th><th>Order Created At</th></tr></thead><tbody></tbody></table>';
          temp += '</ul></div></a><div id="panel' + i + 'a" class="content active">' + orderingDataTable + '</div></li>';

          var from = $(this).children().first().attr("date");
          var to = $(this).children().last().attr("date");
          var location = $("#bc-location").text();
          
          if (data != '{"data":[')
            data += ',';
          data += '{"from":"' + from + '","to":"' + to + '","location":"' + location + '"}';
          
          
          generated += temp;
          i++;
        }
      });
      data += ']}';
      $.ajax({
              headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
              method: "POST",
              url: "/ajax/orders/delete/json",
              dataType: "json",
              data: data
            })
              .success(function( msg ) {
                for (var j = 0; j < i; j++) {
                  $('#table' + j).DataTable({
                    "dom": '',
                    "aaData": msg.data[j],
                    "rowCallback": function( row, data ) {
                        if ( $.inArray(data.DT_RowId, selected) !== -1 ) {
                            $(row).addClass('selected');
                        }
                    },
                    "aoColumns": [{ data: 'id' },
                                { data: 'customer' },
                                { data: 'company' },
                                { data: 'created_at' }]
                  });
                }
              });
    }

    $( "#generated" ).replaceWith(generated);
    var selected = [];
    $('[id^=table] tbody').on('click', 'tr', function () {
        var id = this.id;
        var index = $.inArray(id, selected);
 
        if ( index === -1 ) {
            selected.push( id );
        } else {
            selected.splice( index, 1 );
        }
 
        $(this).toggleClass('selected4D');
    } );
  }

/**
* This function is for loading tooltips.
* ############################################################################
*/
  function loadToolTips(orders) {
    $.ajax({
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      method: "POST",
      url: "/ajax/tooltips/json",
      dataType: "json",
      data: orders
    })
      .success(function( msg ) {
        for (var i = 0; i < msg.data.length; i++) {
          var thisItem = msg.data[i];
          $(".calendar li.week div[date='" + moment(thisItem.from, 'DD-MM-YYYY').format('dddd, MMMM D, YYYY') + "']").siblings().andSelf().attr("title", thisItem.tooltip);
        }
      });
  }

});