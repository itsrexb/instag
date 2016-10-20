<ul id="menu">
  <li id="dashboard"><a href="<?php echo $home; ?>"><i class="fa fa-dashboard fa-fw"></i> <span><?php echo $text_dashboard; ?></span></a></li>
  <li id="catalog"><a class="parent"><i class="fa fa-tags fa-fw"></i> <span><?php echo $text_catalog; ?></span></a>
    <ul>
      <li><a href="<?php echo $capability; ?>"><?php echo $text_capability; ?></a></li>
      <li><a href="<?php echo $category; ?>"><?php echo $text_category; ?></a></li>
      <li><a href="<?php echo $information; ?>"><?php echo $text_information; ?></a></li>
      <li><a href="<?php echo $product; ?>"><?php echo $text_product; ?></a></li>
      <li><a href="<?php echo $recurring; ?>"><?php echo $text_recurring; ?></a></li>
      <li><a href="<?php echo $source_interest; ?>"><?php echo $text_source_interest; ?></a></li>
    </ul>
  </li>
  <li id="extension"><a class="parent"><i class="fa fa-puzzle-piece fa-fw"></i> <span><?php echo $text_extension; ?></span></a>
    <ul>
      <li><a href="<?php echo $analytics; ?>"><?php echo $text_analytics; ?></a></li>
      <li><a href="<?php echo $fraud; ?>"><?php echo $text_fraud; ?></a></li>
      <li><a href="<?php echo $captcha; ?>"><?php echo $text_captcha; ?></a></li>
      <li><a href="<?php echo $conversion; ?>"><?php echo $text_conversion; ?></a></li>
      <li><a href="<?php echo $feed; ?>"><?php echo $text_feed; ?></a></li>
      <li><a href="<?php echo $marketing_extension; ?>"><?php echo $text_marketing; ?></a></li>
      <li><a href="<?php echo $module; ?>"><?php echo $text_module; ?></a></li>
      <li><a href="<?php echo $total; ?>"><?php echo $text_total; ?></a></li>
      <li><a href="<?php echo $payment; ?>"><?php echo $text_payment; ?></a></li>
    </ul>
  </li>
  <li id="design"><a class="parent"><i class="fa fa-television fa-fw"></i> <span><?php echo $text_design; ?></span></a>
    <ul>
      <li><a href="<?php echo $layout; ?>"><?php echo $text_layout; ?></a></li>
      <li><a href="<?php echo $banner; ?>"><?php echo $text_banner; ?></a></li>
    </ul>
  </li>
  <li id="sale"><a class="parent"><i class="fa fa-shopping-cart fa-fw"></i> <span><?php echo $text_sale; ?></span></a>
    <ul>
      <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
      <li><a href="<?php echo $recurring_order; ?>"><?php echo $text_recurring_order; ?></a></li>
      <li><a class="parent"><?php echo $text_voucher; ?></a>
        <ul>
          <li><a href="<?php echo $voucher; ?>"><?php echo $text_voucher; ?></a></li>
          <li><a href="<?php echo $voucher_theme; ?>"><?php echo $text_voucher_theme; ?></a></li>
        </ul>
      </li>
    </ul>
  </li>
  <li id="customer"><a class="parent"><i class="fa fa-user fa-fw"></i> <span><?php echo $text_customer; ?></span></a>
    <ul>
      <li><a href="<?php echo $customer; ?>"><?php echo $text_customer; ?></a></li>
      <li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
      <li><a href="<?php echo $customer_group; ?>"><?php echo $text_customer_group; ?></a></li>
      <li><a href="<?php echo $custom_field; ?>"><?php echo $text_custom_field; ?></a></li>
    </ul>
  </li>
  <li id="affiliate"><a class="parent"><i class="fa fa-user fa-fw"></i> <span><?php echo $text_affiliate; ?></span></a>
    <ul>
      <li><a href="<?php echo $affiliate; ?>"><?php echo $text_affiliate; ?></a></li>
      <li><a href="<?php echo $affiliate_group; ?>"><?php echo $text_affiliate_group; ?></a></li>
    </ul>
  </li>
  <li><a class="parent"><i class="fa fa-share-alt fa-fw"></i> <span><?php echo $text_marketing; ?></span></a>
    <ul>
      <li><a href="<?php echo $marketing; ?>"><?php echo $text_marketing; ?></a></li>
      <li><a href="<?php echo $coupon; ?>"><?php echo $text_coupon; ?></a></li>
    </ul>
  </li>
  <li id="system"><a class="parent"><i class="fa fa-cog fa-fw"></i> <span><?php echo $text_system; ?></span></a>
    <ul>
      <li><a href="<?php echo $setting; ?>"><?php echo $text_setting; ?></a></li>
      <li><a href="<?php echo $url_alias; ?>"><?php echo $text_url_alias; ?></a></li>
      <li><a class="parent"><?php echo $text_users; ?></a>
        <ul>
          <li><a href="<?php echo $user; ?>"><?php echo $text_user; ?></a></li>
          <li><a href="<?php echo $user_group; ?>"><?php echo $text_user_group; ?></a></li>
          <li><a href="<?php echo $api; ?>"><?php echo $text_api; ?></a></li>
        </ul>
      </li>
      <li><a class="parent"><?php echo $text_localisation; ?></a>
        <ul>
          <li><a href="<?php echo $location; ?>"><?php echo $text_location; ?></a></li>
          <li><a href="<?php echo $language; ?>"><?php echo $text_language; ?></a></li>
          <li><a href="<?php echo $currency; ?>"><?php echo $text_currency; ?></a></li>
          <li><a href="<?php echo $order_status; ?>"><?php echo $text_order_status; ?></a></li>
          <li><a href="<?php echo $country; ?>"><?php echo $text_country; ?></a></li>
          <li><a href="<?php echo $zone; ?>"><?php echo $text_zone; ?></a></li>
          <li><a href="<?php echo $geo_zone; ?>"><?php echo $text_geo_zone; ?></a></li>
          <li><a class="parent"><?php echo $text_tax; ?></a>
            <ul>
              <li><a href="<?php echo $tax_class; ?>"><?php echo $text_tax_class; ?></a></li>
              <li><a href="<?php echo $tax_rate; ?>"><?php echo $text_tax_rate; ?></a></li>
            </ul>
          </li>
        </ul>
      </li>
      <li><a class="parent"><?php echo $text_tools; ?></a>
        <ul>
          <li><a href="<?php echo $upload; ?>"><?php echo $text_upload; ?></a></li>
          <li><a href="<?php echo $backup; ?>"><?php echo $text_backup; ?></a></li>
          <li><a href="<?php echo $error_log; ?>"><?php echo $text_error_log; ?></a></li>
        </ul>
      </li>
    </ul>
  </li>
  <li id="reports"><a class="parent"><i class="fa fa-bar-chart-o fa-fw"></i> <span><?php echo $text_reports; ?></span></a>
    <ul>
      <li><a class="parent"><?php echo $text_sale; ?></a>
        <ul>
          <li><a href="<?php echo $report_sale_profit; ?>"><?php echo $text_report_sale_profit; ?></a></li>
          <li><a href="<?php echo $report_sale_order; ?>"><?php echo $text_report_sale_order; ?></a></li>
          <li><a href="<?php echo $report_sale_tax; ?>"><?php echo $text_report_sale_tax; ?></a></li>
          <li><a href="<?php echo $report_sale_coupon; ?>"><?php echo $text_report_sale_coupon; ?></a></li>
        </ul>
      </li>
      <li><a class="parent"><?php echo $text_product; ?></a>
        <ul>
          <li><a href="<?php echo $report_product_purchased; ?>"><?php echo $text_report_product_purchased; ?></a></li>
        </ul>
      </li>
      <li><a class="parent"><?php echo $text_customer; ?></a>
        <ul>
          <li><a href="<?php echo $report_customer_profit; ?>"><?php echo $text_report_customer_profit; ?></a></li>          
          <li><a href="<?php echo $report_customer_online; ?>"><?php echo $text_report_customer_online; ?></a></li>
          <li><a href="<?php echo $report_customer_activity; ?>"><?php echo $text_report_customer_activity; ?></a></li>
          <li><a href="<?php echo $report_customer_order; ?>"><?php echo $text_report_customer_order; ?></a></li>
          <li><a href="<?php echo $report_customer_reward; ?>"><?php echo $text_report_customer_reward; ?></a></li>
          <li><a href="<?php echo $report_customer_credit; ?>"><?php echo $text_report_customer_credit; ?></a></li>
        </ul>
      </li>
      <li><a class="parent"><?php echo $text_account; ?></a>
        <ul>
          <li><a href="<?php echo $report_account_decline; ?>"><?php echo $text_report_account_decline; ?></a></li>
          <li><a href="<?php echo $report_account_follower; ?>"><?php echo $text_report_account_follower; ?></a></li>
          <li><a href="<?php echo $report_account_source_interest; ?>"><?php echo $text_source_interest; ?></a></li>
        </ul>
      </li>      
      <li><a class="parent"><?php echo $text_marketing; ?></a>
        <ul>
          <li><a href="<?php echo $report_marketing; ?>"><?php echo $text_marketing; ?></a></li>
          <li><a href="<?php echo $report_affiliate; ?>"><?php echo $text_report_affiliate; ?></a></li>
          <li><a href="<?php echo $report_affiliate_activity; ?>"><?php echo $text_report_affiliate_activity; ?></a></li>
        </ul>
      </li>
    </ul>
  </li>
</ul>