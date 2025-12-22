<div class="rename row">

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

                <div class="row align-items-center p-2 text-bg-secondary">
                    <div class="col-1 px-1 d-none d-sm-block"></div>
                    <div class="col-3 px-1 d-none d-sm-block"></div>
                    <div class="col-6 col-sm-4 px-1">{$msg_oldvalue}</div>
                    <div class="col-6 col-sm-4 px-1">{$msg_newvalue}</div>
                </div>

                {foreach from=$card_items item=item name=items}
                {$attribute=$attributes_map.{$item}.attribute}
                {$type=$attributes_map.{$item}.type}
                {$faclass=$attributes_map.{$item}.faclass}

                <div class="row align-items-center p-2{if $smarty.foreach.items.iteration % 2 == 0} bg-white{/if}" id="info_{$item}">
                    <div class="col-1 px-1 d-none d-sm-block">
                        <i class="fa fa-fw fa-{$faclass}"></i>
                    </div>
                    <div class="col-3 fw-semibold px-1 d-none d-sm-block">
                        {$msg_label_{$item}}
                            {if $msg_tooltip_{$item}}<span data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="{$msg_tooltip_{$item}}"><i class="fa fa-fw fa-regular fa-circle-question"></i></span>{/if}
                    </div>
                    <div class="col-6 col-sm-4 px-1">
                        {foreach $entry.{$attribute} as $value}
                        <div class="display_value" data-dn="{$dn}" data-item="{$item}" data-type="{$type}" data-attribute="{$attribute}" data-value="{$value}"></div>
                        {/foreach}
                    </div>
                    <div class="col-6 col-sm-4 px-1">
                        {include 'value_editor.tpl' item=$item value="" type=$type list=$item_list.$item truncate_value_after=10000}
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
