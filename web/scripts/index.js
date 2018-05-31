var select = true;
$(document).ready(function() {
  $("#selectall").click(function() {
    $("input[type=checkbox]").prop('checked', select);
    select = !select;
  });
  $("#mainf").submit(function(e) {
    return window.confirm("Confirm deletion of "+$("input[type=checkbox]:checked").length+" files?");
  });
});
