<?php echo $header; ?>
<div  id="dashboard-container" class="container">
  <aside  id="left-sidebar"
          ng-controller="LeftSidebarController as leftSidebar"
          ng-mouseover="leftSidebar.logoHover()"
          ng-mouseleave="leftSidebar.removeLogoHover()">
    <!-- Instagram Accounts -->
    <div class="instagram-accounts">
<?php
      if ($instagram_accounts) {
        if (count($instagram_accounts) > 0) {
          if (count($instagram_accounts) >= 10) {
?>
      <div id="search-accounts">
        <i class="fa fa-search"></i>
        <input  type="text" placeholder="<?php echo $text_search_accounts; ?>" 
                ng-change="leftSidebar.searchAccounts(searchQuery)"
                ng-model="searchQuery">
      </div>
<?php
          }
          foreach ($instagram_accounts as $account) {
            $disabled = '';
            if ($account['status'] == 'started') {
              $status = $account['status'];
            } elseif ($account['status'] == 'stopped') {
              switch ($account['status_message']) {
                case 'invalid_token':
                  $status = 'reconnect';
                  $disabled = 'data-false="true"';
                  break;
                case 'temp_block':
                case 'temp_ban':
                  $status = 'stopped';
                  break;
                case 'no_activity':
                  $status = 'disabled';
                  $disabled = 'data-false="true"';
                  break;
                case 'expired':
                  $status = 'expired';
                  $disabled = 'data-false="true"';
                  break;
                default:
                  $status = $account['status'];
                  break;
              }
            } elseif ($account['status'] == 'sleeping') {
              $status = $account['status'];
            } elseif ($account['status'] == 'kickoff') {
              $status = $account['status'];
              $disabled = 'data-false="true"';
            }
?>
        <div  class="sidebar-item image-item account-item"
              data-id="<?php echo $account['account_id']; ?>"
              ng-click="leftSidebar.setCurrent($event)"
              <?php echo $disabled; ?>
              >
          <div class="account-img">
            <div class="account-status 
              <?php echo $status; ?> ">
            </div>
            <?php if ($account['image']) { ?>
              <img src="<?php echo $account['image']; ?>" />
            <?php } ?>
          </div>
          <div class="sidebar-item-title">
            <span data-username="<?php echo $account['username']; ?>">
              <?php 
                if (strlen($account['username']) < 15) {
                  echo $account['username'];
                } else {
                  echo substr($account['username'],0,14).'...';
                }
              ?>
              <br />
              <small>
                <?php echo $column_account; ?>
              </small>
            </span>
          </div>
          <div  id ="<?php echo $account['account_id']; ?>"
                class="account-square-status <?php echo $status; ?>"
                data-action="<?php echo $status; ?>"
                ng-controller="TooltipsController as tooltip"
                ng-click="leftSidebar.changeAccountStatus($event,'<?php echo $account['account_id']; ?>')"
                ng-mouseover="tooltip.displayTooltip($event)"
                ng-mouseleave="tooltip.hideTooltip($event)"
                data-tooltip="<?php echo $account['tooltip']?>"
                data-tooltip-position="right"
                data-tooltip-style="dark"
                data-hide-fire="mouseleave">
            <i  class="status-icon fa"></i>
          </div>
        </div>
<?php
          }
        }
      } else {
?>

        <div  class="sidebar-item image-item account-item current-account hide"
              data-id="">
          <div class="account-img">
            <div class="account-status stopped">
            </div>
          </div>
          <div class="sidebar-item-title">
            <span data-username="">
              <br />
              <small>
                <?php echo $column_account; ?>
              </small>
            </span>
          </div>
            <div  id =""
                  class="account-square-status stopped"
                  data-action=""
                  ng-click="leftSidebar.changeAccountStatus($event,'')">
              <i  class="status-icon fa"></i>
            </div>
        </div>
<?php
      }
    ?>
    </div>    <!-- /Instagram Accounts -->
    <!-- Add account Button -->
    <div class="sidebar-item btn-group" role="group" id="add-account">
      <button class="btn btn-primary btn-lg" ng-click="leftSidebar.addAccountModal()">
        <div class="icon-left">
          <i class="fa fa-plus-circle"></i>
        </div>
        <div class="sidebar-item-title">
          <span>
            <?php echo $button_add_account; ?>
          </span>
        </div>
      </button>
    </div>    <!-- /Add account Button -->
    <!-- Bottom Left Sidebar -->
    <div id="bottom-left-sidebar">
      <h4 id="customer-name">          
        <?php echo $customer_name; ?>
      </h4>
      <nav>
        <a href="<?php echo $link_profile; ?>" id="profile-link">
          <div class="sidebar-item">
            <i class="fa fa-user"></i>
            <h4><?php echo $text_profile; ?></h4>
          </div>
        </a>
        <a href="<?php echo $link_help; ?>" target="_blank">
          <div class="sidebar-item">
            <i class="fa fa-question"></i>
            <h4><?php echo $text_help; ?></h4>
          </div>
        </a>
        <a href="<?php echo $link_logout; ?>" id="logout-link">
          <div class="sidebar-item">
            <i class="fa fa-power-off"></i>
            <h4><?php echo $text_logout; ?></h4>
          </div>
        </a>
      </nav>
    </div>    <!-- /Bottom Left Sidebar -->
  </aside>

  <div class="row" id="main-container">

  <?php
    if (count($instagram_accounts) == 0) {
  ?>
    
    <div id="content">
      <div class="tab-header" id="tab-header-middle">
        <h4 class="current-tab dashboard" data-tab="dashboard">
          <?php echo $panel_title_dashboard; ?>
        </h4>
      </div>
      <div class="tab-panel-container middle-tab" id="tab-body-middle">
        <div class="tab current-tab" id="dashboard">
          <div class="row">
            <div class="card cartoon-card">
              <div class="card-content row">
                <img src="catalog/view/theme/default/image/toons/guy-yellowinstagram.png">
                <h3>
                  <?php echo $add_account_title; ?>
                </h3>
                <p>
                  <?php echo $add_first_account_text; ?>
                </p>
                <button   class="gold-button"
                          ng-controller="LeftSidebarController as leftsidebar"
                          ng-click="leftsidebar.addAccountModal()">
                  <?php echo $add_account_button; ?>
                </button>
                <span id="tutorial-button"
                      class="tutorial-popup"
                      ng-controller="ModalsController as modals"
                      ng-click="modals.videoModal()">
                  <?php echo $text_video_tutorial; ?>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="right-sidebar" class="no-background">
      <div id="header-username"></div>
      <div class="tab-header"><h4>&nbsp;</h4></div>
    </div>

    <div  id="video-modal" class="modal"
          ng-controller="ModalsController as modals"
          ng-click="modals.closeModal($event)">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <div class="video-container">
              <div id="tutorial"></div>
              <div id="stop-video"> </div>
            </div>
            <script>
              // Add api
              var tag = document.createElement('script');
              tag.src = "https://www.youtube.com/iframe_api";
              var firstScriptTag = document.getElementsByTagName('script')[0];
              firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

              var player, iframe;
              var $ = document.querySelector.bind(document);

              function onYouTubeIframeAPIReady() {
                player = new YT.Player('tutorial', {
                  height: '100%',
                  width: '100%',
                  videoId: 'nv1bM6tL5rA',
                  events: {
                    'onReady': onPlayerReady
                  }
                });
              }
              function onPlayerReady(event) {
                var player = event.target;
                iframe = $('#tutorial');
                setupListener();
              }

              function setupListener (){
                if (window.innerWidth < 980) {
                  $('#tutorial-button').on('click', playFullscreen);
                } else {
                  $('#tutorial-button').on('click', startVideo);
                }
                $('#stop-video').on('click', stopVideo);
              }

              function playFullscreen (){
                player.playVideo();
                var requestFullScreen = iframe.requestFullScreen || iframe.mozRequestFullScreen || iframe.webkitRequestFullScreen;
                if (requestFullScreen) {
                  requestFullScreen.bind(iframe)();
                }
              }

              function startVideo() {
                player.playVideo();
              }

              function stopVideo() {
                player.pauseVideo();
              }
            </script>
          </div>
        </div>
      </div>
    </div>
  <?php
    }
  ?>
  </div>

