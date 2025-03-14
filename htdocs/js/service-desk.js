$(document).ready(function(){
  let timer;

  $("input.fake-password").one("click", function(event) {
    $(this).removeAttr("placeholder");
    $(this).css("-webkit-text-security", "circle");
  });

  $(".dn_link_container input[type=text]").on("keyup", function (event) {
    // Remove value if field is emptied
    if ($(this).val().length == 0) {
        $(this).siblings('input[type=hidden]').val('') ;
    }
    // Minimal search characters
    if ($(this).val().length > 2) {
      if (timer) {
        clearTimeout(timer);
        // clear any existing list
        $(this).siblings('div.dn_link_suggestions').empty();
      }

      timer = setTimeout(() => {
        $.post("index.php", { 'apiendpoint': 'search_dn', 'search': $(this).val() }, (data) => {
          // clear existing list
          $(this).siblings('div.dn_link_suggestions').empty();
          // add entries to list
          data.forEach( (entry) => {
            const $elem = $(`<button type="button" class="list-group-item list-group-item-action">${entry.display}</button>`);
            $elem.on('click', () => {
              $(this).val(entry.display);
              $(this).siblings('input[type=hidden]').val(entry.dn);
              $(this).siblings('div.dn_link_suggestions').empty();
            })
            $(this).siblings('div.dn_link_suggestions').append($elem);
          });
        }, 'json');
      }, 500);
    }
  });
});
