<div class="create row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="card mb-3 shadow">
            <div class="card-header text-bg-secondary text-center">
                <p class="card-title">
                    <i class="fa fa-fw fa-circle-plus"></i>
                    {$msg_createentry}
                </p>
            </div>

            <form method="post">

            <div class="card-body">
                <div class="container-fluid">
                {foreach from=$create_items item=item name=items}
                {$attribute=$attributes_map.{$item}.attribute}
                {$type=$attributes_map.{$item}.type}
                {$faclass=$attributes_map.{$item}.faclass}
                {$multivalued=$attributes_map.{$item}.multivalued}
                {$required=0}
                {if ($attributes_map.{$item}.mandatory|is_array)}
                {if in_array('all',$attributes_map.{$item}.mandatory) or in_array('create',$attributes_map.{$item}.mandatory)}
                {$required=1}
                {/if}
                {/if}

                    <div class="row align-items-center p-2{if $smarty.foreach.items.iteration % 2 == 0} bg-white{/if}" id="create_{$item}">
                        <div class="col-1 px-1">
                            <i class="fa fa-fw fa-{$faclass}"></i>
                        </div>
                        <div class="col-11 col-md-3 fw-semibold px-1">
                            {$msg_label_{$item}}
                            {if $msg_tooltip_{$item}}<span data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="{$msg_tooltip_{$item}}"><i class="fa fa-fw fa-regular fa-circle-question"></i></span>{/if}
                        </div>
                        <div class="col-md px-1">
                            {include 'value_editor.tpl' item=$item itemindex=0 value="" type=$type list=$item_list.$item multivalued=$multivalued required=$required truncate_value_after=10000}
                        </div>
                    </div>
                {/foreach}
                </div>

            </div>

            <div class="card-footer text-center">
                <button type="submit" class="btn btn-success m-1">
                    <i class="fa fa-fw fa-check-square-o"></i> {$msg_submit}
                </button>
            </div>

            <input type="hidden" name="action" value="createentry" />
            </form>

        </div>
    </div>
    <div class="col-md-2"></div>
</div>
