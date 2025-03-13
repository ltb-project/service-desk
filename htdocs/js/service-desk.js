$(document).ready(function(){
  let timer;

  $("input.fake-password").one("click", function(event) {
    $(this).removeAttr("placeholder");
    $(this).css("-webkit-text-security", "circle");
  });

  $(".dn_link input").on("keyup", function (event) {
    // Minimal search characters
    if ($(".dn_link input[type=text]").val().length > 2) {
      if (timer) {
        clearTimeout(timer);
        // clear any existing list
        $("div.dn_link_suggestions").empty();
      }

      timer = setTimeout(() => {
        $.post("index.php", { 'apiendpoint': 'search_dn', 'search': $(".dn_link input[type=text]").val() }, (data) => {
          // clear existing list
          $('div.dn_link_suggestions').empty();
          // add entries to list
          data.forEach( (entry) => {
console.log(entry);
            const $elem = $(`<button type="button" class="list-group-item list-group-item-action">${entry.display}</button>`);
            $elem.on('click', () => {
              $('.dn_link input[type=text]').val(entry.display);
              $('.dn_link input[type=hidden]').val(entry.dn);
              $('div.dn_link_suggestions').empty();
            })
            $('div.dn_link_suggestions').append($elem)
          });
        }, 'json');
      }, 500);
    }
  });
});
