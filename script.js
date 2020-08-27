'use strict';

var $checkbox = document.getElementsByClassName('show_completed');

if ($checkbox.length) {
  $checkbox[0].addEventListener('change', function (event) {
    var is_checked = +event.target.checked;

    var searchParams = new URLSearchParams(window.location.search);
    searchParams.set('show_completed', is_checked);

    window.location = '/index.php?' + searchParams.toString();
  });
}

flatpickr('#date', {
  enableTime: false,
  dateFormat: "Y-m-d",
  locale: "ru"
});

// ajax requests to handle Finished tasks (checkbox)
var checkbox = document.getElementsByClassName('task_checker');
var request = new XMLHttpRequest();

for (const element of checkbox) {

  element.addEventListener('click', function () {
    var status = element.checked;
    console.log(status);

    request.onreadystatechange = function () {
      if (request.readyState === 4) {
        console.log("success 4");
        if (request.status === 200) {
          console.log("success 200");


          $.ajax({
            type: 'GET',
            url: 'index.php',
            dataType: "json",
            data: {statusCheck: status}
            ,
            // .done( function (data) {
            //   console.log('done');
            //   console.log(data);
            // })
            // .fail( function( data ) {
            //   console.log('fail');
            //   console.log(data);
            // });
          success: function(data) {
            console.log(data);
          },
          error: function(response) {
            console.log(response);
          }});

        } else {
          console.log("error");
        }
      }
    }

    request.open('GET', 'index.php', true);

    console.log("request sent");
    request.send();

  })
}






/*
$(document).ready(function () {

  $(element).ready(function () {
    if ($("input[type='checkbox']").is(":checked") == true) {
      console.log("Check box " + $(element).val() + " is Checked");
      $(element).prop("checked", false);
    } else {
      console.log("Check box " + $(element).val() + " is Unchecked");
      $(element).prop("checked", true);
    }
  });

});*/