<div  class="modal fade" id="modal-add-instagram" tabindex="-1" role="dialog" aria-labelledby="add-instagram-label" aria-hidden="true"
      ng-controller="ModalsController as modals"
      ng-click="modals.closeModal($event);">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" ng-click="modals.closeModal()"><span aria-hidden="true">&times;</span></button>
        <img src="catalog/view/theme/default/image/toons/guy-yellowinstagram.png">
        <h4 class="modal-title" id="add-instagram-label">
          <?php echo $add_instagram; ?>
        </h4>
        <p>
            <?php echo $add_instagram_text; ?>
        </p>
      </div>
      <form action="" novalidate onsubmit="return false">
        <div class="modal-body">
          <div class="form-group">
            <div id="login-error"
              ng-click="modals.tooltips.displayTooltip($event)"
              ng-mouseover="modals.tooltips.hideTooltip($event)"
              data-tooltip="<?php echo $tooltip_password_reset; ?>"
              data-tooltip-position="top"
              data-tooltip-style="light"
              data-hide-fire="mouseover">
            </div>
            <div id="add-instagram-error"
              data-tooltip=""
              data-tooltip-position="top"
              data-tooltip-style="light wide"
              data-hide-fire='click'>
            </div>
            <div id="username-empty"
              ng-click="modals.tooltips.displayTooltip($event)"
              ng-mouseover="modals.tooltips.hideTooltip($event)"
              data-tooltip="<?php echo $tooltip_username_empty; ?>"
              data-tooltip-position="top"
              data-tooltip-style="light"
              data-hide-fire="mouseover">
            </div>
            <input  type="text" id="add-account-username" name="username"
                    placeholder="<?php echo $entry_username; ?>" class="form-control"
                    ng-focus="modals.tooltips.hideTooltip($event)"
                    data-hide-fire="focus">
            <i class="fa fa-instagram"></i>
          </div>
          <div class="form-group">
            <div id="password-empty"
              ng-click="modals.tooltips.displayTooltip($event)"
              ng-mouseover="modals.tooltips.hideTooltip($event)"
              data-tooltip="<?php echo $tooltip_password_empty; ?>"
              data-tooltip-position="top"
              data-tooltip-style="light"
              data-hide-fire="mouseover">
            </div>
            <input  type="password" id="add-account-password" name="password"
                    placeholder="<?php echo $entry_password; ?>" class="form-control"
                    ng-focus="modals.tooltips.hideTooltip($event)"
                    data-hide-fire="focus">
            <i class="fa fa-lock"></i>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" id="button-add-instagram" class="btn btn-primary"
                  ng-click="modals.addAccount()">
            <?php echo $button_add_account; ?>
          </button>
          <p  class="password-politics">
            <?php echo $save_passwords_politic; ?>
          </p>
        </div>
      </form>
    </div>
  </div>
