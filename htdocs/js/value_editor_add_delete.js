$(document).ready(function(){
  function delValueEditor(event) {
      $(this).closest('.value_editor_container').remove();
  }

  $('button[data-action=add]').on("click", function (event) {
      var item = $(this).attr('data-item');
      var length = $(this).closest('.value_editor_container').siblings('.value_editor_container').length;
      var newindex = length + 1;
      var clone = $(this).closest('.value_editor_container').clone(true);
      clone.find('.value_editor_field *[data-role=display]').val('');
      clone.find('.value_editor_field *[data-role=value]').val('');
      clone.find('.value_editor_field *[data-role=value]').attr('name', item + '' + newindex);
      clone.find('.value_editor_button button').removeClass('btn-success').addClass('btn-danger');
      clone.find('.value_editor_button button').attr('data-action','del');
      clone.find('.value_editor_button button').attr('data-index', newindex);
      clone.find('.value_editor_button button').off("click");
      clone.find('.value_editor_button button').on("click", delValueEditor);
      clone.find('.value_editor_button button span').removeClass('fa-plus').addClass('fa-minus');
      $(this).closest('.value_editor_container').parent().append(clone);
  });

  $('button[data-action=del]').on("click", delValueEditor);
});
