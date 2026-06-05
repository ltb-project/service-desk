$(document).ready(function(){
  let timer;

  var userdn = $("div#user-dn").data("user-dn");

  $(document).on("change", 'input[data-component="membership"]', function (event) {
    var dn = $(this).data("dn");
    var checked = $(this).is(':checked');

    timer = setTimeout(() => {
        $.post("index.php", { 'apiendpoint': 'update_group_membership', 'userdn': userdn, 'dn': dn, 'checked': checked }, (data) => {
          if (data.error) {
            // Restore check status
            checked ? $(this).prop('checked', false) : $(this).prop('checked', true) ;
          }
        }, 'json');
      }, 500);
  });

});
