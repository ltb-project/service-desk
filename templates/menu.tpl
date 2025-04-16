    <div class="mb-3">

        <nav class="navbar navbar-expand-lg bg-body-tertiary" role="navigation">
          <div class="container-fluid">
              <a class="navbar-brand" href="index.php?page=welcome">
                {if $logo}
                <img src="{$logo}" alt="Logo" class="menu-logo img-fluid" />
                {/if}
                {$msg_title}
              </a>
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>

            <div class="navbar-collapse collapse" id="navbarSupportedContent">
              <ul class="nav navbar-nav me-auto mb-2 mb-lg-0">
                {if $use_create}
                <li class="nav-item">
                  <a href="index.php?page=create" class="nav-link"><i class="fa fa-fw fa-circle-plus"></i> {$msg_create}</a>
                </li>
                {/if}
                {if $use_searchlocked or $use_searchdisabled or $use_searchwillexpire or $use_searchexpired or $use_searchidle}
                <li class="nav-item dropdown">
                  <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-fw fa-dashboard"></i> {$msg_dashboards}<span class="caret"></span></a>
                  <ul class="dropdown-menu">
                      {if $use_searchlocked}
                      <li><a href="index.php?page=searchlocked" class="dropdown-item"><i class="fa fa-fw fa-lock"></i> {$msg_lockedaccounts}</a></li>
                      {/if}
                      {if $use_searchdisabled}
                      <li><a href="index.php?page=searchdisabled" class="dropdown-item"><i class="fa fa-fw fa-user-slash"></i> {$msg_disabledaccounts}</a></li>
                      {/if}
                      {if $use_searchwillexpire}
                      <li><a href="index.php?page=searchwillexpire" class="dropdown-item"><i class="fa fa-fw fa-hourglass-half"></i> {$msg_willexpireaccounts}</a></li>
                      {/if}
                      {if $use_searchexpired}
                      <li><a href="index.php?page=searchexpired" class="dropdown-item"><i class="fa fa-fw fa-hourglass-end"></i> {$msg_expiredaccounts}</a></li>
                      {/if}
                      {if $use_searchidle}
                      <li><a href="index.php?page=searchidle" class="dropdown-item"><i class="fa fa-fw fa-hourglass-o"></i> {$msg_idleaccounts}</a></li>
                      {/if}
                      {if $use_searchinvalid}
                      <li><a href="index.php?page=searchinvalid" class="dropdown-item"><i class="fa fa-fw fa-user-xmark"></i> {$msg_invalidaccounts}</a></li>
                      {/if}
                  </ul>
                </li>
                {if $use_showauditlog}
                <li class="nav-item">
                  <a href="index.php?page=auditlog" class="nav-link"><i class="fa fa-fw fa-list"></i> {$msg_auditlogs}</a>
                </li>
                {/if}
                {/if}
                {if $logout_link}
                <li class="nav_item">
                  <a href="{$logout_link}" class="nav-link"><i class="fa fa-fw fa-sign-out"></i> {$msg_logout}</a>
                </li>
                {/if}
              </ul>
              <form class="d-flex" role="search" action="index.php?page=search" method="post">
                <div class="input-group">
                  <input type="text" class="form-control border-secondary" placeholder="{$msg_search}" name="search" value="{$search}" />
                  <button class="btn btn-outline-secondary" type="submit">&nbsp;<i class="fa fa-fw fa-search"></i></button>
                </div>
              </form>
            </div>
          </div>
        </nav>

    </div>
