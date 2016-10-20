<div id="content" data-kickoff="true">
  <div class="tab-header" id="tab-header-middle" ng-controller="TabsController as tabs">
    <h4 class="current-tab kickoff" data-tab-language="<?php echo $tab_kickoff; ?>" data-tab="kickoff" ng-click="tabs.changeTab($event,'middle')">
      <?php echo $tab_kickoff; ?>
    </h4>
    <h4 class="users" data-tab="users" data-tab-language="<?php echo $tab_users; ?>" ng-click="tabs.changeTab($event,'middle')">
      <?php echo $tab_users; ?> <span class="notification-count" id="users-notification-count"><?php echo count($follow_source_users) + $total_selected_source_interests; ?></span>
    </h4>
    <h4 class="hashtags" data-tab="hashtags" data-tab-language="<?php echo $tab_hashtags; ?>" ng-click="tabs.changeTab($event,'middle')">
      <?php echo $tab_hashtags; ?> <span class="notification-count" id="tags-notification-count"><?php echo count($follow_source_tags); ?></span>
    </h4>
    <h4 class="locations" data-tab="locations" data-tab-language="<?php echo $tab_locations; ?>" ng-click="tabs.changeTab($event,'middle')">
      <?php echo $tab_locations; ?> <span class="notification-count" id="locations-notification-count"><?php echo count($follow_source_locations); ?></span>
    </h4>
    <h4 class="whitelist" data-tab="whitelist" data-tab-language="<?php echo $tab_whitelist; ?>" ng-click="tabs.changeTab($event,'middle')">
      <?php echo $tab_whitelist; ?> <span class="notification-count" id="whitelist-notification-count"><?php echo count($whitelist_users); ?></span>
    </h4>
  </div>
  <div id="tab-dropdown-middle" class="dropdown tab-header-dropdown"
        ng-controller="TabsController as tabs">
    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"  ng-click="header.openMenu()"><?php echo $tab_kickoff; ?></button>
    <ul class="dropdown-menu" ng-controller="HeaderController as header" ng-click="header.closeMenu()">
      <li class="start-account" data-tab="start-account" data-tab-language="<?php echo $tab_kickoff; ?>" ng-click="tabs.changeTab($event,'middle')">
        <?php echo $tab_kickoff; ?>
      </li>
      <li class="current-tab kickoff" data-tab-language="<?php echo $tab_kickoff; ?>" data-tab="kickoff" ng-click="tabs.changeTab($event,'middle')">
        <?php echo $tab_kickoff; ?>
      </li>
      <div class="separator"></div>
      <li class="users" data-tab="users" data-tab-language="<?php echo $tab_users; ?>" ng-click="tabs.changeTab($event,'middle')">
        <?php echo $tab_users; ?> <span class="notification-count"><?php echo count($follow_source_users) + $total_selected_source_interests; ?></span>
      </li>
      <li class="hashtags" data-tab="hashtags" data-tab-language="<?php echo $tab_hashtags; ?>" ng-click="tabs.changeTab($event,'middle')">
        <?php echo $tab_hashtags; ?> <span class="notification-count"><?php echo count($follow_source_tags); ?></span>
      </li>
      <li class="locations" data-tab="locations" data-tab-language="<?php echo $tab_locations; ?>" ng-click="tabs.changeTab($event,'middle')">
        <?php echo $tab_locations; ?> <span class="notification-count"><?php echo count($follow_source_locations); ?></span>
      </li>
      <li class="whitelist" data-tab="whitelist" data-tab-language="<?php echo $tab_whitelist; ?>" ng-click="tabs.changeTab($event,'middle')">
        <?php echo $tab_whitelist; ?> <span class="notification-count"><?php echo count($whitelist_users); ?></span>
      </li>
    </ul>
  </div>
  <div class="tab-panel-container middle-tab" id="tab-body-middle">
    <div class="row" ng-controller="KickoffController as kickoff">
      <?php if (($follow_source_users || $total_selected_source_interests) && ($whitelist_users || $skip_whitelist)) { ?>
        <button class="gold-button start-account-button"
                data-start-account="<?php echo $kickoff_start_account_tab; ?>"
                data-disabled-users="<?php echo $kickoff_start_add_users; ?>"
                data-disabled-whitelist="<?php echo $kickoff_start_add_whitelist; ?>"
                ng-click="kickoff.startKickoff('<?php echo $account_id; ?>')">
          <?php echo $kickoff_start_account_tab; ?>
        </button>
      <?php } else { ?>
        <?php if ((!$follow_source_users || !$total_selected_source_interests) && ($whitelist_users || $skip_whitelist)) { ?>
          <button class="gold-button start-account-button disabled-users"
                  data-start-account="<?php echo $kickoff_start_account_tab; ?>"
                  data-disabled-users="<?php echo $kickoff_start_add_users; ?>"
                  data-disabled-whitelist="<?php echo $kickoff_start_add_whitelist; ?>"
                  ng-click="kickoff.startKickoff('<?php echo $account_id; ?>')">
            <?php echo $kickoff_start_add_users; ?>
          </button>
        <?php } elseif (($follow_source_users || $total_selected_source_interests) && (!$whitelist_users || !$skip_whitelist)) { ?>
          <button class="gold-button start-account-button disabled-whitelist"
                  data-start-account="<?php echo $kickoff_start_account_tab; ?>"
                  data-disabled-users="<?php echo $kickoff_start_add_users; ?>"
                  data-disabled-whitelist="<?php echo $kickoff_start_add_whitelist; ?>"
                  ng-click="kickoff.startKickoff('<?php echo $account_id; ?>')">
            <?php echo $kickoff_start_add_whitelist; ?>
          </button>
        <?php } else { ?>
          <button class="gold-button start-account-button disabled" disabled
                  data-start-account="<?php echo $kickoff_start_account_tab; ?>"
                  data-disabled-users="<?php echo $kickoff_start_add_users; ?>"
                  data-disabled-whitelist="<?php echo $kickoff_start_add_whitelist; ?>"
                  ng-click="kickoff.startKickoff('<?php echo $account_id; ?>')">
            <?php echo $kickoff_start_account_tab; ?>
          </button>
        <?php } ?>
      <?php } ?>
    </div>
    <div  id="kickoff" class="tab current-tab kickoff-tab"
          ng-controller="KickoffController as kickoff">
      <div class="row cards-row">
        <div class="card cartoon-card users-steps">
          <div class="card-content row">
            <div>
              <h3 class="steps"><?php echo $text_steps_1; ?></h3>
              <img src="catalog/view/theme/default/image/toons/guy-yellowmagnetuser.png">
              <h3><?php echo $kickoff_users_title; ?></h3>
              <p><?php echo $kickoff_users_text; ?></p>
            </div>
            <div class="card-input">
              <span id="tutorial-button" class="tutorial-popup"
                    ng-controller="ModalsController as modals"
                    ng-click="modals.helpModal($event)"
                    data-video-id="_ZM-zFN7iYk">
                <?php echo $text_video_tutorial; ?>
              </span>
              <small class="required"><?php echo $kickoff_required; ?></small>
              <button class="gold-button"
                      data-tab="users"
                      data-tab-language="<?php echo $tab_users; ?>"
                      ng-controller="TabsController as tabs"
                      ng-click="tabs.changeTab($event,'middle')"
                      data-add-more="<?php echo $kickoff_add_more; ?>"
                      data-get-started="<?php echo $kickoff_button_get_started; ?>">
                <?php if (!$follow_source_users && !$total_selected_source_interests) { ?>
                <?php   echo $kickoff_button_get_started; ?>
                <?php } else { ?>
                <?php   echo $kickoff_add_more; ?>
                <?php } ?>
              </button>
            </div>
          </div>
        </div>
        <div class="card cartoon-card whitelist-steps">
          <div class="card-content row">
            <div>
              <h3 class="steps"><?php echo $text_steps_2; ?></h3>
              <img src="catalog/view/theme/default/image/toons/guy-yellowangel.png">
              <h3><?php echo $kickoff_whitelist_title; ?></h3>
              <p><?php echo $kickoff_whitelist_text; ?></p>
            </div>
            <div class="card-input">
              <span id="tutorial-button" class="tutorial-popup"
                    ng-controller="ModalsController as modals"
                    ng-click="modals.helpModal($event)"
                    data-video-id="X63OL14KPQc">
                <?php echo $text_video_tutorial; ?>
              </span>
              <small class="required"><?php echo $kickoff_required; ?></small>
              <button class="gold-button"
                      data-tab="whitelist"
                      data-tab-language="<?php echo $tab_whitelist; ?>"
                      ng-controller="TabsController as tabs"
                      ng-click="tabs.changeTab($event,'middle')"
                      data-add-more="<?php echo $kickoff_add_more; ?>"
                      data-get-started="<?php echo $kickoff_button_get_started; ?>">
                <?php if (!$whitelist_users) { ?>
                <?php   echo $kickoff_button_get_started; ?>
                <?php } else { ?>
                <?php   echo $kickoff_add_more; ?>
                <?php } ?>
              </button>
            </div>
          </div>
        </div>
        <div class="card cartoon-card tags-steps">
          <div class="card-content row">
            <div>
              <h3 class="steps"><?php echo $text_steps_3; ?></h3>
              <img src="catalog/view/theme/default/image/toons/guy-yellowmagnethash.png">
              <h3><?php echo $kickoff_tags_title; ?></h3>
              <p><?php echo $kickoff_tags_text; ?></p>
            </div>
            <div class="card-input">
              <span id="tutorial-button" class="tutorial-popup"
                    ng-controller="ModalsController as modals"
                    ng-click="modals.helpModal($event)"
                    data-video-id="SE5SM_iNhkA">
                <?php echo $text_video_tutorial; ?>
              </span>
              <small><?php echo $kickoff_optional; ?></small>
              <button class="gold-button"
                      data-tab="hashtags"
                      data-tab-language="<?php echo $tab_hashtags; ?>"
                      ng-controller="TabsController as tabs"
                      ng-click="tabs.changeTab($event,'middle')"
                      data-add-more="<?php echo $kickoff_add_more; ?>"
                      data-get-started="<?php echo $kickoff_button_get_started; ?>">
                <?php if (!$follow_source_tags) { ?>
                <?php   echo $kickoff_button_get_started; ?>
                <?php } else { ?>
                <?php   echo $kickoff_add_more; ?>
                <?php } ?>
              </button>
            </div>
          </div>
        </div>
        <div class="card cartoon-card locations-steps">
          <div class="card-content row">
            <div>
              <h3 class="steps"><?php echo $text_steps_4; ?></h3>
              <img src="catalog/view/theme/default/image/toons/guy-yellowmagnetmarker.png">
              <h3><?php echo $kickoff_locations_title; ?></h3>
              <p><?php echo $kickoff_locations_text; ?></p>
            </div>
            <div class="card-input">
              <span id="tutorial-button" class="tutorial-popup">
              </span>
              <small><?php echo $kickoff_optional; ?></small>
              <button class="gold-button"
                      data-tab="locations"
                      data-tab-language="<?php echo $tab_locations; ?>"
                      ng-controller="TabsController as tabs"
                      ng-click="tabs.changeTab($event,'middle')"
                      data-add-more="<?php echo $kickoff_add_more; ?>"
                      data-get-started="<?php echo $kickoff_button_get_started; ?>">
                <?php if (!$follow_source_locations) { ?>
                <?php   echo $kickoff_button_get_started; ?>
                <?php } else { ?>
                <?php   echo $kickoff_add_more; ?>
                <?php } ?>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="tab users-tab kickoff-tab" id="users">
      <div class="row cards-row">
        <div id="users-sources-container">
          <!-- Search Users Card -->
          <div class="card cartoon-card">
            <div class="card-content row search-tool" id="search-users-sources">
              <div>
                <img src="catalog/view/theme/default/image/toons/guy-yellowmagnetuser.png">
                <?php echo $title_kickoff_users_add; ?>
                <?php echo $text_kickoff_users_add; ?>
              </div>
              <div class="input-search">
                <i class="fa fa-search"></i>
                <input type="text" placeholder="<?php echo $entry_search; ?>"
                       ng-controller="SearchController as search"
                       ng-model='searchUsers'
                       ng-change='search.searchUsers(searchUsers,"search-users-sources","<?php echo $account_id; ?>")'>
                <div class="search-results">
                </div>
              </div>
            </div>
          </div>
          <!-- /Search Users Card -->
          <!-- Users Sources List -->
          <div id="user-sources-list">
            <?php foreach ($follow_source_users as $user) { ?>
            <div class="user-list <?php echo str_replace('.','',$user->username); echo ' '.$user->id; ?>">
              <h4>
                <img src="catalog/view/theme/default/image/dashboard/default_user.jpg">
                <?php if (strlen($user->username) < 23) { ?>
                <?php echo $user->username; ?>
                <?php } else { ?>
                <?php echo substr($user->username,0,22).'...'; ?>
                <?php } ?>
              </h4>
              <button class="gold-button"
                      ng-controller="RemoveController as remove"
                      ng-click="remove.removeFromList($event,'<?php echo $account_id; ?>','follow_source_users','<?php echo $user->id; ?>')">
                X
              </button>
            </div>
            <?php } ?>
          </div>
          <!-- /Users Sources List -->
        </div>
        <div id="source-interests-container">
          <!-- Source Interests Card -->
          <div id="source-interests" class="card">
            <div class="card-content row">
              <h4 class="or"><?php echo $text_or_source_interests; ?></h4>
              <h3><?php echo $title_source_interests; ?></h3>
              <p><?php echo $text_source_interests; ?></p>
              <div id="source-categories">
                <?php foreach ($source_interests as $source_interest) { ?>
                <?php if ($source_interest['children']) { ?>
                  <div>
                    <h4><?php echo $source_interest['name']; ?></h4>
                    <div class="row">
                      <?php foreach ($source_interest['children'] as $child) { ?>
                      <button class="<?php if ($child['selected']) echo 'active'; ?>"
                              data-source-id="<?php echo $child['source_interest_id']; ?>"
                              ng-controller="SourceInterestsController as sources"
                              ng-click="sources.activateSource($event)">
                        <i class="fa"></i>
                        <?php echo $child['name']; ?>
                      </button>
                      <?php } ?>
                    </div>
                  </div>
                <?php } ?>
                <?php } ?>
              </div>
            </div>
          </div>
          <!-- /Source Interests -->
        </div>
      </div>
    </div>
    <div class="tab kickoff-tab hashtags-tab" id="hashtags">
      <div class="row cards-row">
        <div class="card cartoon-card">
          <div class="card-content row search-tool" id="search-hashtags-sources">
            <div>
              <img src="catalog/view/theme/default/image/toons/guy-yellowmagnethash.png">
              <?php echo $title_hashtags_add; ?>
              <?php echo $text_hashtags_add; ?>
            </div>
            <div class="input-search">
              <i class="fa fa-search"></i>
              <input type="text" placeholder="<?php echo $entry_search; ?>"
                     ng-controller="SearchController as search"
                     ng-model='searchHashtags'
                     ng-change='search.searchTags(searchHashtags,"search-hashtags-sources","<?php echo $account_id; ?>")'>
              <div class="search-results"></div>
            </div>
          </div>
        </div>
      </div>
      <hr class="clearfix">
      <div class="row cards-row" id="hashtag-sources-list">
        <?php foreach ($follow_source_tags as $tag) { ?>
        <div class="user-list <?php echo $tag; ?>">
          <h4>
            <i class="fa fa-hashtag"></i>
            <?php if (strlen($tag) < 23) { ?>
            <?php   echo $tag; ?>
            <?php } else { ?>
            <?php echo substr($tag,0,22).'...'; ?>
            <?php } ?>
          </h4>
          <button class="gold-button"
                  ng-controller="RemoveController as remove"
                  ng-click="remove.removeFromList($event,'<?php echo $account_id; ?>','follow_source_tags','<?php echo $tag; ?>')">
            X
          </button>
        </div>
        <?php } ?>
      </div>
    </div>
    <div class="tab users-tab kickoff-tab" id="locations">
      <div class="row cards-row">
        <!-- Search Locations Card -->
        <div class="card cartoon-card">
          <div class="card-content row search-tool" id="search-locations-sources">
            <div>
              <img src="catalog/view/theme/default/image/toons/guy-yellowmagnetmarker.png">
              <?php echo $title_locations_add; ?>
              <?php echo $text_kickoff_locations_add; ?>
            </div>
            <div class="input-search">
              <i class="fa fa-search"></i>
              <input type="text" placeholder="<?php echo $entry_search; ?>"
                     ng-controller="SearchController as search"
                     ng-model='searchLocations'
                     ng-change='search.searchLocations(searchLocations,"search-locations-sources","<?php echo $account_id; ?>")'>
              <div class="search-results">
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row cards-row">
        <!-- /Search Locations Card -->
        <!-- Locations Sources List -->
        <hr class="clearfix">
        <div class="row cards-row" id="locations-sources-list">
          <?php foreach ($follow_source_locations as $location) { ?>
          <div class="user-list <?php echo $location->id; ?>">
            <i class="fa fa-map-marker"></i>
            <div class="user-description">
              <h4>
                <?php if (strlen($location->name) < 28) { ?>
                <?php echo $location->name; ?>
                <?php } else { ?>
                <?php echo substr($location->name,0,25).'...'; ?>
                <?php } ?>
              </h4>
              <?php if (strtolower(preg_replace('/[^A-Za-z0-9\-]/', '',str_replace('.','',$location->name))) != '') { ?>
              <p>
                <?php if (!empty($location->subtitle)) { ?>
                <?php if (strlen($location->subtitle) < 33) { ?>
                <?php echo $location->subtitle; ?>
                <?php } else { ?>
                <?php echo substr($location->subtitle,0,30).'...'; ?>
                <?php } ?>
                <?php } ?>
              </p>
              <?php } ?>
            </div>
            <button class="gold-button"
                    ng-controller="RemoveController as remove"
                    ng-click="remove.removeFromList($event,'<?php echo $account_id; ?>','follow_source_locations','<?php echo $location->id; ?>')">
              X
            </button>
          </div>
          <?php } ?>
        </div>
        <!-- /Locations Sources List -->
      </div>
    </div>
    <div id="whitelist" class="tab whitelist-tab"
         ng-controller="WhitelistController as whitelist">
      <div class="row cards-row">   
        <div class="card cartoon-card">
          <div class="row card-content search-tool" id="search-whitelist">
            <div>
              <img src="catalog/view/theme/default/image/toons/guy-yellowangel.png">
              <?php echo $title_whitelist_add; ?>
              <?php echo $text_whitelist_add; ?>
            </div>
            <div class="input-search">
              <i class="fa fa-search"></i>
              <input type="text" placeholder="<?php echo $entry_search; ?>"
                     ng-controller="SearchController as search"
                     ng-model='searchInput'
                     ng-change='search.searchUsers(searchInput,"search-whitelist","<?php echo $account_id; ?>")'>
              <div class="search-results">
              </div>
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-content row">
            <div>
              <?php echo $text_whitelist_import; ?>
            </div>
            <div class="card-input dropdown">
              <button class="btn btn-default dropdown-toggle"
                      id="whitelist-limit" type="button"
                      data-limit="unset"
                      data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <?php echo $button_import_users; ?>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu" aria-labelledby="whitelist-limit">
                <li ng-click="whitelist.setWhitelistLimit($event)">100</li>
                <li ng-click="whitelist.setWhitelistLimit($event)">200</li>
                <li ng-click="whitelist.setWhitelistLimit($event)">300</li>
                <li ng-click="whitelist.setWhitelistLimit($event)">400</li>
                <li ng-click="whitelist.setWhitelistLimit($event)">500</li>
                <li ng-click="whitelist.setWhitelistLimit($event)">1000</li>
                <li ng-click="whitelist.setWhitelistLimit($event)">1500</li>
              </ul>
            </div>
            <div class="select-dropdown">
              <select id="select-whitelist"
                      ng-model="whitelistLimit"
                      ng-change="whitelist.setWhitelistLimit()">
                <option value="" disabled selected><?php echo $button_import_users; ?></option>
                <option>100</option>
                <option>200</option>
                <option>300</option>
                <option>400</option>
                <option>500</option>
                <option>1000</option>
                <option>1500</option>
              </select>
            </div>
          </div>
        </div>
        <div class="card" id="skip-whitelist-card">
          <div class="card-content row">
          <?php if (!$skip_whitelist) { ?>
            <div>
              <?php echo $text_skip_whitelist; ?>
            </div>
            <div class="card-input">
              <button type="button" class="gold-button"
                      data-language="<?php echo $button_whitelist_skipped; ?>"
                      ng-controller="ModalsController as modals"
                      ng-click="modals.displayWarning('skip-whitelist-warning')">
                <?php echo $button_skip_whitelist; ?>
              </button>
            </div>
          <?php } else { ?>
            <div>
              <?php echo $text_skip_whitelist; ?>
            </div>
            <div class="card-input">
              <button type="button" class="gold-button" disabled>
                <?php echo $button_whitelist_skipped; ?>
              </button>
            </div>
          <?php } ?>
          </div>
        </div>
      </div>
      <hr class="clearfix">
      <!-- Whitelist Users List -->
      <div class="row cards-row" id="whitelist-users-list">
        <?php foreach ($whitelist_users as $user) { ?>
        <div class="user-list <?php echo str_replace('.','',$user->username); echo ' '.$user->id; ?>">
          <h4>
            <img src="catalog/view/theme/default/image/dashboard/default_user.jpg">
            <?php if (strlen($user->username) < 23) { ?>
            <?php echo $user->username; ?>
            <?php } else { ?>
            <?php echo substr($user->username,0,22).'...'; ?>
            <?php } ?>
          </h4>
          <button class="gold-button"
                  ng-controller="RemoveController as remove"
                  ng-click="remove.removeFromList($event,'<?php echo $account_id; ?>','whitelist_users','<?php echo $user->id; ?>')">
            X
          </button>
        </div>
        <?php } ?>
      </div>
      <!-- Whitelist Users List -->
    </div>
  </div>
