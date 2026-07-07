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
            checked ? showToast('group-added-ko') : showToast('group-removed-ko');
          } else {
            checked ? showToast('group-added-ok') : showToast('group-removed-ok');
          }
        }, 'json');
      }, 500);
  });

  function showToast(label) {
    var $template = $("#toast-template").html();
    var $toast = $($template);
    $toast.find('.toast-body > span').hide();
    $toast.find('[data-label='+label+']').show();
    $(".toast-container").append($toast);
    var toastBootstrap = new bootstrap.Toast($toast[0]);
    $toast.on('hidden.bs.toast', function () {
        $(this).remove();
    });
    toastBootstrap.show();
  }
});
