<div class="row">
    <div class="update">

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
                <div class="table-responsive">
                <table class="table table-striped table-hover">
                {foreach $card_items as $item}
                {$attribute=$attributes_map.{$item}.attribute}
                {$type=$attributes_map.{$item}.type}
                {$faclass=$attributes_map.{$item}.faclass}

                {if !({$entry.$attribute.0}) && ! $item|in_array:$update_items}
                    {continue}
                {/if}

                    <tr id="info_{$item}">
                        <th class="text-center">
                            <i class="fa fa-fw fa-{$faclass}"></i>
                        </th>
                        <th class="hidden-xs">
                            {$msg_label_{$item}}
                        </th>
                        <td>
                            {if $item|in_array:$update_items}
                                {if !({$entry.$attribute.0})}
                                {include 'value_editor.tpl' item=$item value="" type=$type truncate_value_after=10000}
                                {else}
                                    {foreach $entry.{$attribute} as $value}
                                        {include 'value_editor.tpl' item=$item value=$value type=$type truncate_value_after=10000}
                                    {/foreach}
                                {/if}
                            {else}
                                {foreach $entry.{$attribute} as $value}
                                    {include 'value_displayer.tpl' value=$value type=$type truncate_value_after=10000}
                                {/foreach}
                            {/if}
                        </td>
                    </tr>
                {/foreach}
                </table>
                </div>


            </div>

            <div class="card-footer text-center">
                <div class="d-grid gap-2 col-md-4 mx-auto">
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-fw fa-check-square-o"></i> {$msg_submit}
                </button>
                <a href="?page=display&dn={$dn}" class="btn btn-secondary"><i class="fa fa-fw fa-cancel"></i> {$msg_cancelbacktoentry}</a>
                </div>
            </div>

            </form>

        </div>

   </div>
</div>
