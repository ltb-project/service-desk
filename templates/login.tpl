<div class="panel panel-info">
<div class="panel-heading text-center">
    <p class="panel-title">
        <i class="fa fa-fw fa-check-circle"></i>
        {$msg_login}
    </p>
</div>

 <div class="panel-body">

     <form id="login" method="post" action="index.php?page=login" name="login">
         {if $autherror neq '' and isset($msg_{$autherror})}
            <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_{$autherror}}</div>
         {elseif $autherror neq ''}
            <div class="alert alert-danger"><i class="fa fa-fw fa-exclamation-triangle"></i> Error: {$autherror}</div>
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