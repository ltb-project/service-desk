{if $type eq 'text'}
    <input type="text" class="form-control" value="{$value}" />
{else if $type eq 'mailto'}
    <input type="email" class="form-control" value="{$value}" />
{else}
    <input type="text" class="form-control" value="{$value}" />
{/if}
