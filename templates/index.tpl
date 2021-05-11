{include file="header.tpl"}

<div class="panel panel-success">
<div class="panel-body">

{include file="menu.tpl"}

{if $page_title}
<div class="alert alert-info">
    <p class="lead text-center">{$msg_{$page_title}}</p>
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
