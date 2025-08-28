$(document).ready(function(){
  $("input.fake-password").one("click", function(event) {
    $(this).removeAttr("placeholder");
    $(this).css("-webkit-text-security", "circle");
  });
});
