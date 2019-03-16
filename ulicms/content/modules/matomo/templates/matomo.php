<?php
$matomo_url = Settings::get("matomo_url", "str");
$matomo_site_id = Settings::get("matomo_site_id", "int");
?>
<!-- Matomo -->
<script type="text/javascript">
    var _paq = window._paq || [];
    /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
    _paq.push(['trackPageView']);
    _paq.push(['enableLinkTracking']);
    (function () {
        var u = "//<?php Template::escape($matomo_url); ?>/";
        _paq.push(['setTrackerUrl', u + 'matomo.php']);
        _paq.push(['setSiteId', '<?php echo $matomo_site_id; ?>']);
        var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
        g.type = 'text/javascript';
        g.async = true;
        g.defer = true;
        g.src = u + 'matomo.js';
        s.parentNode.insertBefore(g, s);
    })();
</script>
<!-- End Matomo Code -->