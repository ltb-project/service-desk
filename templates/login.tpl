<div class="panel panel-info">
<div class="panel-heading text-center">
    <p class="panel-title">
        <i class="fa fa-fw fa-check-circle"></i>
        {$msg_login}
    </p>
</div>

 <div class="panel-body">

     <form id="login" method="post" action="index.php?page=login" name="login">
         {if $autherror eq 'usernotfound'}
            <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_usernotfound}</div>
         {/if}
         {if $autherror eq 'passwordrefused'}
            <div class="alert alert-danger"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_passwordrefused}</div>
         {/if}
         {if $autherror eq 'usernotallowed'}
            <div class="alert alert-danger"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_usernotallowed}</div>
         {/if}
         {if $autherror eq 'usernamerequired'}
            <div class="alert alert-danger"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_usernamerequired}</div>
         {/if}
         {if $autherror eq 'passwordrequired'}
            <div class="alert alert-danger"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_passwordrequired}</div>
         {/if}
         {if $autherror neq 'passwordrefused' 
         and $autherror neq 'passwordrequired' 
         and $autherror neq 'usernotfound' 
         and $autherror neq 'usernamerequired'
         and $autherror neq 'usernotallowed'
         and $autherror neq ''}
            <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> There was an error: {$autherror}</div>
         {/if}
         <div class="form-group">
             <div class="input-group">
                 <span class="input-group-addon"><i class="fa fa-fw fa-user"></i></span>
                <input type="username" name="username" id="username" class="form-control" placeholder="{$msg_username}" />
             </div>
         </div>
         <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-fw fa-lock"></i></span>
                <input type="password" name="password" id="password" class="form-control" placeholder="{$msg_password}" />
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