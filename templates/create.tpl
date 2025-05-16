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
                <div class="table-responsive">
                <table class="table table-striped table-hover">
                {foreach $create_items as $item}
                {$attribute=$attributes_map.{$item}.attribute}
                {$type=$attributes_map.{$item}.type}
                {$faclass=$attributes_map.{$item}.faclass}
                {$multivalued=$attributes_map.{$item}.multivalued}

                    <tr id="create_{$item}">
                        <th class="text-center">
                            <i class="fa fa-fw fa-{$faclass}"></i>
                        </th>
                        <th class="d-none d-sm-table-cell">
                            {$msg_label_{$item}}
                        </th>
                        <td>
                            {include 'value_editor.tpl' item=$item itemindex=0 value="" type=$type list=$item_list.$item multivalued=$multivalued truncate_value_after=10000}
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
                </div>
            </div>

            <input type="hidden" name="action" value="createentry" />
            </form>

        </div>
    </div>
    <div class="col-md-2"></div>
</div>