</div>

<div  id="video-modal" class="modal"
      ng-controller="ModalsController as modals"
      ng-click="modals.closeModal($event)">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <div class="video-container">
          <div id="video-loader"></div>
          <div id="help-video"></div>
          <div id="stop-video"> </div>
          <div id="help-video-button"></div>
        </div>
        <script>
          // Add api
          var tag = document.createElement('script');
          tag.src = "https://www.youtube.com/iframe_api";
          var firstScriptTag = document.getElementsByTagName('script')[0];
          firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

          var player, iframe;
          var $ = document.querySelector.bind(document);

          function onYouTubeIframeAPIReady() {
            player = new YT.Player('help-video', {
              height: '100%',
              width: '100%',
              videoId: '',
              events: {
                'onReady': onPlayerReady
              }
            });
          }
          function onPlayerReady(event) {
            var player = event.target;
            iframe = $('#help-video');
            setupListener();
          }
                        
          function setupListener (){
            if ($('#help-video-button')) {
              if (window.innerWidth < 980) {
                $('#help-video-button').on('click', playFullscreen);
              } else {
                $('#help-video-button').click(function(){
                  startVideo();
                });
              }
            }
            $('#stop-video').click(function(){
              stopVideo();
            });
          }

          function playFullscreen (){
            player.playVideo();
            var requestFullScreen = iframe.requestFullScreen || iframe.mozRequestFullScreen || iframe.webkitRequestFullScreen;
            if (requestFullScreen) {
              requestFullScreen.bind(iframe)();
            }
          }

          function startVideo() {
            player.playVideo();
          }

          function stopVideo() {
            player.pauseVideo();
          }
        </script>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>