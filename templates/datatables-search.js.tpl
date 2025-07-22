{literal}
    <script type="text/javascript">
      $(document).ready( function() {
{/literal}
    var datatables_params = JSON.parse(atob("{$datatables_params}"));
{literal}

    var itemlist = $('table.dataTable').DataTable({
      serverSide: true,
      processing: true,
      ajax: {
        url: '/index.php',
        type: 'POST',
        data: {
{/literal}
            action: "{$page}",
            search_query: "{$search_query}",
            apiendpoint: "search-api"
{literal}
        }
      },
      // Calling renderer for each cell
      columnDefs: [
          { targets: '_all', render: function ( data, type, row, meta ) {return datatableTypeRenderer(data, type, row, meta, datatables_params);} }
      ],
      drawCallback: function (settings) { updateEntriesCount(settings, datatables_params);},
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
      }
    });
{/literal}
{literal}
        $('table tr.clickable').click(function() {
          document.location.href = $(this).find('[href]').attr('href');
        });
      });
    </script>
{/literal}
