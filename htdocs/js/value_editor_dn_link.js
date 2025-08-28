$(document).ready(function(){
  let timer;

  $(".dn_link_container input[type=text]").on("keyup", function (event) {
    var conf_search_min_chars = $(this).data("conf-searchminchars");
    var search_min_chars = conf_search_min_chars ? conf_search_min_chars : 3;
    // Remove value if field is emptied or less than minimal characters
    if ($(this).val().length < search_min_chars) {
        $(this).siblings('input[type=hidden]').val('') ;
        $(this).siblings('div.dn_link_suggestions').empty();
    }
    // Minimal search characters
    if ($(this).val().length >= search_min_chars) {
      if (timer) {
        clearTimeout(timer);
        $(this).siblings('div.dn_link_suggestions').empty();
      }

      timer = setTimeout(() => {
        $.post("index.php", { 'apiendpoint': 'search_dn', 'search': $(this).val(), 'search_type': 'dn_link' }, (data) => {
          // clear existing list
          $(this).siblings('div.dn_link_suggestions').empty();
          if (data.entries) {
            // add entries to list
            data.entries.forEach( (entry) => {
              const $elem = $(`<button type="button" class="list-group-item list-group-item-action">${entry.display}</button>`);
              $elem.on('click', () => {
                $(this).val(entry.display);
                $(this).siblings('input[type=hidden]').val(entry.dn);
                $(this).siblings('div.dn_link_suggestions').empty();
              })
              $(this).siblings('div.dn_link_suggestions').append($elem);
            });
            if (data.warning) {
              const $elem = $(`<span class="list-group-item list-group-item-warning">${data.warning}</span>`);
              $(this).siblings('div.dn_link_suggestions').append($elem);
            }
          }
          if (data.error) {
            const $elem = $(`<span class="list-group-item list-group-item-danger">${data.error}</span>`);
            $(this).siblings('div.dn_link_suggestions').append($elem);
          }
        }, 'json');
      }, 500);
    }
  });
});
