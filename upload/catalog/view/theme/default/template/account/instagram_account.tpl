<div id="content" data-msg="<?php echo $account_info->StatusMessage; ?>">
	<div class="tab-header" id="tab-header-middle" ng-controller="TabsController as tabs">
		<h4 class="current-tab dashboard" data-tab="dashboard" data-tab-language="<?php echo $tab_dashboard; ?>" ng-click="tabs.changeTab($event,'middle')">
			<?php echo $tab_dashboard; ?>
		</h4>
		<div></div>
		<h4 class="users" data-tab="users" data-tab-language="<?php echo $tab_users; ?>" ng-click="tabs.changeTab($event,'middle')">
			<?php echo $tab_users; ?> <span class="notification-count" id="users-notification-count"><?php echo count($follow_source_users); ?></span>
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
		<div></div> 
		<h4 class="settings" data-tab="settings" data-tab-language="<?php echo $tab_settings; ?>" ng-click="tabs.changeTab($event,'middle')">
			<?php echo $tab_settings; ?>
		</h4>
		<?php if ($show_billing) { ?>
		<div></div>
		<h4 class="billing" data-tab="billing" data-tab-language="<?php echo $tab_billing; ?>" ng-click="tabs.billingTab($event)">
			<?php echo $tab_billing; ?>
		</h4>
		<?php } ?>
	</div>
	<div id="tab-dropdown-middle" class="dropdown tab-header-dropdown" ng-controller="TabsController as tabs">
		<button id="mobile-menu" class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" ng-click="header.openMenu()">
			<i class="fa fa-bars"></i> <?php echo $tab_dashboard; ?>
		</button>
		<ul class="dropdown-menu" aria-labelledby="mobile-menu" ng-controller="HeaderController as header" ng-click="header.closeMenu()">
			<li class="account" data-tab="account" data-tab-language="<?php echo $tab_account; ?>" ng-click="tabs.changeTab($event,'middle')"><?php echo $tab_account; ?></li>
			<li class="activity request" data-tab="activity" data-tab-language="<?php echo $tab_activity; ?>" ng-click="tabs.changeTab($event,'middle')"><?php echo $tab_activity; ?></li>
			<li class="current-tab dashboard" data-tab="dashboard" data-tab-language="<?php echo $tab_dashboard; ?>" ng-click="tabs.changeTab($event,'middle')"><?php echo $tab_dashboard; ?></li>
			<div class="separator"></div>
			<li class="users" data-tab="users" data-tab-language="<?php echo $tab_users; ?>" ng-click="tabs.changeTab($event,'middle')">
				<?php echo $tab_users; ?> <span class="notification-count"><?php echo count($follow_source_users); ?></span>
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
			<div class="separator"></div>
			<li class="settings" data-tab="settings" data-tab-language="<?php echo $tab_settings; ?>" ng-click="tabs.changeTab($event,'middle')"><?php echo $tab_settings; ?></li>
			<div class="separator"></div>
			<?php if ($show_billing) { ?>
			<li class="billing" data-tab="billing" data-tab-language="<?php echo $tab_billing; ?>" ng-click="tabs.billingTab($event)"><?php echo $tab_billing; ?></li>
			<?php } ?>
		</ul>
	</div>
	<div class="tab-panel-container middle-tab" id="tab-body-middle">
		<div class="tab current-tab" id="dashboard">
			<!-- Charts -->
			<div class="row cards-row">
				<div class="card" id="my-influence">
					<div class="card-header row">
						<h4>
							<?php echo $text_my_influence; ?>
						</h4>
					</div>
					<div class="card-content row">
						<div class="followers-number">
							<?php echo $text_followers; ?>
							<span class="followers-count-date">
								<?php echo $text_followers_as_of_date; ?>
							</span>
							<span class="oldest-followers">
								<?php echo $followers_oldest; ?>
							</span>
						</div>
						<div class="followers-number">
							<?php echo $text_followers; ?>
							<span class="followers-count-date">
								<?php echo $text_followers_as_of_now; ?>
							</span>
							<span class="current-followers">
								<?php echo $followers_current; ?>
							</span>
							<span class="followers-difference">
								<?php if ($followers_difference > 0) { ?>
								<span class="positive-difference">
									+<?php echo $followers_difference; ?>
									(<?php echo $followers_pct_difference; ?>%)
								</span>
								<?php } elseif ($followers_difference == 0) { ?>
								<span>
									<?php echo $followers_difference; ?>
									(<?php echo $followers_pct_difference; ?>%)
								</span>
								<?php } elseif ($followers_difference < 0) { ?>
								<span class="negative-difference">
									<?php echo $followers_difference; ?>
									(<?php echo $followers_pct_difference; ?>%)
								</span>
								<?php } ?>
							</span>
						</div>
						<div class="followers-comment">
							<p>
								<?php echo $text_followers_comment; ?>
							</p>
							<a href="https://instagsocialhelp.zendesk.com/hc/en-us/categories/203813048-Pro-Tips" target="_blank">
								<button class="gold-button">
									<?php echo $link_pro_tips; ?>
								</button>
							</a>
						</div>
					</div>
				</div>
				<div class="card" id="chart-card">
					<div class="card-header row">
						<div class="chart_title">
							<h4><?php echo $chart_title; ?></h4>
						</div>
					</div>
					<div class="card-content row">
						<i class="fa fa-refresh"></i>
					</div>
				</div>
			</div>
			<div class="row cards-row">
				<div  id="top-sources" class="card"
							ng-controller="TopSourcesController as topsources">
					<div class="card-header row">
						<h4>
							<?php echo $text_top_sources; ?>
						</h4>
						<div class="card-input dropdown">
							<button id="top-sources-range" class="btn btn-default dropdown-toggle"
											type="button" data-limit="unset" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
								<?php echo $text_last_30_days; ?>
								<span class="caret pull-right"></span>
							</button>
							<ul class="dropdown-menu" aria-labelledby="top-sources-range">
								<li ng-click="topsources.getSources('<?php echo $account_id; ?>','-1 day',$event)"><?php echo $text_yesterday; ?></li>
								<li ng-click="topsources.getSources('<?php echo $account_id; ?>','-7 days',$event)"><?php echo $text_last_7_days; ?></li>
								<li ng-click="topsources.getSources('<?php echo $account_id; ?>','-30 days',$event)"><?php echo $text_last_30_days; ?></li>
								<li ng-click="topsources.getSources('<?php echo $account_id; ?>','-60 days',$event)"><?php echo $text_last_60_days; ?></li>
							</ul>
						</div>
					</div>
					<div class="card-content row">
					</div>
				</div>
			</div>
		</div>
		<div  id="users" class="tab"
					ng-controller="UsersSourcesController as users">
			<div class="row cards-row">
				<div class="card cartoon-card">
					<div class="card-content row search-tool" id="search-users-sources">
						<div>
							<img src="catalog/view/theme/default/image/toons/guy-yellowmagnetuser.png">
							<?php echo $title_users_add; ?>
							<i  class="fa fa-video-camera"
									ng-controller="ModalsController as modals"
									ng-click="modals.helpModal($event)"
									ng-mouseover="modals.displayTooltip($event)"
									ng-mouseleave="modals.hideTooltip($event)"
									data-video-id="_ZM-zFN7iYk"
									data-tooltip="<?php echo $follow_tooltip; ?>"
									data-tooltip-position="right"
									data-tooltip-style="dark"
									data-hide-fire="mouseleave"
							></i>
							<?php echo $text_users_add; ?>
						</div>
						<div class="input-search">
							<i class="fa fa-search"></i>
							<input  type="text" placeholder="<?php echo $entry_search; ?>"
											ng-controller="SearchController as search"
											ng-model='searchUsers'
											ng-change='search.searchUsers(searchUsers,"search-users-sources","<?php echo $account_id; ?>")'>
							<div class="search-results">
							</div>
						</div>            
					</div>
				</div>
				<div class="card">
					<div class="card-content row">
						<div><?php echo $text_users_reset; ?></div>
						<div class="card-input" ng-controller="ModalsController as modals">
							<button class="gold-button" ng-click="modals.displayWarning('users-reset')"><?php echo $button_reset_users; ?></button>
						</div>
					</div>
				</div>
			</div>
			<hr class="clearfix">
			<div class="row cards-row" id="user-sources-list">
				<?php foreach ($follow_source_users as $user) { ?>
				<div class="user-list <?php echo str_replace('.','',$user->username); echo ' '.$user->id; ?>">
					<a href="http://www.instagram.com/<?php echo $user->username; ?>" target="_blank">
						<h4>
							<img src="catalog/view/theme/default/image/dashboard/default_user.jpg">
							<?php if (strlen($user->username) < 20) { ?>
							<?php echo $user->username; ?>
							<?php } else { ?>
							<?php echo substr($user->username,0,19).'...'; ?>
							<?php } ?>
						</h4>
					</a>
					<button class="gold-button"
									ng-controller="RemoveController as remove"
									ng-click="remove.removeFromList($event,'<?php echo $account_id; ?>','follow_source_users','<?php echo $user->id; ?>')">
						X
					</button>
				</div>
				<?php } ?>
			</div>
		</div>
		<div  id="hashtags" class="tab"
					ng-controller="TagsSourcesController as tags">
			<div class="row cards-row">
				<div class="card cartoon-card">
					<div id="search-hashtags-sources" class="card-content row search-tool">
						<div>
							<img src="catalog/view/theme/default/image/toons/guy-yellowmagnethash.png">
							<?php echo $title_hashtags_add; ?>
							<i  class="fa fa-video-camera"
									ng-controller="ModalsController as modals"
									ng-click="modals.helpModal($event)"
									ng-mouseover="modals.displayTooltip($event)"
									ng-mouseleave="modals.hideTooltip($event)"
									data-video-id="SE5SM_iNhkA"
									data-tooltip="<?php echo $follow_tooltip; ?>"
									data-tooltip-position="right"
									data-tooltip-style="dark"
									data-hide-fire="mouseleave"
							></i>
							<?php echo $text_hashtags_add; ?>
						</div>
						<div class="input-search">
							<i class="fa fa-search"></i>
							<input  type="text" placeholder="<?php echo $entry_search; ?>"
											ng-controller="SearchController as search"
											ng-model='searchHashtags'
											ng-change='search.searchTags(searchHashtags,"search-hashtags-sources","<?php echo $account_id; ?>")'>
							<div class="search-results"></div>
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-content row">
						<div><?php echo $text_hashtags_reset; ?></div>
						<div class="card-input"  ng-controller="ModalsController as modals">
							<button class="gold-button" ng-click="modals.displayWarning('tags-reset')"><?php echo $button_reset_hashtags; ?></button>
						</div>
					</div>
				</div>
			</div>
			<hr class="clearfix">
			<div class="row cards-row" id="hashtag-sources-list">
				<?php foreach ($follow_source_tags as $tag) { ?>
				<div class="user-list <?php echo str_replace('.','',$tag); ?>">
					<h4>
						<i class="fa fa-hashtag"></i>
						<?php if (strlen($tag) < 20) { ?>
						<?php   echo $tag; ?>
						<?php } else { ?>
						<?php echo substr($tag,0,19).'...'; ?>
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
		<div  id="locations" class="tab"
					ng-controller="LocationsController as locations">
			<div class="row cards-row">
				<div class="card cartoon-card">
					<div class="row card-content search-tool" id="search-locations">
						<div>
							<img src="catalog/view/theme/default/image/toons/guy-yellowmagnetmarker.png">
							<?php echo $title_locations_add; ?>
							<?php echo $text_locations_add; ?>
						</div>
						<div class="input-search">
							<i class="fa fa-search"></i>
							<input type="text" placeholder="<?php echo $entry_search; ?>"
										 ng-controller="SearchController as search"
										 ng-model='searchLocation'
										 ng-change='search.searchLocations(searchLocation,"search-locations","<?php echo $account_id; ?>")'>
							<div class="search-results">
							</div>
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-content row">
						<div><?php echo $text_locations_reset; ?></div>
						<div class="card-input"  ng-controller="ModalsController as modals">
							<button class="gold-button" ng-click="modals.displayWarning('locations-reset')"><?php echo $button_reset_locations; ?></button>
						</div>
					</div>
				</div>
			</div>
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
						<?php if (isset($location->subtitle)) { ?>
						<p>
							<?php if (strlen($location->subtitle) < 33) { ?>
							<?php echo $location->subtitle; ?>
							<?php } else { ?>
							<?php echo substr($location->subtitle,0,30).'...'; ?>
							<?php } ?>
						</p>
						<?php } ?>
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
		</div>
		<div  id="whitelist" class="tab"
					ng-controller="WhitelistController as whitelist">
			<div class="row cards-row">
				<div class="card cartoon-card">
					<div class="row card-content search-tool" id="search-whitelist">
						<div>
							<img src="catalog/view/theme/default/image/toons/guy-yellowangel.png">
							<?php echo $title_whitelist_add; ?>
							<i  class="fa fa-video-camera"
									ng-controller="ModalsController as modals"
									ng-click="modals.helpModal($event)"
									ng-mouseover="modals.displayTooltip($event)"
									ng-mouseleave="modals.hideTooltip($event)"
									data-video-id="X63OL14KPQc"
									data-tooltip="<?php echo $follow_tooltip; ?>"
									data-tooltip-position="right"
									data-tooltip-style="dark"
									data-hide-fire="mouseleave"
							></i>
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
						<div><?php echo $text_whitelist_import; ?></div>
						<div class="card-input dropdown">
							<button class="btn btn-default dropdown-toggle"
											type="button" id="whitelist-limit"
											data-limit="unset"
											data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
								<?php echo $button_import_users; ?>
								<span class="caret pull-right"></span>
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
				<div id="reset-whitelist" class="card">
					<div class="card-content row">
						<div><?php echo $text_whitelist_reset; ?></div>
						<div class="card-input"  ng-controller="ModalsController as modals">
							<button class="gold-button" ng-click="modals.displayWarning('whitelist-reset')"><?php echo $button_reset_whitelist; ?></button>
						</div>
					</div>
				</div>
			</div>
			<hr class="clearfix">
			<div class="row cards-row" id="whitelist-users-list">
				<?php foreach ($whitelist_users as $user) { ?>
				<div class="user-list <?php echo str_replace('.','',$user->username); echo ' '.$user->id; ?>">
					<a href="http://www.instagram.com/<?php echo $user->username; ?>" target="_blank">
						<h4>
							<img src="catalog/view/theme/default/image/dashboard/default_user.jpg">
							<?php if (strlen($user->username) < 20) { ?>
							<?php   echo $user->username; ?>
							<?php } else { ?>
							<?php echo substr($user->username,0,19).'...'; ?>
							<?php } ?>
						</h4>
					</a>
					<button class="gold-button"
									ng-controller="RemoveController as remove"
									ng-click="remove.removeFromList($event,'<?php echo $account_id; ?>','whitelist_users','<?php echo $user->id; ?>')">
						X
					</button>
				</div>
				<?php } ?>
			</div>
		</div>
		<div  id="settings" class="tab <?php echo $follow_unfollow_tab_class; ?>"
					ng-controller="SettingsTabController as settings">
			<div class="row card-section-title">
				<h4>
					<?php echo $text_follow; ?>
					<i  class="fa fa-video-camera"
							ng-controller="ModalsController as modals"
							ng-click="modals.helpModal($event)"
							ng-mouseover="modals.displayTooltip($event)"
							ng-mouseleave="modals.hideTooltip($event)"
							data-video-id="DF1f17u6AfY"
							data-tooltip="<?php echo $follow_tooltip; ?>"
							data-tooltip-position="right"
							data-tooltip-style="dark"
							data-hide-fire="mouseleave"
					></i>
				</h4>
			</div>
			<div class="row cards-row">
				<div class="card cartoon-card <?php echo $follow_speed; ?>" id="follow-range">
					<div class="card-content row">
						<div>
							<img src="catalog/view/theme/default/image/toons/guy-yellowsherlock.png"> <?php echo $text_follow_speed; ?>
						</div>
						<div class="card-input">
							<?php $follow_speed_value = 1; ?>
							<?php foreach ($speeds as $key => $speed) { ?>
							<?php $class = $speed['code']; ?>
							<?php if ($follow_speed == $speed['code']) { ?>
							<?php $follow_speed_value = $key + 1; ?>
							<?php $class .= ' displayed'; ?>
							<?php } ?>
							<span class="<?php echo $class; ?> speed-span" data-speed="<?php echo $speed['code']; ?>" data-speed-value="<?php echo $key + 1; ?>" data-valid="<?php echo (int)$speed['status']; ?>"><?php echo $speed['name']; ?></span>
							<?php } ?>
							<input type="range" value="<?php echo $follow_speed_value; ?>" min="1" max="3" step="1"
										 class="input-range <?php echo $follow_speed; ?>"
										 name="follow-range"
										 ng-model='follow'
										 ng-change="settings.changeSettings($event,'follow_speed','<?php echo $account_id; ?>',follow)">
							<p  id="follow-upgrade-plan"
									data-tab="billing"
                  data-tab-language="<?php echo $tab_billing; ?>"
									ng-controller="TabsController as tabs"
									ng-click="tabs.changeTab($event, 'middle')">
								<?php echo $text_speed_upgrade_plan; ?>
							</p>
						</div>
					</div>
				</div>
				<div class="card" id="follow-limit-container">
					<div class="card-content row">
						<div><?php echo $text_follow_limit; ?></div>
						<div class="card-input dropdown">
							<button class="btn btn-default dropdown-toggle" type="button" id="follow-limit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
								<?php echo $follows_max_limit; ?>
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu" aria-labelledby="follow-limit">
								<li ng-click="settings.changeSettings($event,'follow','<?php echo $account_id; ?>')">2000</li>
								<li ng-click="settings.changeSettings($event,'follow','<?php echo $account_id; ?>')">3000</li>
								<li ng-click="settings.changeSettings($event,'follow','<?php echo $account_id; ?>')">4000</li>
								<li ng-click="settings.changeSettings($event,'follow','<?php echo $account_id; ?>')">5000</li>
								<li ng-click="settings.changeSettings($event,'follow','<?php echo $account_id; ?>')">6000</li>
								<li ng-click="settings.changeSettings($event,'follow','<?php echo $account_id; ?>')">7000</li>
							</ul>
						</div>
						<div class="select-dropdown">
							<select ng-model="followLimit" ng-change="settings.updateSetting(followLimit,'follow-limit-container')" ng-init="followLimit='<?php echo $follows_max_limit; ?>'">
								<option value="2000" <?php if ($follows_max_limit == 2000) echo "selected ng-selected='true'"; ?>>2000</option>
								<option value="3000" <?php if ($follows_max_limit == 3000) echo "selected ng-selected='true'"; ?>>3000</option>
								<option value="4000" <?php if ($follows_max_limit == 4000) echo "selected ng-selected='true'"; ?>>4000</option>
								<option value="5000" <?php if ($follows_max_limit == 5000) echo "selected ng-selected='true'"; ?>>5000</option>
								<option value="6000" <?php if ($follows_max_limit == 6000) echo "selected ng-selected='true'"; ?>>6000</option>
								<option value="7000" <?php if ($follows_max_limit == 7000) echo "selected ng-selected='true'"; ?>>7000</option>
							</select>
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-content row">
						<div><?php echo $text_follow_private_users; ?></div>
						<div class="card-input">
							<div class="btn-group btn-group-justified follow-buttons" role="group" aria-label="">
								<button type="button" class="btn btn-default <?php if ($follow_no_private) echo 'active'; ?>"
												data-private="1" ng-click="settings.changeSettings($event,'follow_no_private','<?php echo $account_id; ?>')">
									<?php echo $button_no; ?>
								</button>
								<button type="button" class="btn btn-default <?php if (!$follow_no_private) echo 'active'; ?>"
												data-private="0" ng-click="settings.changeSettings($event,'follow_no_private','<?php echo $account_id; ?>')">
									<?php echo $button_yes; ?>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row card-section-title">
				<h4>
					<?php echo $text_unfollow; ?>
					<i  class="fa fa-video-camera"
							ng-controller="ModalsController as modals"
							ng-click="modals.helpModal($event)"
							ng-mouseover="modals.displayTooltip($event)"
							ng-mouseleave="modals.hideTooltip($event)"
							data-video-id="8chNTcLPXV8"
							data-tooltip="<?php echo $follow_tooltip; ?>"
							data-tooltip-position="right"
							data-tooltip-style="dark"
							data-hide-fire="mouseleave"
					></i>
				</h4>
			</div>
			<div class="row cards-row">
				<div class="card cartoon-card <?php echo $unfollow_speed; ?>" id="unfollow-range">
					<div class="card-content row">
						<div>
							<img src="catalog/view/theme/default/image/toons/guy-yellowhidding.png"> <?php echo $text_unfollow_speed; ?>
						</div>
						<div class="card-input">
							<?php $unfollow_speed_value = 1; ?>
							<?php foreach ($speeds as $key => $speed) { ?>
							<?php $class = $speed['code']; ?>
							<?php if ($unfollow_speed == $speed['code']) { ?>
							<?php $unfollow_speed_value = $key + 1; ?>
							<?php $class .= ' displayed'; ?>
							<?php } ?>
							<span class="<?php echo $class; ?> speed-span" data-speed="<?php echo $speed['code']; ?>" data-speed-value="<?php echo $key + 1; ?>" data-valid="<?php echo (int)$speed['status']; ?>"><?php echo $speed['name']; ?></span>
							<?php } ?>
							<input type="range" value="<?php echo $unfollow_speed_value; ?>" min="1" max="3" step="1"
										 class="input-range <?php echo $unfollow_speed; ?>"
										 name="unfollow-range"
										 ng-model='unfollow'
										 ng-change="settings.changeSettings($event, 'unfollow_speed', '<?php echo $account_id; ?>',unfollow)">
							<p id="unfollow-upgrade-plan"
									data-tab="billing"
									data-tab-language="<?php echo $tab_billing; ?>"
									ng-controller="TabsController as tabs"
									ng-click="tabs.changeTab($event, 'middle')">
									<?php echo $text_speed_upgrade_plan; ?>
							</p>
						</div>
					</div>
				</div>
				<div class="card" id="unfollow-limit-container">
					<div class="card-content row">
						<div><?php echo $text_unfollow_limit; ?></div>
						<div class="card-input dropdown">
							<button class="btn btn-default dropdown-toggle" type="button" id="follow-limit" data-toggle="dropdown"
											aria-haspopup="true" aria-expanded="true"
											data-limit="<?php echo $follows_min_limit; ?>">
								<?php echo $text_whitelist . (!empty($follows_min_limit) ? ' + ' . $follows_min_limit : ''); ?>
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu" aria-labelledby="follow-limit">
								<li data-limit="0" ng-click="settings.changeSettings($event,'unfollow-limit','<?php echo $account_id; ?>')"><?php echo $text_whitelist; ?></li>
								<li data-limit="100" ng-click="settings.changeSettings($event,'unfollow-limit','<?php echo $account_id; ?>')"><?php echo $text_whitelist; ?> + 100</li>
								<li data-limit="200" ng-click="settings.changeSettings($event,'unfollow-limit','<?php echo $account_id; ?>')"><?php echo $text_whitelist; ?> + 200</li>
								<li data-limit="300" ng-click="settings.changeSettings($event,'unfollow-limit','<?php echo $account_id; ?>')"><?php echo $text_whitelist; ?> + 300</li>
								<li data-limit="400" ng-click="settings.changeSettings($event,'unfollow-limit','<?php echo $account_id; ?>')"><?php echo $text_whitelist; ?> + 400</li>
								<li data-limit="500" ng-click="settings.changeSettings($event,'unfollow-limit','<?php echo $account_id; ?>')"><?php echo $text_whitelist; ?> + 500</li>
								<li data-limit="600" ng-click="settings.changeSettings($event,'unfollow-limit','<?php echo $account_id; ?>')"><?php echo $text_whitelist; ?> + 600</li>
								<li data-limit="700" ng-click="settings.changeSettings($event,'unfollow-limit','<?php echo $account_id; ?>')"><?php echo $text_whitelist; ?> + 700</li>
							</ul>
						</div>
						<div class="select-dropdown">
							<select ng-model="unfollowLimit" ng-change="settings.updateSetting(unfollowLimit,'unfollow-limit-container')" ng-init="unfollowLimit='<?php echo $follows_min_limit; ?>'">
								<option value="0"   <?php if ($follows_min_limit == 0)   echo "selected ng-selected='true'"; ?>><?php echo $text_whitelist; ?></option>
								<option value="100" <?php if ($follows_min_limit == 100) echo "selected ng-selected='true'"; ?>><?php echo $text_whitelist; ?> + 100</option>
								<option value="200" <?php if ($follows_min_limit == 200) echo "selected ng-selected='true'"; ?>><?php echo $text_whitelist; ?> + 200</option>
								<option value="300" <?php if ($follows_min_limit == 300) echo "selected ng-selected='true'"; ?>><?php echo $text_whitelist; ?> + 300</option>
								<option value="400" <?php if ($follows_min_limit == 400) echo "selected ng-selected='true'"; ?>><?php echo $text_whitelist; ?> + 400</option>
								<option value="500" <?php if ($follows_min_limit == 500) echo "selected ng-selected='true'"; ?>><?php echo $text_whitelist; ?> + 500</option>
								<option value="600" <?php if ($follows_min_limit == 600) echo "selected ng-selected='true'"; ?>><?php echo $text_whitelist; ?> + 600</option>
								<option value="700" <?php if ($follows_min_limit == 700) echo "selected ng-selected='true'"; ?>><?php echo $text_whitelist; ?> + 700</option>
							</select>
						</div>
					</div>
				</div>
				<div class="card" id="unfollow-source-container">
					<div class="card-content row">
						<div><?php echo $text_unfollow_source; ?></div>
						<div class="card-input dropdown">
							<button class="btn btn-default dropdown-toggle" type="button" id="unfollow-source" data-toggle="dropdown"
											aria-haspopup="true" aria-expanded="true"
											data-source="<?php echo $unfollow_source; ?>">
								<?php if ($unfollow_source == 'all') { ?>
								<?php echo $text_unfollow_all; ?>
								<?php } else { ?>
								<?php echo $text_unfollow_instag; ?>
								<?php } ?>
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu" aria-labelledby="follow-limit">
								<li data-source="all" ng-click="settings.changeSettings($event,'unfollow-source','<?php echo $account_id; ?>')"><?php echo $text_unfollow_all; ?></li>
								<li data-source="system" ng-click="settings.changeSettings($event,'unfollow-source','<?php echo $account_id; ?>')"><?php echo $text_unfollow_instag; ?></li>
							</ul>
						</div>
						<div class="select-dropdown">
							<select ng-model="unfollowSource" ng-change="settings.updateSetting(unfollowSource,'unfollow-source-container')" ng-init="unfollowSource='<?php echo $unfollow_source; ?>'">
								<option value="all"    <?php if ($unfollow_source == 'all')    echo "selected ng-selected='true'"; ?>><?php echo $text_unfollow_all; ?></option>
								<option value="system" <?php if ($unfollow_source == 'system') echo "selected ng-selected='true'"; ?>><?php echo $text_unfollow_instag; ?></option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="row card-section-title">
				<h4><?php echo $text_sleep; ?></h4>
			</div>
			<div class="row cards-row" id="sleep-settings">
				<div class="card cartoon-card">
					<div class="card-content row">
						<div>
							<img src="catalog/view/theme/default/image/toons/guy-yellowsleeping.png">
							<h3>
								<?php echo $text_sleep_card; ?>
							</h3>
							<p>
								<?php echo $text_sleep_card_description; ?>
							</p>
						</div>
						<div class="card-input">
							<div class="btn-group btn-group-justified" role="group" aria-label="">
								<button type="button" class="btn btn-default <?php if (!$sleep_status) echo 'active'; ?>"
												data-sleep="0" ng-click="settings.changeSettings($event,'sleep_status','<?php echo $account_id; ?>')">
									<?php echo $button_no; ?>
								</button>
								<button type="button" class="btn btn-default <?php if ($sleep_status) echo 'active'; ?>"
												data-sleep="1" ng-click="settings.changeSettings($event,'sleep_status','<?php echo $account_id; ?>')">
									<?php echo $button_yes; ?>
								</button>
							</div>
						</div>
					</div>
				</div>
				<div class="card sleep-time" id="sleep-time-container">
					<div class="card-content row">
						<div>
							<h3>
								<?php echo $text_sleep_time_card; ?>
							</h3>
							<p>
								<?php echo $text_sleep_time_description; ?>
							</p>
						</div>
						<div class="card-input dropup">
							<button class="btn btn-default dropdown-toggle" type="button" id="sleep-time" data-toggle="dropdown"
											aria-haspopup="true" aria-expanded="true"
											data-sleep="<?php echo $sleep_time; ?>">
											<?php foreach ($sleep_hours as $key => $value) { ?>
											<?php if ($value['utc'] == $sleep_time) { ?>
											<?php echo $value['label']; ?>
											<?php } ?>
											<?php } ?>
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu" aria-labelledby="sleep-time">
								<?php foreach ($sleep_hours as $key => $value) { ?>
								<li data-sleep="<?php echo $value['utc']; ?>" ng-click="settings.changeSettings($event,'sleep_time','<?php echo $account_id; ?>')">
									<?php echo $value['label']; ?>
								</li>
								<?php } ?>
							</ul>
						</div>
						<div class="select-dropdown">
							<select ng-model="sleepTime" ng-change="settings.updateSetting(sleepTime,'sleep-time-container')" ng-init="sleepTime='<?php echo $sleep_time; ?>'">
								<?php foreach ($sleep_hours as $key => $value) { ?>
								<option value="<?php echo $value['utc']; ?>" <?php if ($value['utc'] == $sleep_time) echo "selected ng-selected='true'"; ?>>
									<?php echo $value['label']; ?>
								</option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>
				<div class="card" id="sleep-duration-container">
					<div class="card-content row">
						<div>
							<h3>
								<?php echo $text_sleep_duration; ?>
							</h3>
							<p>
								<?php echo $text_sleep_duration_description; ?>
							</p>
						</div>
						<div class="card-input dropup">
							<button class="btn btn-default dropdown-toggle" type="button" id="sleep-duration" data-toggle="dropdown"
											aria-haspopup="true" aria-expanded="true"
											data-sleep="<?php echo $sleep_duration; ?>">
											<?php if ($sleep_duration == 1) { ?>
											<?php echo $sleep_duration.' '.$text_sleep_hour; ?>
											<?php } else { ?>
											<?php echo $sleep_duration.' '.$text_sleep_hours; ?>
											<?php } ?>
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu" aria-labelledby="sleep-duration">
								<?php for ($i=1;$i<=8;$i++) { ?>
								<li data-sleep="<?php echo $i; ?>" ng-click="settings.changeSettings($event,'sleep_duration','<?php echo $account_id; ?>')">
									<?php if ($i == 1) { ?>
									<?php echo $i.' '.$text_sleep_hour; ?>
									<?php } else { ?>
									<?php echo $i.' '.$text_sleep_hours; ?>
									<?php } ?>
								</li>
								<?php } ?>
							</ul>
						</div>
						<div class="select-dropdown">
							<select ng-model="sleepDuration" ng-change="settings.updateSetting(sleepDuration,'sleep-duration-container')" ng-init="sleepDuration='<?php echo $sleep_duration; ?>'">
								<?php for ($i=1;$i<=8;$i++) { ?>
								<option <?php if ($sleep_duration == $i) echo "selected ng-selected='true'"; ?> value="<?php echo $i; ?>">
									<?php if ($i == 1) { ?>
									<?php echo $i.' '.$text_sleep_hour; ?>
									<?php } else { ?>
									<?php echo $i.' '.$text_sleep_hours; ?>
									<?php } ?>
								</option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="tab" id="billing" ng-controller="BillingTabController as billing">
		</div>
	</div>
</div>
<div id="sidebar-username">
	<i class="fa fa-user"></i>
	<h4><?php echo $account_username; ?> </h4>
</div>
<aside  id="right-sidebar">
	<div  id="tab-header-sidebar" class="tab-header"
				ng-controller="TabsController as tabs">
		<h4 class="account current-tab" data-tab="account" data-tab-language="<?php echo $tab_account; ?>" ng-click="tabs.changeTab($event,'sidebar')">
			<?php echo $tab_account; ?>
		</h4>
		<div></div>
		<h4 class="activity request" data-tab="activity" data-tab-language="<?php echo $tab_activity; ?>" ng-click="tabs.changeTab($event,'sidebar')">
			<?php echo $tab_activity; ?>
		</h4>
	</div>
	<div id="right-sidebar-content" class="right-sidebar-content">
		<div class="tab-panel-container sidebar-tab" id="tab-body-sidebar">
			<div  class="tab current-tab" id="account"
						ng-controller="RightSidebarController as rightsidebar">
				<?php if ($account_plan_trial) { ?>
				<div  id="account-trial" class="sidebar-item cartoon-card"
							ng-controller="TabsController as tabs">
						<img src="catalog/view/theme/default/image/toons/guy-yellowcelebrating.png" alt="">
						<h3>
							<?php echo $text_free_trial; ?>
						</h3>
						<p>
							<?php echo $text_free_trial_message; ?>
						</p>
						<button class="gold-button <?php echo (($account_plan_trial_days_remaining) < 1 ? 'red-button' : ''); ?>" data-tab="billing" ng-click="tabs.changeTab($event, 'middle')">
							<?php echo $button_choose_plan; ?>
						</button>
						<div class="separator">
							<hr>
							<small>
								<?php echo $text_time_left; ?>
							</small>
						</div>
						<div id="countdown">
							<div class="time-countdown">
								<h4>
									<?php echo $text_days; ?>
								</h4>
							</div>
							<div class="time-countdown hours-countdown">
								<h4>
									<?php echo $text_hours; ?>
								</h4>
							</div>
							<div class="time-countdown">
								<h4>
									<?php echo $text_minutes; ?>
								</h4>
							</div>
							<div class="time-countdown">
								<p>
									<?php echo $account_plan_trial_days_remaining; ?>
								</p>
							</div>
							<div class="time-countdown">
								<p>
									<?php echo $account_plan_trial_hours_remaining; ?>
								</p>
							</div>
							<div class="time-countdown">
								<p>
									<?php echo $account_plan_trial_minutes_remaining; ?>
								</p>
							</div>
						</div>
				</div>
				<?php } ?>
				<div class="sidebar-item" id="account-data">
					<div id="pictures-fee">
						<div class="row nomargin">
							<?php $picture_classes = array('top left', 'top left', 'top left', 'top', 'left', 'left', 'left', 'bot'); ?>
							<?php foreach ($picture_classes as $key => $picture_class) { ?>
							<?php if ($key && ($key % 4) == 0) { ?>
							</div>
							<div class="row nomargin">
							<?php } ?>
							<div class="pic-container <?php echo $picture_class; ?>">
								<?php if (isset($recent_media[$key])) { ?>
								<img src="<?php echo $recent_media[$key]; ?>">
								<?php } ?>
							</div>
							<?php } ?>
						</div>
						<div class="dark-filter"></div>
						<a  href="<?php echo $href_account_profile; ?>" target="_blank" id="profile-link"
								ng-controller="TooltipsController as tooltip"
								ng-mouseover="tooltip.displayTooltip($event)"
								ng-mouseleave="tooltip.hideTooltip($event)"
								data-tooltip="<?php echo $tooltip_profile; ?>"
								data-tooltip-position="left"
								data-tooltip-style="dark"
								data-tooltip-offset="70"
								data-hide-fire="mouseleave">
							<div id="profile-pic"><img src="<?php echo $profile_picture; ?>"></div>
						</a>
					</div>
					<div id="account-stats">
						<div class="row nomargin stats-header">
						<div class="column">
							<h4><?php echo $text_posts; ?></h4>
						</div>
						<div class="column center-column">
							<h4><?php echo $text_followers; ?></h4>
						</div>
						<div class="column">
							<h4><?php echo $text_following; ?></h4>
						</div>
						</div>
						<div class="row nomargin">
						<div class="column">
							<h4><?php echo $posts; ?></h4>
						</div>
						<div class="column">
							<h4><?php echo $followers; ?></h4>
						</div>
						<div class="column">
							<h4><?php echo $follows; ?></h4>
						</div>
						</div>
					</div>
				</div>  
				<?php foreach ($speeds as $key => $speed) { ?>
				<?php if ($speed['code'] == 'fast' && (int)$speed['status'] == 0 && !$account_plan_trial && $account_info->StatusMessage != 'expired') { ?>
					<button id="upgrade-ludicrous" class="gold-button" data-tab="billing"
									data-tab-language="<?php echo $tab_billing; ?>"
									ng-controller="TabsController as tabs"
									ng-click="tabs.billingTab($event)">
						<i class="fa fa-bolt"></i>
						<p>
							<?php echo $text_upgrade_rightsidebar; ?>
						</p>
					</button>
				<?php } ?>
				<?php } ?>
				<div class="sidebar-item">
					<div class="account-status row nomargin run-status">
						<i class="fa fa-<?php echo $account_status; ?> account-status-icon"
							<?php if ($account_tooltip) { ?>
								ng-controller="TooltipsController as tooltip"
								ng-mouseover="tooltip.displayTooltip($event)"
								ng-mouseleave="tooltip.hideTooltip($event)"
								data-tooltip="<?php echo $account_tooltip; ?>"
								data-tooltip-position="left"
								data-tooltip-style="dark"
								data-tooltip-offset="90"
								data-hide-fire="mouseleave"
							<?php } ?>
							></i>
						<div class="item-content">
							<h4><?php echo $text_status; ?></h4>
							<p><?php echo $account_flow; ?></p>
						</div>
						<button id="change-status-btn" class="<?php echo $account_action; ?>-button" data-action="<?php echo $account_action; ?>" ng-click="rightsidebar.accountStatus($event,'<?php echo $account_id; ?>')">
							<?php echo $account_action_button; ?>
						</button>
					</div>
					<div class="account-status row nomargin">
						<i class="fa fa-arrow-up account-status-icon"></i>
						<div class="item-content">
							<h4><?php echo $text_follow; ?></h4>
							<p id="right-sidebar-follow-speed">
								<?php foreach ($speeds as $speed) { ?>
								<?php if ($follow_speed == $speed['code']) { ?>
								<?php echo $speed['name']; ?>
								<?php } ?>
								<?php } ?>
							</p>
						</div>
						<div class="following-status">
							<button id="follow-button" class="<?php  if ($account_info->Flow == 'follow') echo 'active'; else echo 'inactive'; ?>"
											ng-click="rightsidebar.changeFlow('follow','<?php echo $account_id; ?>')"></button>
							<p><?php echo $account_info->CountFollow; ?></p>
						</div>
					</div>
					<div class="account-status row nomargin unfollow-status">
						<i class="fa fa-arrow-down account-status-icon"></i>
						<div class="item-content">
							<h4><?php echo $text_unfollow; ?></h4>
							<p id="right-sidebar-unfollow-speed">
								<?php foreach ($speeds as $speed) { ?>
								<?php if ($unfollow_speed == $speed['code']) { ?>
								<?php echo $speed['name']; ?>
								<?php } ?>
								<?php } ?>
							</p>
						</div>
						<div class="following-status">
							<button id="unfollow-button" class="<?php if ($account_info->Flow == 'unfollow') echo 'active'; else echo 'inactive'; ?>"
											ng-click="rightsidebar.changeFlow('unfollow','<?php echo $account_id; ?>')"></button>
								<p><?php echo $account_info->CountUnfollow; ?></p>
						</div>
					</div>
					<div id="like-toggle" class="row nomargin">
						<i class="fa fa-heart account-status-icon"></i>
						<div class="item-content">
							<h4><?php echo $text_like; ?></h4>
							<p ng-controller="ModalsController as modals">
								 <a ng-click="modals.displayWarning('like-toggle-modal')"><?php echo $text_toggle_description; ?></a>
							</p>
						</div>
						<div class="toggle">
							<button class="<?php if (!empty($like_status)) echo 'active'; else echo 'inactive'; ?>"
											ng-click="rightsidebar.changeSettings($event,'like_status','<?php echo $account_id; ?>')"></button>
						</div>
					</div>
				</div>
				<div class="hidden">
					<span id="timeago-seconds" data-language="<?php echo $text_seconds_ago; ?>"></span>
					<span id="timeago-minute" data-language="<?php echo $text_minute_ago; ?>"></span>
					<span id="timeago-minutes" data-language="<?php echo $text_minutes_ago; ?>"></span>
					<span id="timeago-hour" data-language="<?php echo $text_hour_ago; ?>"></span>
					<span id="timeago-hours" data-language="<?php echo $text_hours_ago; ?>"></span>
					<span id="timeago-day" data-language="<?php echo $text_day_ago; ?>"></span>
					<span id="timeago-days" data-language="<?php echo $text_days_ago; ?>"></span>
				</div>
				<div id="history-content" data-offset="<?php echo $customer_timezone_offset; ?>">
					<?php foreach ($events as $event) { ?>
					<?php $event_icon = $event['code']; ?>
					<?php if ($event['message']) { ?>
					<?php $event_icon .= '_' . $event['message']; ?>
					<?php } ?>
					<div class="sidebar-item history-item">
						<i class="fa fa-event-<?php echo $event_icon; ?> history-icon"></i>
						<div class="item-content">
							<h4><?php echo $event['title']; ?></h4>
							<p><?php echo $event['description']; ?></p>
							<small data-time="<?php echo $event['date_added']; ?>"></small>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
			<div  class="tab activity-feed" id="activity"
						ng-controller="TabsController as tabs">
				<div class="btn-group btn-group-justified" role="group" aria-label="">
						<button type="button" class="active btn btn-default"
										data-feed="activity"
										ng-click="tabs.requestActivity('activity');tabs.switchBtns($event)">
							<?php echo $button_actions; ?>
						</button>
					 <button type="button" class="btn btn-default"
										data-feed="followback"
										ng-click="tabs.requestActivity('followback');tabs.switchBtns($event)">
							<?php echo $button_followbacks; ?>
						</button>
				</div>
				<button class="refresh-act-feed gold-button" ng-click="tabs.updateFeed($event)">
					<i class="fa fa-refresh"></i> <?php echo $text_refresh_feed; ?>
				</button>
				<div id="activity-content" class="activity-content"></div>
			</div>
		</div>
	</div>
</aside>

<div  id="reconnect-modal" class="modal fade"
			ng-controller="ModalsController as modals"
			ng-click="modals.closeModal($event)">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<i class="fa fa-instagram"></i>
				<h4 class="modal-title" id="add-instagram-label"><?php echo $text_reconnect_title; ?></h4>
				<p><?php echo $text_reconnect_description; ?></p>
			</div>
			<form action="" novalidate onsubmit="return false">
				<div class="modal-body"
						 ng-controller="TooltipsController as tooltip">
					<div class="form-group">
						<div  class="username-tooltip"
									ng-click="tooltip.displayTooltip($event)"
									ng-mouseover="tooltip.hideTooltip($event)"
									data-tooltip="<?php echo $empty_username_tooltip; ?>"
									data-tooltip-position="top"
									data-tooltip-style="light wide"
									data-hide-fire="mouseover"></div>
						<input type="text" id="instagram_username" name="username"
									 placeholder="<?php echo $entry_username; ?>" class="form-control"
									 ng-click="tooltip.hideTooltip($event)"
									 data-hide-fire="click">
						<i class="fa fa-instagram"></i>
					</div>
					<div class="form-group">
						<div  class="password-tooltip"
									ng-click="tooltip.displayTooltip($event)"
									ng-mouseover="tooltip.hideTooltip($event)"
									data-tooltip="<?php echo $empty_password_tooltip; ?>"
									data-tooltip-position="top"
									data-tooltip-style="light wide"
									data-hide-fire="mouseover"></div>
						<input type="password" id="instagram_password" name="password"
									 placeholder="<?php echo $entry_password; ?>" class="form-control"
									 ng-click="tooltip.hideTooltip($event)"
									 data-hide-fire="click">
						<i class="fa fa-lock"></i>
					</div>
				</div>
				<div class="modal-footer">
					<div class="tooltip light">
						<p></p>
					</div>
					<div  id="reconnect-error"
								ng-click="tooltip.displayTooltip($event)"
								ng-mouseover="tooltip.hideTooltip($event)"
								data-tooltip=""
								data-tooltip-position="bottom"
								data-tooltip-style="light wide"
								data-hide-fire="mouseover"></div>
					<button id="button-reconnect" type="submit" class="btn btn-primary"
									ng-click="modals.reconnectAccount($event,'<?php echo $account_id; ?>')">
						<?php echo $button_reconnect; ?>
					</button>
					<p  class="password-politics">
						<?php echo $save_passwords_politic; ?>
					</p>
				</div>
			</form>
		</div>
	</div>
</div>

<div  id="expired-modal" class="modal warning-modal"
			ng-controller="ModalsController as modals"
			ng-click="modals.closeModal($event)">
	<div class="modal-dialog">
		<div class="modal-content">
			<?php if ($show_billing) { ?>
			<div class="modal-header">
				<div class="cartoon-card">
					<img src="catalog/view/theme/default/image/toons/guy-yellowcrying.png">
				</div>
				<?php echo $expired_modal_text; ?>
			</div>
			<div class="modal-footer">
				<button class="gold-button"
								data-tab="billing"
								data-tab-language="<?php echo $tab_billing; ?>"
								ng-controller="TabsController as tabs"
								ng-click="tabs.billingTab($event);modals.closeModal();">
					<?php echo $expired_modal_button; ?>
				</button>        
			</div>
			<?php } else { ?>
			<div class="modal-header">
				<div class="cartoon-card">
					<img src="catalog/view/theme/default/image/toons/guy-yellowcrying.png">
				</div>
				<?php echo $expired_modal_nobilling_text; ?>
			</div>
			<?php } ?>
		</div>
	</div>
</div>

<div  id="upgrade-modal" class="modal warning-modal"
			ng-controller="ModalsController as modals"
			ng-click="modals.closeModal($event)">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="cartoon-card">
					<img src="catalog/view/theme/default/image/toons/guy-yellowracer.png">
				</div>
				<?php echo $upgrade_modal_text; ?>
			</div>
			<div class="modal-footer">
				<button class="gold-button"
								data-tab="billing"
								data-tab-language="<?php echo $tab_billing; ?>"
								ng-controller="TabsController as tabs"
								ng-click="tabs.billingTab($event);modals.closeModal();">
					<?php echo $upgrade_modal_button; ?>
				</button>        
			</div>
		</div>
	</div>
</div>

<div id="warning-whitelist" class="modal warning-modal"
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
				<button class="cancel" ng-click="modals.cancelAction('warning-whitelist')"><?php echo $button_no; ?></button>
				<button class="confirm"
								ng-controller="WhitelistController as whitelist"
								ng-click="whitelist.checkWhitelist('<?php echo $account_id; ?>')">
					<?php echo $button_yes; ?>
				</button>
			</div>
		</div>
	</div>
</div>

<div  id="whitelist-reset" class="modal warning-modal"
			ng-controller="ModalsController as modals"
			ng-click="modals.closeModal($event)">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<?php echo $text_whitelist_reset_confirm; ?>
			</div>
			<div class="modal-footer">
				<button class="cancel" ng-click="modals.cancelAction('whitelist-reset')"><?php echo $button_no; ?></button>
				<button class="confirm"
								ng-controller="RemoveController as remove"
								ng-click="remove.clearList('<?php echo $account_id; ?>','whitelist_users')">
					<?php echo $button_yes; ?>
				</button>
			</div>
		</div>
	</div>
</div>

<div  id="users-reset" class="modal warning-modal"
			ng-controller="ModalsController as modals"
			ng-click="modals.closeModal($event)">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<?php echo $text_users_reset_confirm; ?>
			</div>
			<div class="modal-footer">
				<button class="cancel" ng-click="modals.cancelAction('users-reset')"><?php echo $button_no; ?></button>
				<button class="confirm"
								ng-controller="RemoveController as remove"
								ng-click="remove.clearList('<?php echo $account_id; ?>','follow_source_users')">
					<?php echo $button_yes; ?>
				</button>
			</div>
		</div>
	</div>
</div>

<div  id="tags-reset" class="modal warning-modal"
			ng-controller="ModalsController as modals"
			ng-click="modals.closeModal($event)">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<?php echo $text_hashtags_reset_confirm; ?>
			</div>
			<div class="modal-footer">
				<button class="cancel" ng-click="modals.cancelAction('tags-reset')"><?php echo $button_no; ?></button>
				<button class="confirm"
								ng-controller="RemoveController as remove"
								ng-click="remove.clearList('<?php echo $account_id; ?>','follow_source_tags')">
					<?php echo $button_yes; ?>
				</button>
			</div>
		</div>
	</div>
</div>

<div  id="locations-reset" class="modal warning-modal"
			ng-controller="ModalsController as modals"
			ng-click="modals.closeModal($event)">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<?php echo $text_locations_reset_confirm; ?>
			</div>
			<div class="modal-footer">
				<button class="cancel" ng-click="modals.cancelAction('locations-reset')"><?php echo $button_no; ?></button>
				<button class="confirm"
								ng-controller="RemoveController as remove"
								ng-click="remove.clearList('<?php echo $account_id; ?>','follow_source_locations')">
					<?php echo $button_yes; ?>
				</button>
			</div>
		</div>
	</div>
</div>

<div  id="billing-modal" class="modal warning-modal"
			ng-controller="ModalsController as modals"
			ng-click="modals.closeModal($event)">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<?php echo $text_payment_modal; ?>
			</div>
			<div class="modal-footer text-center">
				<button class="gold-button"
								ng-controller="TooltipsController as tooltips"
								ng-click="tooltips.showChat()">
					<?php echo $button_payment_modal; ?>
				</button>
			</div>
		</div>
	</div>
</div>

<div  id="like-toggle-modal" class="modal warning-modal"
			ng-controller="ModalsController as modals"
			ng-click="modals.closeModal($event)">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<p>
					<?php echo $text_toggle_modal; ?>
				</p>
			</div>
		</div>
	</div>
</div>

<div  id="source-remove" class="modal warning-modal"
			ng-controller="ModalsController as modals"
			ng-click="modals.closeModal($event)">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<?php echo $text_remove_source; ?>
			</div>
			<div class="modal-footer">
				<button class="cancel" ng-click="modals.cancelAction('source-remove')"><?php echo $button_no; ?></button>
				<button class="confirm"
								data-source=""
								data-type=""
								ng-controller="RemoveController as remove"
								ng-click="remove.removeFromWarning($event,'<?php echo $account_id; ?>')">
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