<?php
$version = new UliCMSVersion();
$admin_logo = Settings::get("admin_logo");
if (!$admin_logo) {
    $admin_logo = "gfx/logo.png";
}
?>
<p>
    <strong>Release <?php echo cms_version(); ?>
        "<?php echo $version->getCodeName(); ?>"</strong>
    <?php if ($version->getBuildTimestamp() > 0) { ?>
        <br /> <small><?php translate("build_date"); ?>:
            <?php echo $version->getBuildDate() ?></small>
        <?php
    }
    ?>
</p>
<p>
    <a href="http://www.ulicms.de" target="_blank">UliCMS</a> &copy; 2011 -
    <?php cms_release_year(); ?> by Ulrich Schmidt<br /> <a
        href="http://www.ckeditor.com" target="_blank">CKEditor</a> &copy;
    2003 - 2020 by CKSource<br />
    <?php
    // FIXME: Replace with Responsive File Manager copyright
    ?>
    Copyright 2005, 2014 jQuery Foundation, Inc. and other contributors
    <br />
    <a href="http://codemirror.net/" target="_blank">CodeMirror</a>
    &copy;
    2017 by Marijn Haverbeke &lt;marijnh@gmail.com&gt; and others <br />
    <a
        href="http://www.raymondhill.net/finediff/viewdiff-ex.php"
        target="_blank">PHP Fine Diff</a> Copyright 2011 (c) Raymond Hill
    <br />
    <a href="http://mobiledetect.net/" target="_blank">Mobile_Detect</a>
    Copyright &copy; 2011 - 2015 Serban Ghita, Nick Ilyin and contributors.
    <br /> <a href="https://github.com/chrisbliss18/php-ico"
              target="_blank">PHP ICO - The PHP ICO Generator</a> - Copyright 2011 -
    2013 Chris Jean <br /> <a href="https://plugins.jquery.com/url/"
                              target="_blank">jQuery URL Plugin</a> Copyright (C) 2011 - 2012 <a
                              href="http://www.websanova.com" target="_blank">Websanova</a> <br />
    <a
        href="http://www.phpgangsta.de/2-faktor-authentifizierung-mit-dem-google-authenticator"
        target="_blank">GoogleAuthenticator</a> Copyright (c) 2012, Michael
    Kliewe<br /> <a href="http://www.phpfastcache.com/" target="_blank">Phpfastcache</a>
    Copyright (c) 2016
</p>
<div>
    Icon made by <a href="http://www.freepik.com" title="Freepik">Freepik</a>
    from <a href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a>
    is licensed under <a href="http://creativecommons.org/licenses/by/3.0/"
                         title="Creative Commons BY 3.0">CC BY 3.0</a>
</div>
<div>
    Icon made by <a href="http://www.freepik.com" title="Freepik">Freepik</a>
    from <a href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a>
    is licensed under <a href="http://creativecommons.org/licenses/by/3.0/"
                         title="Creative Commons BY 3.0">CC BY 3.0</a>
</div>
<div>
    Icon made by <a href="http://www.freepik.com" title="Freepik">Freepik</a>
    from <a href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a>
    is licensed under <a href="http://creativecommons.org/licenses/by/3.0/"
                         title="Creative Commons BY 3.0">CC BY 3.0</a>
</div>
<div>
    Icon made by <a href="http://www.google.com" title="Google">Google</a>
    from <a href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a>
    is licensed under <a href="http://creativecommons.org/licenses/by/3.0/"
                         title="Creative Commons BY 3.0">CC BY 3.0</a>
</div>
<div>
    Icon made by <a href="http://www.elegantthemes.com"
                    title="Elegant Themes">Elegant Themes</a> from <a
                    href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a>
    is licensed under <a href="http://creativecommons.org/licenses/by/3.0/"
                         title="Creative Commons BY 3.0">CC BY 3.0</a>
</div>
<div>
    Icons made by <a
        href="http://www.flaticon.com/authors/butterflytronics"
        title="Butterflytronics">Butterflytronics</a> from <a
        href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a>
    is licensed by <a href="http://creativecommons.org/licenses/by/3.0/"
                      title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a>
</div>
<div>
    Icons made by <a href="http://www.freepik.com" title="Freepik">Freepik</a>
    from <a href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a>
    is licensed by <a href="http://creativecommons.org/licenses/by/3.0/"
                      title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a>
</div>
<div class="btn-group voffset2">
    <a href="http://www.ulicms.de" target="_blank" class="btn btn-info"
       role="button"><i class="fa fa-globe" aria-hidden="true"></i> UliCMS
           <?php translate("portal"); ?>
    </a>
    <a
        href="index.php?action=license"
        class="btn btn-info is-ajax"
        >
        <i class="fa fa-info-circle" aria-hidden="true"></i>
        <?php translate("license") ?>
    </a>
    <a href="http://www.ulicms.de/kontakt.html" target="_blank"
       class="btn btn-info" role="button"><i class="fas fa-envelope"></i>
           <?php translate("feedback"); ?>
    </a>

    <a 
        href="index.php?action=changelog"
        class="btn btn-info is-ajax"
        >            
        <i class="fab fa-readme"></i>
        <?php translate("changelog"); ?>

    </a>
    <a href="<?php echo ModuleHelper::buildActionURL("legal_composer"); ?>" 
       class="btn btn-info is-ajax"
       data-url="">
        <i class="fas fa-file-contract"></i>
        <?php translate("legal"); ?>
    </a>
</div>

