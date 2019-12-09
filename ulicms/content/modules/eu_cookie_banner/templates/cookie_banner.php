<!-- Modal -->
<div
    id="cookies-eu-banner"
    style="display: none"
    data-url="<?php
    echo ModuleHelper::buildMethodCallUrl(EuCookieBannerController::class,
            "getHtmlCode");
    ?>">
    <div class="modal fade in" role="dialog" style="display: block">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-body">
                    <?php echo Settings::getLang("eu_cookie_banner/help_text", getCurrentLanguage()); ?>
                    <!--<a href="./read-more.html" id="cookies-eu-more">Read more</a> -->

                </div>
                <div class="modal-footer">
                    <button id="cookies-eu-reject">
                        <?php echo Settings::getLang("eu_cookie_banner/reject",
						getCurrentLanguage()); ?>
                    </button>
                    <button id="cookies-eu-accept">
                        <?php echo Settings::getLang("eu_cookie_banner/accept",
						getCurrentLanguage()); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>