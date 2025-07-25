{literal}
    <script type="text/javascript">
      $(document).ready( function() {
{/literal}
{literal}
    var itemlist = $('table.dataTable').DataTable({
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
                { extend: 'print', text: '{$msg_print_all}', exportOptions: { columns: ':not(.hidden-print)' }, autoPrint: {if $datatables_auto_print}true{else}false{/if} },
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
