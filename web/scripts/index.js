var select = true;
$(document).ready(function() {
  $("#selectall").click(function() {
    $("input[type=checkbox]").prop('checked', select);
    select = !select;
  });
});
