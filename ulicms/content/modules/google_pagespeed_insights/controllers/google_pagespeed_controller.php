<?php

class PageSpeedController extends Controller
{

    public function adminMenuEntriesFilter($entries)
    {
        $url = "https://developers.google.com/speed/pagespeed/insights/?hl=" . urlencode(getSystemLanguage()) . "&url=" . urlencode(get_protocol_and_domain());
        $entries[count($entries) - 1] = new MenuEntry('<i class="fas fa-chart-line"></i> PageSpeed Insights', $url, "pagespeed", null, array(), true);
        $entries[] = new MenuEntry('<i class="fa fa-sign-out-alt"></i> ' . get_translation("logout"), "?action=destroy", "destroy");
        return $entries;
    }
}