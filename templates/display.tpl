<div class="row">
    <div class="display col-md-6">

        <div class="panel panel-info">
            <div class="panel-heading text-center">
                <p class="panel-title">
                    <i class="fa fa-fw fa-{$attributes_map.{$card_title}.faclass}"></i>
                    {$entry.{$attributes_map.{$card_title}.attribute}.0}
                </p>
            </div>

            <div class="panel-body">

                <div class="table-responsive">
                <table class="table table-striped table-hover">
                {foreach $card_items as $item}
                {$attribute=$attributes_map.{$item}.attribute}
                {$type=$attributes_map.{$item}.type}
                {$faclass=$attributes_map.{$item}.faclass}

                {if !({$entry.$attribute.0}) && ! $show_undef}
                    {continue}
                {/if}
                    <tr>
                        <th class="text-center">
                            <i class="fa fa-fw fa-{$faclass}"></i>
                        </th>
                        <th class="hidden-xs">
                            {$msg_label_{$item}}
                        </th>
                        <td>
                        {if ({$entry.$attribute.0})}
                            {foreach $entry.{$attribute} as $value}
                            {include 'value_displayer.tpl' value=$value type=$type truncate_value_after=10000}
                            {/foreach}
                        {else}
                            <i>{$msg_notdefined}</i><br />
                        {/if}
                        </td>
                    </tr>
                {/foreach}
                </table>
                </div>

            </div>
        </div>

        <div class="panel panel-info">
            <div class="panel-heading text-center">
                <p class="panel-title">
                    <i class="fa fa-fw fa-info-circle"></i>
                    {$msg_accountstatus}
                </p>
            </div>

            <div class="panel-body">

                <div class="table-responsive">
                <table class="table table-striped table-hover">
                {foreach $password_items as $item}
                {$attribute=$attributes_map.{$item}.attribute}
                {$type=$attributes_map.{$item}.type}
                {$faclass=$attributes_map.{$item}.faclass}

                {if !({$entry.$attribute.0}) && ! $show_undef}
                    {continue}
                {/if}
                    <tr>
                        <th class="col-md-6">
                            {$msg_label_{$item}}
                        </th>
                        <td class="col-md-6">
                        {if ({$entry.$attribute.0})}
                            {foreach $entry.{$attribute} as $value}
                            {include 'value_displayer.tpl' value=$value type=$type truncate_value_after=10000}
                            {/foreach}
                        {else}
                            <i>{$msg_notdefined}</i><br />
                        {/if}
                        </td>
                    </tr>
                {/foreach}
                {if {$display_password_expiration_date} and {$ldapExpirationDate}}
                    <tr>
                        <th class="col-md-6">
                            {$msg_label_expirationdate}
                        </th>
                        <td class="col-md-6">
                            {include 'value_displayer.tpl' value=$ldapExpirationDate type="date" truncate_value_after=10000}
                        </td>
                    </tr>
                {/if}
                </table>
                </div>

            </div>
        </div>

    </div>
    <div class="col-md-6">

        {if $use_checkpassword}
        <div class="panel panel-info">
            <div class="panel-heading text-center">
                <p class="panel-title">
                    <i class="fa fa-fw fa-check-circle"></i>
                    {$msg_checkpassword}
                </p>
            </div>
    
             <div class="panel-body">
    
                 <form id="checkpassword" method="post" action="index.php?page=checkpassword">
                     {if $checkpasswordresult eq 'passwordrequired'}
                     <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_passwordrequired}</div>
                     {/if}
                     {if $checkpasswordresult eq 'ldaperror'}
                     <div class="alert alert-danger"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_passwordinvalid}</div>
                     {/if}
                     {if $checkpasswordresult eq 'passwordok'}
                     <div class="alert alert-success"><i class="fa fa-fw fa-check"></i> {$msg_passwordok}</div>
                     {/if}
                     <input type="hidden" name="dn" value="{$dn}" />
                     <div class="form-group">
                         <div class="input-group">
                             <span class="input-group-addon"><i class="fa fa-fw fa-lock"></i></span>
                             <input type="password" name="currentpassword" id="currentpassword" autocomplete="current-password" class="form-control" placeholder="{$msg_currentpassword}" />
                         </div>
                     </div>
                     <div class="form-group">
                         <button type="submit" class="btn btn-success">
                             <i class="fa fa-fw fa-check-square-o"></i> {$msg_submit}
                         </button>
                     </div>
                </form>
            </div>
        </div>
        {/if}

        {if $use_resetpassword}
        <div class="panel panel-info">
            <div class="panel-heading text-center">
                <p class="panel-title">
                    <i class="fa fa-fw fa-repeat"></i>
                    {$msg_resetpassword}
                </p>
            </div>

             <div class="panel-body">

                 <form id="resetpassword" method="post" action="index.php?page=resetpassword">
                     {if $resetpasswordresult eq 'passwordrequired'}
                     <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_passwordrequired}</div>
                     {/if}
                     {if $resetpasswordresult eq 'passwordrefused'}
                     <div class="alert alert-danger"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_passwordrefused}</div>
                     {/if}
                     {if $resetpasswordresult eq 'passwordchanged'}
                     <div class="alert alert-success"><i class="fa fa-fw fa-check"></i> {$msg_passwordchanged}</div>
                     {/if}
                     {if $posthookresult}
                     <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$posthookresult}</div>
                     {/if}
                     <input type="hidden" name="dn" value="{$dn}" />
                     <div class="form-group">
                         <div class="input-group">
                             <span class="input-group-addon"><i class="fa fa-fw fa-lock"></i></span>
                             <input type="password" name="newpassword" id="newpassword" autocomplete="new-password" class="form-control" placeholder="{$msg_newpassword}" />
                         </div>
                     </div>
                     {if $use_resetpassword_resetchoice}
                     <div class="form-group row">
                         <div class="col-md-9"><p>{$msg_forcereset}</p></div>
                         <div class="col-md-3 text-right">
                             <div class="btn-group" data-toggle="buttons">
                                 <label class="btn btn-primary{if $resetpassword_reset_default} active{/if}">
                                 {if $resetpassword_reset_default}
                                     <input type="radio" name="pwdreset" id="true" value="true" checked /> {$msg_true}
                                 {else}
                                     <input type="radio" name="pwdreset" id="true" value="true" /> {$msg_true}
                                 {/if}
                                 </label>
                                 <label class="btn btn-primary{if !$resetpassword_reset_default} active{/if}">
                                 {if !$resetpassword_reset_default}
                                     <input type="radio" name="pwdreset" id="false" value="false" checked /> {$msg_false}
                                 {else}
                                     <input type="radio" name="pwdreset" id="false" value="false" /> {$msg_false}
                                 {/if}
                                 </label>
                             </div>
                         </div>
                     </div>
                     {else}
                         {if $resetpassword_reset_default}
                         <input type="hidden" name="pwdreset" value="true" />
                         {else}
                         <input type="hidden" name="pwdreset" value="false" />
                         {/if}
                     {/if}
                     <div class="form-group">
                         <button type="submit" class="btn btn-success">
                             <i class="fa fa-fw fa-check-square-o"></i> {$msg_submit}
                         </button>
                     </div>
                </form>
            </div>
        </div>
        {/if}

        {if $isLocked}
        <div class="panel panel-danger">
            <div class="panel-heading text-center">
                <p class="panel-title">
                    <i class="fa fa-fw fa-exclamation-triangle"></i>
                    {$msg_accountlocked}
                </p>
            </div>

             <div class="panel-body">
                 {if $unlockDate}
                 <p>{$msg_unlockdate} {$unlockDate|date_format:{$date_specifiers}}</p>
                 {/if}
                 {if $use_unlockaccount}
                 <form id="unlockaccount" method="post" action="index.php?page=unlockaccount">
                     {if $unlockaccountresult eq 'ldaperror'}
                     <div class="alert alert-danger"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_accountnotunlocked}</div>
                     {/if}
                     <input type="hidden" name="dn" value="{$dn}" />
                     <div class="form-group">
                         <button type="submit" class="btn btn-success">
                             <i class="fa fa-fw fa-unlock"></i> {$msg_unlockaccount}
                         </button>
                     </div>
                 </form>
                 {/if}
            </div>
        </div>
        {/if}

        {if !$isLocked}
        <div class="panel panel-success">
            <div class="panel-heading text-center">
                <p class="panel-title">
                    <i class="fa fa-fw fa-check-square-o"></i>
                    {$msg_accountunlocked}
                </p>
            </div>

             {if $use_lockaccount}
             <div class="panel-body">
                 <form id="lockaccount" method="post" action="index.php?page=lockaccount">
                     {if $lockaccountresult eq 'ldaperror'}
                     <div class="alert alert-danger"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_accountnotlocked}</div>
                     {/if}
                     <input type="hidden" name="dn" value="{$dn}" />
                     <div class="form-group">
                         <button type="submit" class="btn btn-success">
                             <i class="fa fa-fw fa-lock"></i> {$msg_lockaccount}
                         </button>
                     </div>
                 </form>
            </div>
            {/if}
        </div>
        {/if}

        {if $isExpired}
        <div class="panel panel-danger">
            <div class="panel-heading text-center">
                <p class="panel-title">
                    <i class="fa fa-fw fa-exclamation-triangle"></i>
                    {$msg_passwordexpired}
                </p>
            </div>
        </div>
        {/if}
   </div>
</div>
