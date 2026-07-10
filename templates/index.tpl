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
{include file="error_message.tpl"}
{/if}

{include file="$page.tpl"}

</div>
</div>

{include file="footer.tpl"}
