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
                <img src="{$logo}" alt="{$msg_title}" class="menu-logo img-responsive" />
                {$msg_title}
              </a>
            </div>
            <div class="navbar-collapse collapse">
              <ul class="nav navbar-nav">
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
