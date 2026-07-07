<div aria-live="polite" aria-atomic="true" class="position-relative">
  <div class="toast-container top-0 end-0">
  </div>
</div>

<div class="hidden" id="toast-template">
  <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <strong class="me-auto">{$msg_result}</strong>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
      <span data-label="group-added-ok">{$msg_groupmembership_addedok}</span>
      <span data-label="group-removed-ok">{$msg_groupmembership_removedok}</span>
      <span data-label="group-added-ko">{$msg_groupmembership_addedko}</span>
      <span data-label="group-removed-ko">{$msg_groupmembership_removedko}</span>
    </div>
  </div>
</div>
