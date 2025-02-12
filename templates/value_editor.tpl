{if $type eq 'text'}
    <input type="text" name={$item} class="form-control" value="{$value}" />
{else if $type eq 'mailto'}
    <input type="email" name={$item} class="form-control" value="{$value}" />
{else}
    <input type="text" name={$item} class="form-control" value="{$value}" />
{/if}
