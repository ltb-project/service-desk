<div class="row">
  <div class="col">
    <a href="index.php">
      <img src="{$logo}" alt="{$msg_title}" class="logo img-fluid mx-auto d-block" />
    </a>
  </div>
  <div class="col p-5 d-flex flex-column">
    <div class="alert alert-success">{$msg_welcome|unescape: "html" nofilter}</div>
    {if $use_searchall}
      <a href="index.php?page=searchall" class="btn btn-outline-primary mt-4"><i class="fa fa-fw fa-users"></i> {$msg_allaccounts}</a>
    {/if}
    {if $use_create}
      <a href="index.php?page=create" class="btn btn-outline-success mt-4"><i class="fa fa-fw fa-circle-plus"></i> {$msg_createentry}</a>
    {/if}
  </div>
</div>

{if $prehookdeleteresult}
<div class="container">
    <div class="alert alert-danger"><i class="fa fa-fw fa-exclamation-circle"></i> {$msg_hookerror}</div>
    <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$prehookdeleteresult}</div>
</div>
{/if}

{if $posthookdeleteresult}
<div class="container">
    <div class="alert alert-danger"><i class="fa fa-fw fa-exclamation-circle"></i> {$msg_hookerror}</div>
    <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$posthookdeleteresult}</div>
</div>
{/if}
