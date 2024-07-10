{include file="header.tpl"}

<div class="card mb-3 shadow card-success">
<div class="card-body">

{include file="menu.tpl"}

{if $page_title}
<div class="alert alert-info text-center fs-5">
    {$msg_{$page_title}}
</div>
{/if}

{if $error}
<div class="alert alert-danger">
    <i class="fa fa-fw fa-exclamation-circle"></i> {$error}
</div>
{else}
{include file="$page.tpl"}
{/if}

</div>
</div>

{include file="footer.tpl"}
