<a href="index.php">
  <img src="{$logo}" alt="{$msg_title}" class="logo img-fluid mx-auto d-block" />
</a>

<div class="alert alert-success">{$msg_welcome|unescape: "html" nofilter}</div>

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
