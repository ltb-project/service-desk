</div>

{if $display_footer}
<div id="footer">LDAP Tool Box Service Desk - version {$version}</div>
{/if}

<script src="vendor/jquery/js/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/datatables/dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap5.min.js"></script>
<script src="vendor/datatables/dataTables.buttons.min.js"></script>
<script src="vendor/datatables/buttons.bootstrap5.min.js"></script>

{literal}
    <script type="text/javascript">
      $(document).ready( function() {
{/literal}
{literal}
    var itemlist = $('table.dataTable').DataTable({
      "stateSave":    true,
      "searching":    true,
      "paging":       true,
      "info":         true,
      "processing":   true,
{/literal}
{if $datatables_page_length_choices}
      "lengthMenu": [
          [ {$datatables_page_length_choices} ],
          [ {$datatables_page_length_choices|replace:'-1':($msg_pager_all|string_format:'"%s"') nofilter} ]
      ],
{/if}
{if $datatables_page_length_default}
      "pageLength": {$datatables_page_length_default},
{/if}
{literal}
      "dom":
        "<'row ft-head'<'col-sm-3'{/literal}{if $datatables_page_length_choices}l{/if}{literal}><'col-sm-3'f><'col-sm-6'p>>" +
        "<'row dt-main'<'col-sm-12'tr>>" +
        "<'row dt-foot'<'col-sm-6'i><'col-sm-6'p>>" +
        "<'row dt-foot'<'col-sm-12'B>>",
      /* "buttons": [
        { extend: 'print', autoPrint: {/literal}{if $datatables_auto_print}true{else}false{/if}{literal}, text: "{/literal}<i class=\"fa fa-print\"></i> {$msg_print_all}{literal}", className: "btn-info" },
        { extend: 'print', autoPrint: {/literal}{if $datatables_auto_print}true{else}false{/if}{literal}, exportOptions: {modifier:{page: 'current'}}, text: "{/literal}<i class=\"fa fa-print\"></i> {$msg_print_page}{literal}", className: "btn-info" },
      ],*/
      "order": [
        [ {/literal}{if is_int($listing_sortby)}{$listing_sortby}{else}1{/if}{literal}, "asc" ]
      ],
      "aoColumnDefs": [
        { "bSortable": false, "aTargets": ['nosort'] },
      ],
      "language": {
        "url": "vendor/datatables/i18n/{/literal}{$lang|default:'en'}{literal}.json"
      }
    });
{/literal}
{literal}
        $('table tr.clickable').click(function() {
          document.location.href = $(this).find('[href]').attr('href');
        });
      });
    </script>
    <script>
    $(document).ready(function(){
        $('[data-toggle="popover"]').popover({
            trigger: 'hover',
            placement: 'bottom',
            container: 'body'
        });
    });
    </script>
{/literal}

</body>
</html>
