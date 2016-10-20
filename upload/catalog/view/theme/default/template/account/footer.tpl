<?php foreach ($scripts as $script) { ?>
<script src="<?php echo $script; ?>"></script>
<?php } ?>
<!--Start of Zopim Live Chat Script-->
<script>
var ua = navigator.userAgent.toLowerCase(),
platform = navigator.platform.toLowerCase();
platformName = ua.match(/ip(?:ad|od|hone)/) ? 'ios' : (ua.match(/(?:webos|android)/) || platform.match(/mac|win|linux/) || ['other'])[0],
isMobile = /ios|android|webos/.test(platformName);
if (!isMobile) {
	window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
	d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
	_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute("charset","utf-8");
	$.src="//v2.zopim.com/?3HggoosuvoTGenLsSCxNi1kda8kKrnec";z.t=+new Date;$.
	type="text/javascript";e.parentNode.insertBefore($,e)})(document,"script");
}
</script>
<!--End of Zopim Live Chat Script-->
</body></html> 