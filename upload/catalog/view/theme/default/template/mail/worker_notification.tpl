<div style="background: #fff; color: #666; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px;">
  <div style="background: #8137B0; margin-bottom: 20px; padding: 17px 20px 18px 20px;">
    <div style="max-width: 666px; margin: 0 auto;"><a href="<?php echo $href_home; ?>" title="<?php echo $site_name; ?>">
      <img src="<?php echo $logo; ?>" alt="<?php echo $site_name; ?>" style="border: none; max-height: 25px;" /></a>
    </div>
  </div>
  <div style="min-width: 320px; max-width: 680px; margin: 0 auto; text-align: left;">
    <p style="margin-top: 0; margin-bottom: 0; font-size: 16px; font-weight: bold; padding: 7px;"><?php echo $firstname; ?> <?php echo $lastname; ?>,</p>

    <table style="border-collapse: collapse; width: 100%; margin-bottom: 20px;">
      <tbody>
        <tr>
          <td style="font-size: 12px; text-align: left; padding: 7px;"><?php echo $message; ?></td>
        </tr>
        <tr>
          <td style="font-size: 12px; text-align: left; padding: 20px 7px;">
          <a href="<?php echo $href_dashboard; ?>" style="
            background-color: #ffbc0f;
            border: 1px solid transparent;
            border-radius: 4px;
            color: #fff;
            cursor: pointer;
            display: inline-block;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.42857;
            margin-bottom: 0;
            padding: 6px 12px;
            text-align: center;
            text-decoration: none;
            vertical-align: middle;
            white-space: nowrap;"><?php echo $button_account; ?></a></td>
        </tr>     
        <tr>
          <td style="font-size: 12px; text-align: left; padding: 7px;"><?php echo $text_support; ?></td>
        </tr>
        <tr>
          <td style="font-size: 12px; text-align: left; padding: 7px;">
          <?php echo $text_sincerely; ?>, <br />
          <b><?php echo $text_team; ?></b>
          </td>
        </tr>
      </tbody>
    </table>

    <p style="margin-top: 0px; margin-bottom: 20px; text-align: center;"><?php echo $text_footer; ?></p>
  </div>
</div>