</div>
<div id="sidebar-username">
  <i class="fa fa-user"></i>
  <h4><?php echo $account_username; ?> </h4>
</div>
<aside id="right-sidebar">
  <div class="tab-header" id="tab-header-sidebar">
    <h4>
      &nbsp;
    </h4>
  </div>
  <div class="right-sidebar-content" id="right-sidebar-content">
    <div class="tab-panel-container sidebar-tab" id="tab-body-sidebar">
      <div class="tab current-tab" id="start-account">
        <div class="sidebar-item cartoon-card" id="rocket-item"
              ng-controller="TabsController as tabs">
          <img src="catalog/view/theme/default/image/toons/rocket-yellow.png">
          <?php echo $kickoff_right_sidebar_text; ?>
          <div  class="row right-sidebar-users" data-tab="users" data-tab-language="<?php echo $tab_users; ?>"
                ng-click="tabs.changeTab($event,'middle')">
            <span class="steps">1</span>
            <p>
              <?php echo $kickoff_right_sidebar_users; ?>
            </p>
            <small class="required">
              <?php echo $kickoff_required; ?>
            </small>
            <?php if ($follow_source_users || $total_selected_source_interests) { ?>
              <i class="fa fa-check-circle"></i>
            <?php } else { ?>
              <i class="fa fa-chevron-right"></i>
            <?php } ?>
          </div>
          <div  class="row right-sidebar-whitelist" data-tab="whitelist" data-tab-language="<?php echo $tab_whitelist; ?>"
                ng-click="tabs.changeTab($event,'middle')">
            <span class="steps">2</span>
            <p>
              <?php echo $kickoff_right_sidebar_whitelist; ?>
            </p>
            <small class="required">
              <?php echo $kickoff_required; ?>
            </small>
            <?php if ($whitelist_users || $skip_whitelist) { ?>
              <i class="fa fa-check-circle"></i>
            <?php } else { ?>
              <i class="fa fa-chevron-right"></i>
            <?php } ?>
          </div>
          <div  class="row right-sidebar-tags" data-tab="hashtags" data-tab-language="<?php echo $tab_hashtags; ?>"
                ng-click="tabs.changeTab($event,'middle')">
            <span class="steps">3</span>
            <p>
              <?php echo $kickoff_right_sidebar_tags; ?>
            </p>
            <small>
              <?php echo $kickoff_optional; ?>
            </small>
            <?php if ($follow_source_tags) { ?>
              <i class="fa fa-check-circle"></i>
            <?php } else { ?>
              <i class="fa fa-chevron-right"></i>
            <?php } ?>
          </div>
          <div  class="row right-sidebar-locations" data-tab="locations" data-tab-language="<?php echo $tab_locations; ?>"
                ng-click="tabs.changeTab($event,'middle')">
            <span class="steps">4</span>
            <p>
              <?php echo $kickoff_right_sidebar_locations; ?>
            </p>
            <small>
              <?php echo $kickoff_optional; ?>
            </small>
            <?php if ($follow_source_locations) { ?>
              <i class="fa fa-check-circle"></i>
            <?php } else { ?>
              <i class="fa fa-chevron-right"></i>
            <?php } ?>
          </div>
          <div  class="row start-kickoff-container"
                ng-controller="KickoffController as kickoff">

          <?php if (($follow_source_users || $total_selected_source_interests) && ($whitelist_users || $skip_whitelist)) { ?>
            <button class="gold-button" id="start-kickoff-button"
                    data-start-account="<?php echo $kickoff_start_account; ?>"
                    data-disabled-users="<?php echo $kickoff_start_add_users; ?>"
                    data-disabled-whitelist="<?php echo $kickoff_start_add_whitelist; ?>"
                    data-skip-whitelist="<?php echo $skip_whitelist; ?>"
                    ng-click="kickoff.startKickoff('<?php echo $account_id; ?>')">
              <?php echo $kickoff_start_account; ?>
            </button>
          <?php } else { ?>
            <?php if ((!$follow_source_users || !$total_selected_source_interests) && ($whitelist_users || $skip_whitelist)) { ?>
              <button class="gold-button disabled-users" id="start-kickoff-button"
                      data-start-account="<?php echo $kickoff_start_account; ?>"
                      data-disabled-users="<?php echo $kickoff_start_add_users; ?>"
                      data-disabled-whitelist="<?php echo $kickoff_start_add_whitelist; ?>"
                      data-skip-whitelist="<?php echo $skip_whitelist; ?>"
                      ng-click="kickoff.startKickoff('<?php echo $account_id; ?>')">
                <?php echo $kickoff_start_add_users; ?>
              </button>
            <?php } elseif (($follow_source_users || $total_selected_source_interests) && (!$whitelist_users || !$skip_whitelist)) { ?>
              <button class="gold-button disabled-whitelist" id="start-kickoff-button"
                      data-start-account="<?php echo $kickoff_start_account; ?>"
                      data-disabled-users="<?php echo $kickoff_start_add_users; ?>"
                      data-disabled-whitelist="<?php echo $kickoff_start_add_whitelist; ?>"
                      data-skip-whitelist="<?php echo $skip_whitelist; ?>"
                      ng-click="kickoff.startKickoff('<?php echo $account_id; ?>')">
                <?php echo $kickoff_start_add_whitelist; ?>
              </button>
            <?php } else { ?>
              <button class="gold-button disabled" id="start-kickoff-button" disabled
                      data-start-account="<?php echo $kickoff_start_account; ?>"
                      data-disabled-users="<?php echo $kickoff_start_add_users; ?>"
                      data-disabled-whitelist="<?php echo $kickoff_start_add_whitelist; ?>"
                      data-skip-whitelist="<?php echo $skip_whitelist; ?>"
                      ng-click="kickoff.startKickoff('<?php echo $account_id; ?>')">
                <?php echo $kickoff_start_account; ?>
              </button>
            <?php } ?>
          <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</aside>

<div ng-controller="SourceInterestsController"></div>

<div  id="warning-whitelist" class="modal warning-modal"
      ng-controller="ModalsController as modals"
      ng-click="modals.closeModal($event)">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <div class="cartoon-card">
          <img src="catalog/view/theme/default/image/toons/guy-yellowinstagram.png">
        </div>
        <?php echo $text_whitelist_import_confirm; ?>
      </div>
      <div class="modal-footer">
        <button class="cancel"
                ng-click="modals.cancelAction('warning-whitelist')">
          <?php echo $button_no; ?>
        </button>
        <button class="confirm"
                ng-controller="WhitelistController as whitelist"
                ng-click="whitelist.checkWhitelist('<?php echo $account_id; ?>')">
          <?php echo $button_yes; ?>
        </button>
      </div>
    </div>
  </div>
</div>

<div  id="skip-whitelist-warning" class="modal warning-modal"
      ng-controller="ModalsController as modals"
      ng-click="modals.closeModal($event)">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <p>
          <?php echo $text_skip_whitelist_modal; ?>
        </p>
      </div>
      <div class="modal-footer">
        <button class="cancel"
                ng-click="modals.cancelAction('skip-whitelist-warning')">
          <?php echo $button_no; ?>
        </button>
        <button class="confirm"
                ng-controller="KickoffController as kickoff"
                ng-click="kickoff.skipWhitelist('<?php echo $account_id; ?>')">
          <?php echo $button_yes; ?>
        </button>
      </div>
    </div>
  </div>
</div>

<div  id="list-warning" class="modal warning-modal"
      ng-controller="ModalsController as modals"
      ng-click="modals.closeModal($event)">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <p class="message"></p>
      </div>
      <div class="modal-footer">
        <button class="gold-button" ng-click="modals.cancelAction('list-warning')"><?php echo $button_return; ?></button>
      </div>
    </div>
  </div>
</div>