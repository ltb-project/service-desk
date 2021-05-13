    <div class="navbar-wrapper">

        <div class="navbar navbar-default navbar-static-top" role="navigation">
          <div class="container-fluid">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="index.php?page=welcome">
                {if $logo}
                <img src="{$logo}" alt="Logo" class="menu-logo img-responsive" />
                {/if}
                {$msg_title}
              </a>
            </div>
            <div class="navbar-collapse collapse">
              <ul class="nav navbar-nav">
                {if $use_searchlocked or $use_searchwillexpire or $use_searchexpired or $use_searchidle}
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-fw fa-dashboard"></i> {$msg_dashboards}<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                      {if $use_searchlocked}
                      <li><a href="index.php?page=searchlocked"><i class="fa fa-fw fa-lock"></i> {$msg_lockedaccounts}</a></li>
                      {/if}
                      {if $use_searchwillexpire}
                      <li><a href="index.php?page=searchwillexpire"><i class="fa fa-fw fa-hourglass-half"></i> {$msg_willexpireaccounts}</a></li>
                      {/if}
                      {if $use_searchexpired}
                      <li><a href="index.php?page=searchexpired"><i class="fa fa-fw fa-hourglass-end"></i> {$msg_expiredaccounts}</a></li>
                      {/if}
                      {if $use_searchidle}
                      <li><a href="index.php?page=searchidle"><i class="fa fa-fw fa-hourglass-o"></i> {$msg_idleaccounts}</a></li>
                      {/if}
                    </ul>
                  </a>
                </li>
                {/if}
                {if $logout_link}
                <li>
                  <a href="{$logout_link}"><i class="fa fa-fw fa-sign-out"></i> {$msg_logout}</a>
                </li>
                {/if}
              </ul>
              <form class="navbar-form navbar-right" role="search" action="index.php?page=search" method="post">
                <div class="input-group">
                  <input type="text" class="form-control" placeholder="{$msg_search}" name="search" value="{$search}" />
                  <span class="input-group-btn">
                    <button class="btn btn-default" type="submit">&nbsp;<i class="fa fa-fw fa-search"></i></button>
                  </span>
                </div>
              </form>
            </div>
          </div>
        </div>

    </div>
