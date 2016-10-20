    <footer>
      <div class="container">
        <div class="row">
          <div class="footer-first-half">
            <div id="footer-logo">
              <img src="<?php echo $logo; ?>">
              <span>&copy; <?php echo date('Y'); ?></span>
            </div>
            <div class="footer-links">
              <?php foreach ($informations as $key => $value) { ?>
              <div class="footer-link">
                <a href="<?php echo $value['href']; ?>">
                  <span><?php echo $value['title']; ?></span>
                </a>
              </div>
              <span class="bullet">&bullet;</span>
              <?php } ?>
              <div class="footer-link">
                <a href="<?php echo $contact; ?>">
                  <span><?php echo $text_contact; ?></span>
                </a>
              </div>
            </div>
          </div>
          <div class="footer-second-half">
            <span>Made With <i class="fa fa-heart"></i> In Utah</span>
          </div>
        </div>
      </div>
    </footer>
    <script src="catalog/view/theme/default/dist/js/common/libraries.min.<?php echo filemtime('catalog/view/theme/default/dist/js/common/libraries.min.js'); ?>.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular-touch.min.js"></script>
    <script src="catalog/view/theme/default/dist/js/common.min.<?php echo filemtime('catalog/view/theme/default/dist/js/common.min.js'); ?>.js"></script>
    <?php foreach ($scripts as $script) { ?>
    <script src="<?php echo $script; ?>"></script>
    <?php } ?>
    <!--Start of Zopim Live Chat Script-->
    <script>
    window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
    d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
    _.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute("charset","utf-8");
    $.src="//v2.zopim.com/?3HggoosuvoTGenLsSCxNi1kda8kKrnec";z.t=+new Date;$.
    type="text/javascript";e.parentNode.insertBefore($,e)})(document,"script");
    </script>
    <!--End of Zopim Live Chat Script-->
  </body>
</html>