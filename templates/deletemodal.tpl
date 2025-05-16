<div class="modal fade" id="delete{$dn|sha256}" tabindex="-1" aria-labelledby="deleteModal{$dn|sha256}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="deletetModal{$dn|sha256}">{$msg_deleteentry}</h1>
            </div>
            <div class="modal-body">
                {$msg_deleteconfirmation}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-fw fa-window-close-o"></i> {$msg_close}
                </button>
                <a role="button" class="btn btn-danger" href="{$delete_link}">
                    <i class="fa fa-fw fa-user-minus"></i> {$msg_deleteentry}
                </a>
            </div>
        </div>
    </div>
</div>
