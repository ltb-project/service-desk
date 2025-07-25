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
<script src="vendor/datatables/buttons.colVis.min.js"></script>
<script src="vendor/datatables/buttons.html5.min.js"></script>
<script src="vendor/datatables/buttons.print.min.js"></script>
<script src="vendor/datatables/buttons.bootstrap5.min.js"></script>
<!-- dayjs, from https://github.com/iamkun/dayjs/ MIT LICENSE -->
<script src="js/dayjs.min.js"></script>
<!-- ldap2date, from https://github.com/rsolomo/ldap2date.js/ MIT LICENSE -->
<script src="js/ldap2date.js"></script>
<script src="js/utils.js"></script>
<script src="js/value-renderer.js"></script>
<script src="js/service-desk.js"></script>
<script src="js/ppolicy.js"></script>
{include 'commentbox.js.tpl'}

{if $page|strstr:"search"}
{include 'datatables-search.js.tpl'}
{/if}
{if $page|strstr:"audit"}
{include 'datatables-audit.js.tpl'}
{/if}
{if $page|strstr:"display" || $page|strstr:"update" || $page|strstr:"rename"}
{include 'value-displayer.js.tpl'}
{/if}
{if $page|strstr:"auditlog"}
{include 'auditlog-value-displayer.js.tpl'}
{/if}



{literal}
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
