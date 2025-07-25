<script type="text/javascript">
{literal}
$(document).ready( function() {
{/literal}

    var datatables_params = JSON.parse(atob("{$datatables_params}"));
    var messages = datatables_params["messages"];

    loadCommentBox(  'unlockcommentbox',
                     'unlock',
                     'unlockaccount',
                      messages,
                     '{$dn}',
                     '{$returnto}',
                     '{$use_unlockcomment_required}'
                  );

    loadCommentBox(  'lockcommentbox',
                     'lock',
                     'lockaccount',
                      messages,
                     '{$dn}',
                     '{$returnto}',
                     '{$use_lockcomment_required}'
                  );

    loadCommentBox(  'disablecommentbox',
                     'disable',
                     'disableaccount',
                      messages,
                     '{$dn}',
                     '{$returnto}',
                     '{$use_disablecomment_required}'
                  );

    loadCommentBox(  'enablecommentbox',
                     'enable',
                     'enableaccount',
                      messages,
                     '{$dn}',
                     '{$returnto}',
                     '{$use_enablecomment_required}'
                  );

});
</script>
