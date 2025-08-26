{literal}
    <script type="text/javascript">
      $(document).ready( function() {
{/literal}
    var config_js = JSON.parse(atob("{$config_js}"));
    var searchaction = "{$searchaction}";
    var search_query = "{$search_query}";
{literal}

    DataTable.ext.errMode = 'none';

    var itemlist = $('table.dataTable')
    .on('dt-error.dt', function (e, settings, techNote, error) {
        datatableManageError(config_js, error);
    })
    .DataTable({
      serverSide: true,
      processing: true,
      ajax: {
        url: '/index.php',
        type: 'POST',
        data: {
            action: searchaction,
            search_query: search_query,
            apiendpoint: "search-api"
        }
      },
      // Calling renderer for each cell
      columnDefs: [
          { targets: '_all', render: function ( data, type, row, meta ) {return datatableTypeRenderer(data, type, row, meta, config_js);} }
      ],
      drawCallback: function (settings) { updateEntriesCount(settings, config_js, searchaction); redirectWhenOneEntry(settings, config_js, searchaction)},
      layout: {
        topStart: {
{/literal}
{if $datatables_page_length_choices}
          pageLength: {
            menu: [ {$datatables_page_length_choices nofilter} ]
          }
{/if}
        },
        bottom2Start: {
            buttons: [
{if $datatables_print_all}
                { text: '{$msg_print_all}', action: function (e, dt, node, config, cb){ print_all_results(e, dt, node, config, cb, {if $datatables_auto_print}true{else}false{/if});} },
{/if}
{if $datatables_print_page}
                { extend: 'print', text: '{$msg_print_page}', exportOptions: { columns: ':not(.hidden-print)', modifier: { page: 'current' } }, autoPrint: {if $datatables_auto_print}true{else}false{/if} },
{/if}
            ]
        }
      },
{if $datatables_page_length_default}
      pageLength: {$datatables_page_length_default},
{/if}
{literal}
      language: {
        url: "vendor/datatables/i18n/{/literal}{$lang|default:'en'}{literal}.json"
      },
      responsive: true,
    });
    });
    </script>
{/literal}
