<div class="update row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="card mb-3 shadow">
            <div class="card-header text-bg-secondary text-center">
                <p class="card-title">
                    <i class="fa fa-fw fa-{$attributes_map.{$card_title}.faclass}"></i>
                    {$entry.{$attributes_map.{$card_title}.attribute}.0}
                </p>
            </div>

            <form method="post">

            <input type="hidden" name="dn" value="{$dn}"/>

            <div class="card-body">
                <div class="container-fluid">
                {assign var="modulo" value=0}
                {foreach from=$card_items item=item name=items}
                {$attribute=$attributes_map.{$item}.attribute}
                {$type=$attributes_map.{$item}.type}
                {$faclass=$attributes_map.{$item}.faclass}
                {$multivalued=$attributes_map.{$item}.multivalued}

                {if !({$entry.$attribute.0}) && ! $item|in_array:$update_items}
                    {if $modulo==0}{assign var="modulo" value=1}{else}{assign var="modulo" value=0}{/if}
                    {continue}
                {/if}

                    <div class="row align-items-center p-2{if $smarty.foreach.items.iteration % 2 == $modulo} bg-white{/if}" id="update_{$item}">
                        <div class="col-1 px-1">
                            <i class="fa fa-fw fa-{$faclass}"></i>
                        </div>
                        <div class="col-3 d-none d-sm-block px-1">
                            {$msg_label_{$item}}
                        </div>
                        <div class="col px-1">
                            {if $item|in_array:$update_items}
                                {if !({$entry.$attribute.0})}
                                {include 'value_editor.tpl' item=$item itemindex=0 value="" type=$type list=$item_list.$item multivalued=$multivalued truncate_value_after=10000}
                                {else}
                                    {foreach from=$entry.{$attribute} item=$value name=updatevalue}
                                        {include 'value_editor.tpl' item=$item itemindex=$smarty.foreach.updatevalue.index multivalued=$multivalued value=$value type=$type list=$item_list.$item truncate_value_after=10000}
                                    {/foreach}
                                {/if}
                            {else}
                                {foreach $entry.{$attribute} as $value}
                                    <div class="display_value" data-dn="{$dn}" data-item="{$item}" data-type="{$type}" data-attribute="{$attribute}" data-value="{$value}" >
                                    </div>
                                {/foreach}
                            {/if}
                        </div>
                    </div>
                {/foreach}
                </div>
            </div>

            <div class="card-footer text-center">
                <button type="submit" class="btn btn-success m-1">
                    <i class="fa fa-fw fa-check-square-o"></i> {$msg_submit}
                </button>
                <a href="?page=display&dn={$dn}" class="btn btn-outline-secondary m-1"><i class="fa fa-fw fa-cancel"></i> {$msg_cancelbacktoentry}</a>
            </div>

            </form>

        </div>
    </div>
    <div class="col-md-2"></div>
</div>
