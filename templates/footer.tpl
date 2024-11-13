</div>

{if $display_footer}
<div id="footer">LDAP Tool Box Service Desk - version {$version}</div>
{/if}

<div id="ltb-component" hidden>sd</div>

<script src="vendor/jquery/js/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/datatables/dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap5.min.js"></script>
<script src="vendor/datatables/dataTables.buttons.min.js"></script>
<script src="vendor/datatables/buttons.bootstrap5.min.js"></script>
<script src="js/service-desk.js"></script>
<script src="js/ppolicy.js"></script>

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
      },
{if $datatables_page_length_default}
      pageLength: {$datatables_page_length_default},
{/if
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
