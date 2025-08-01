<div class="row">

    {if $updateresult eq 'updateok'}
    <div class="container">
        <div class="alert alert-success"><i class="fa fa-fw fa-check"></i> {$msg_updateok}</div>
    </div>
    {/if}

    {if $renameresult eq 'renameok'}
    <div class="container">
        <div class="alert alert-success"><i class="fa fa-fw fa-check"></i> {$msg_renameok}</div>
    </div>
    {/if}

    {if $createresult eq 'createok'}
    <div class="container">
        <div class="alert alert-success"><i class="fa fa-fw fa-check"></i> {$msg_createok}</div>
    </div>
    {/if}

    {if $prehookdeleteresult}
    <div class="container">
        <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$prehookdeleteresult}</div>
    </div>
    {/if}

    {if $posthookdeleteresult}
    <div class="container">
        <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$posthookdeleteresult}</div>
    </div>
    {/if}

    <div class="display col-md-6">

        <div class="card mb-3 shadow">
            <div class="card-header text-bg-secondary text-center">
                <p class="card-title">
                    <i class="fa fa-fw fa-{$attributes_map.{$card_title}.faclass}"></i>
                    {$entry.{$attributes_map.{$card_title}.attribute}.0}
                </p>
            </div>

            <div class="card-body">

                <div class="table-responsive">
                <table id="user_attributes" class="table table-striped table-hover" data-dn="{$dn}" >
                {foreach $card_items as $item}
                {$attribute=$attributes_map.{$item}.attribute}
                {$type=$attributes_map.{$item}.type}
                {$faclass=$attributes_map.{$item}.faclass}

                    <tr id="info_{$item}" data-item="{$item}" data-type="{$type}" data-attribute="{$attribute}" data-faclass="{$faclass}">
                    </tr>
                {/foreach}
                </table>
                </div>
            </div>

            {if $edit_link || $rename_link || $delete_link}
            <div class="card-footer text-center">
                {if $edit_link}
                <a class="btn btn-success" href="{$edit_link}"><i class="fa fa-edit"></i> {$msg_editentry}</a>
                {/if}
                {if $rename_link}
                <a class="btn btn-success" href="{$rename_link}"><i class="fa fa-user-pen"></i> {$msg_renameentry}</a>
                {/if}
                {if $delete_link}
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#delete{$dn|sha256}">
                  <i class="fa fa-user-minus"></i> {$msg_deleteentry}
                </button>
                 {include 'deletemodal.tpl' dn={$dn}}
                {/if}
            </div>
            {/if}

        </div>

        <div class="card mb-3 shadow ">
            <div class="card-header text-bg-secondary text-center">
                <p class="card-title">
                    <i class="fa fa-fw fa-info-circle"></i>
                    {$msg_accountstatus}
                </p>
            </div>

            <div class="card-body">

                <div class="table-responsive">
                <table id="status_attributes" class="table table-striped table-hover">
                {foreach $password_items as $item}
                {$attribute=$attributes_map.{$item}.attribute}
                {$type=$attributes_map.{$item}.type}
                {$faclass=$attributes_map.{$item}.faclass}
                    <tr id="status_{$item}" data-item="{$item}" data-type="{$type}" data-attribute="{$attribute}" data-faclass="{$faclass}">
                    </tr>
                {/foreach}
                {if $lockDate}
                    <tr>
                        <th class="col-md-6">
                            {$msg_label_pwdaccountlockedtime}
                        </th>
                        <td class="col-md-6">
                            {$lockDate|date_format:{$date_specifiers}|truncate:10000}
                        </td>
                    </tr>
                {/if}
                {if {$display_password_expiration_date} and {$ldapExpirationDate}}
                    <tr>
                        <th class="col-md-6">
                            {$msg_label_expirationdate}
                        </th>
                        <td class="col-md-6">
                            {$ldapExpirationDate|date_format:{$date_specifiers}|truncate:10000}
                        </td>
                    </tr>
                {/if}
                {if $resetAtNextConnection}
                    <tr>
                        <th class="col-md-6">
                            {$msg_label_pwdreset}
                        </th>
                        <td class="col-md-6">
                            {$msg_true}
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
        <div class="card mb-3 shadow ">
            <div class="card-header text-bg-secondary text-center">
                <p class="card-title">
                    <i class="fa fa-fw fa-check-circle"></i>
                    {$msg_checkpassword}
                </p>
            </div>
    
             <div class="card-body">
    
                 <form id="checkpassword" method="post" action="index.php?page=checkpassword">
                     {if $checkpasswordresult eq 'passwordrequired'}
                     <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_passwordrequired}</div>
                     {/if}
                     {if $checkpasswordresult eq 'passwordinvalid'}
                     <div class="alert alert-danger"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_passwordinvalid}</div>
                     {/if}
                     {if $checkpasswordresult eq 'passwordok'}
                     <div class="alert alert-success"><i class="fa fa-fw fa-check"></i> {$msg_passwordok}</div>
                     {/if}
                     {if $checkpasswordresult eq 'passwordinhistory'}
                     <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_passwordinhistory}</div>
                     {/if}
                     <input type="hidden" name="dn" value="{$dn}" />
                     <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa fa-fw fa-lock"></i></span>
                        {if $fake_password_inputs}
                            <input type="text" name="currentpassword" id="currentpassword" autocomplete="current-password" class="form-control fake-password" placeholder="{$msg_currentpassword}" />
                        {else}
                            <input type="password" name="currentpassword" id="currentpassword" autocomplete="current-password" class="form-control" placeholder="{$msg_currentpassword}" />
                        {/if}
                     </div>
                     <button type="submit" class="btn btn-success">
                        <i class="fa fa-fw fa-check-square-o"></i> {$msg_submit}
                     </button>
                </form>
            </div>
        </div>
        {/if}

        {if $use_resetpassword}
        <div class="card mb-3 shadow">
            <div class="card-header text-bg-secondary text-center">
                <p class="card-title">
                    <i class="fa fa-fw fa-repeat"></i>
                    {$msg_resetpassword}
                </p>
            </div>

             <div class="card-body">

                 <form id="resetpassword" method="post" action="index.php?page=resetpassword">
                     {if $resetpasswordresult eq 'passwordrequired'}
                     <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_passwordrequired}</div>
                     {elseif $resetpasswordresult eq 'passwordrefused'}
                     <div class="alert alert-danger"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_passwordrefused}</div>
                     {elseif $resetpasswordresult eq 'passwordchanged'}
                     <div class="alert alert-success"><i class="fa fa-fw fa-check"></i> {$msg_passwordchanged}</div>
                     {elseif $resetpasswordresult eq ''}
                     {else}
                     <div class="alert alert-danger"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_resetpasswordresult}</div>
                     {/if}
                     {if $prehookresult}
                     <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$prehookresult}</div>
                     {/if}
                     {if $posthookresult}
                     <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$posthookresult}</div>
                     {/if}
                     {if $pwd_show_policy !== "never" and $pwd_show_policy_pos === 'above'}
                        {include file="policy.tpl"}
                     {/if}
                     <input type="hidden" name="dn" value="{$dn}" />
                     <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa fa-fw fa-lock"></i></span>
                        {if $fake_password_inputs}
                           <input type="text" name="newpassword" id="newpassword" autocomplete="new-password" class="form-control fake-password" placeholder="{$msg_newpassword}" />
                        {else}
                           <input type="password" name="newpassword" id="newpassword" autocomplete="new-password" class="form-control" placeholder="{$msg_newpassword}" />
                        {/if}
                     </div>
                     {if $use_resetpassword_resetchoice}
                     <div class="form-check form-switch mb-3">
                       <input class="form-check-input" type="checkbox" role="switch" name="pwdreset" id="pwdresetcheckbox"{if $resetpassword_reset_default} checked{/if} value="true" >
                       <label class="form-check-label" for="pwdresetcheckbox">{$msg_forcereset}</label>
                     </div>
                     {else}
                         {if $resetpassword_reset_default}
                         <input type="hidden" name="pwdreset" value="true" />
                         {else}
                         <input type="hidden" name="pwdreset" value="false" />
                         {/if}
                     {/if}
                     <button type="submit" class="btn btn-success">
                        <i class="fa fa-fw fa-check-square-o"></i> {$msg_submit}
                     </button>
                     {if $pwd_show_policy !== "never" and $pwd_show_policy_pos === 'below'}
                        {include file="policy.tpl"}
                     {/if}
                </form>
            </div>
        </div>
        {/if}

        {if $show_lockstatus}
        {if $isLocked}
        <div class="card mb-3 shadow border-danger">
            <div class="card-header text-bg-danger text-center">
                <p class="card-title">
                    <i class="fa fa-fw fa-exclamation-triangle"></i>
                    {$msg_accountlocked}
                </p>
            </div>

             <div class="card-body">
                {if $prehooklockresult}
                <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$prehooklockresult}</div>
                {/if}
                {if $posthooklockresult}
                <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$posthooklockresult}</div>
                {/if}
                {if $prehookunlockresult}
                <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$prehookunlockresult}</div>
                {/if}
                {if $posthookunlockresult}
                <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$posthookunlockresult}</div>
                {/if}
                {if $unlockDate}
                <p>{$msg_unlockdate} {$unlockDate|date_format:{$date_specifiers}}</p>
                {/if}
                {if $use_unlockaccount}
                {if $unlockaccountresult eq 'ldaperror'}
                <div class="alert alert-danger"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_accountnotunlocked}</div>
                {/if}
                {if $use_unlockcomment}
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#commentModalunlock{$dn|sha256}">
                        <i class="fa fa-fw fa-unlock me-2"></i>{$msg_unlockaccount}
                        <i class="fa fa-fw fa-info-circle text-body-tertiary ms-2" title="{$msg_comment_needed}"></i>
                    </button>
                    <div id="unlockcommentbox">
                    </div>
                {else}
                    <form id="unlockaccount" method="post" action="index.php?page=unlockaccount">
                    <input type="hidden" name="dn" value="{$dn}" />
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-fw fa-unlock"></i> {$msg_unlockaccount}
                        </button>
                    </form>
                {/if}
                {/if}
            </div>
        </div>
        {/if}

        {if !$isLocked}
        <div class="card mb-3 shadow border-success">
            <div class="card-header text-bg-success text-center">
                <p class="card-title">
                    <i class="fa fa-fw fa-check-square-o"></i>
                    {$msg_accountunlocked}
                </p>
            </div>

            {if $use_lockaccount || $prehooklockresult || $posthooklockresult || $prehookunlockresult || $posthookunlockresult}
            <div class="card-body">
                {if $prehooklockresult}
                <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$prehooklockresult}</div>
                {/if}
                {if $posthooklockresult}
                <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$posthooklockresult}</div>
                {/if}
                {if $prehookunlockresult}
                <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$prehookunlockresult}</div>
                {/if}
                {if $posthookunlockresult}
                <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$posthookunlockresult}</div>
                {/if}
                {if $lockaccountresult eq 'ldaperror'}
                    <div class="alert alert-danger"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_accountnotlocked}</div>
                {/if}
                {if $use_lockcomment}
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#commentModallock{$dn|sha256}">
                        <i class="fa fa-fw fa-lock me-2"></i>{$msg_lockaccount}
                        <i class="fa fa-fw fa-info-circle text-body-tertiary ms-2" title="{$msg_comment_needed}"></i>
                    </button>
                    <div id="lockcommentbox">
                    </div>
                {else}
                    <form id="lockaccount" method="post" action="index.php?page=lockaccount">
                    <input type="hidden" name="dn" value="{$dn}" />
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-fw fa-lock"></i> {$msg_lockaccount}
                        </button>
                    </form>
                {/if}
            </div>
            {/if}
        </div>
        {/if}
        {/if}

        {if $show_expirestatus}
        {if $isExpired}
        <div class="card mb-3 shadow border-danger">
            <div class="card-header text-bg-danger text-center">
                <p class="card-title">
                    <i class="fa fa-fw fa-exclamation-triangle"></i>
                    {$msg_passwordexpired}
                </p>
            </div>
        </div>
        {/if}
        {/if}

        {if $show_enablestatus}
        {if $isAccountEnabled}
        <div class="card mb-3 shadow border-success">
            <div class="card-header text-bg-success text-center">
                <p class="card-title">
                    <i class="fa fa-fw fa-check-square-o"></i>
                    {$msg_accountenabled}
                </p>
            </div>
            {if $use_disableaccount || $prehookenableresult || $posthookenableresult || $prehookdisableresult || $posthookdisableresult}
            <div class="card-body">
                {if $disableaccountresult eq 'ldaperror' or $disableaccountresult eq 'actionforbidden'}
                <div class="alert alert-danger"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_accountnotdisabled}</div>
                {/if}
                {if $prehookenableresult}
                <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$prehookenableresult}</div>
                {/if}
                {if $posthookenableresult}
                <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$posthookenableresult}</div>
                {/if}
                {if $prehookdisableresult}
                <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$prehookdisableresult}</div>
                {/if}
                {if $posthookdisableresult}
                <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$posthookdisableresult}</div>
                {/if}
                {if $use_disablecomment}
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#commentModaldisable{$dn|sha256}">
                        <i class="fa fa-fw fa-user-slash me-2"></i>{$msg_disableaccount}
                        <i class="fa fa-fw fa-info-circle text-body-tertiary ms-2" title="{$msg_comment_needed}"></i>
                    </button>
                    <div id="disablecommentbox">
                    </div>
                {else}
                    <form id="disableaccount" method="post" action="index.php?page=disableaccount">
                        <input type="hidden" name="dn" value="{$dn}" />
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-fw fa-user-slash"></i> {$msg_disableaccount}
                        </button>
                    </form>
                {/if}
            </div>
            {/if}
        </div>
        {else}
        <div class="card mb-3 shadow border-danger">
            <div class="card-header text-bg-danger text-center">
                <p class="card-title">
                    <i class="fa fa-fw fa-exclamation-triangle"></i>
                    {$msg_accountdisabled}
                </p>
            </div>
            {if $use_enableaccount || $prehookenableresult || $posthookenableresult || $prehookdisableresult || $posthookdisableresult}
            <div class="card-body">
                {if $enableaccountresult eq 'ldaperror' or $enableaccountresult eq 'actionforbidden'}
                <div class="alert alert-danger"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_accountnotenabled}</div>
                {/if}
                {if $prehookenableresult}
                <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$prehookenableresult}</div>
                {/if}
                {if $posthookenableresult}
                <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$posthookenableresult}</div>
                {/if}
                {if $prehookdisableresult}
                <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$prehookdisableresult}</div>
                {/if}
                {if $posthookdisableresult}
                <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$posthookdisableresult}</div>
                {/if}
                {if $use_enablecomment}
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#commentModalenable{$dn|sha256}">
                        <i class="fa fa-fw fa-user-check me-2"></i>{$msg_enableaccount}
                        <i class="fa fa-fw fa-info-circle text-body-tertiary ms-2" title="{$msg_comment_needed}"></i>
                    </button>
                    <div id="enablecommentbox">
                    </div>
                {else}
                    <form id="disableaccount" method="post" action="index.php?page=enableaccount">
                        <input type="hidden" name="dn" value="{$dn}" />
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-fw fa-user-check"></i> {$msg_enableaccount}
                        </button>
                    </form>
                {/if}
            </div>
            {/if}
        </div>
        {/if}
        {/if}

        {if $show_validitystatus}
        <div class="card mb-3 shadow border-{if $isAccountValid}success{else}danger{/if}">
            <div class="card-header text-bg-{if $isAccountValid}success{else}danger{/if} text-center">
                <p class="card-title">
                    <i class="fa fa-fw fa-{if $isAccountValid}check-square-o{else}exclamation-triangle{/if}"></i>
                    {if $isAccountValid}{$msg_accountvalid}{else}{$msg_accountnotvalid}{/if}
                </p>
            </div>
            {if $use_updatestarttime || $use_updateendtime || $prehookupdatevalidityresult || $posthookupdatevalidityresult}
            <div class="card-body">
                {if $updatevaliditydatesresult eq 'ldaperror' or $updatevaliditydatesresult eq 'actionforbidden'}
                <div class="alert alert-danger"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_validitydatesnotupdated}</div>
                {/if}
                {if $updatevaliditydatesresult eq 'validiydatesupdated'}
                <div class="alert alert-success"><i class="fa fa-fw fa-check"></i> {$msg_validitydatesupdated}</div>
                {/if}
                {if $prehookupdatevalidityresult}
                <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$prehookupdatevalidityresult}</div>
                {/if}
                {if $posthookupdatevalidityresult}
                <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$posthookupdatevalidityresult}</div>
                {/if}
                <form id="updatevaliditydates" method="post" action="index.php?page=updatevaliditydates" class="row g-3">
                        <input type="hidden" name="dn" value="{$dn}" />
                        {if $use_updatestarttime}
                        <div class="col-md-6">
                            <label for="startDate" class="form-label">{$msg_startdate}</label>
                            <input type="date" class="form-control" id="startDate" name="start_date" value="{$startDate|date_format:"%Y-%m-%d"}"/>
                        </div>
                        {/if}
                        {if $use_updateendtime}
                        <div class="col-md-6">
                            <label for="endDate" class="form-label">{$msg_enddate}</label>
                            <input type="date" class="form-control" id="endDate" name="end_date" value="{$endDate|date_format:"%Y-%m-%d"}"/>
                        </div>
                        {/if}
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-fw fa-calendar-check"></i> {$msg_updatevaliditydates}
                        </button>
                </form>
            </div>
            {/if}
        </div>
        {/if}

   </div>
</div>
