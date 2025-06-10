<div id="commentbox">
    <form id="commentid" method="post" action="index.php?page=pageid">
        <input type="hidden" name="dn" value="dn" />
        <input type="hidden" name="returnto" value="returnto" />
        <div class="modal fade" id="commentModalMethodHashedDN" tabindex="-1" aria-labelledby="CommentModalMethodHashedDN" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="CommentModal">title</h1>
                    </div>
                    <div class="modal-body">
                        <textarea class="form-control" name="comment" id="comment-method" rows="3" placeholder="msg_insert_comment"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa fa-fw fa-window-close-o"></i> msg_close
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-fw fa-check-square-o"></i> msg_submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
