<form id={$method} method="post" action="index.php?page={$page}">
    <input type="hidden" name="dn" value="{$dn}" />
    <div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="CommentModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="CommentModal">{$title}</h1>
                </div>
                <div class="modal-body">
                    <textarea class="form-control" name="comment" id="comment-{$method}" rows="3" placeholder="{$msg_insert_comment}"
                    {if $method eq 'lock' || $method eq 'unlock'}
                        {if $use_lockcomment_required || $use_unlockcomment_required}required{/if}
                    {elseif $method eq 'enable' || $method eq 'disable'}
                        {if $use_disablecomment_required || $use_enablecomment_required}required{/if}
                    {/if}
                    ></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-fw fa-window-close-o"></i> {$msg_close}
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-fw fa-check-square-o"></i> {$msg_submit}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
