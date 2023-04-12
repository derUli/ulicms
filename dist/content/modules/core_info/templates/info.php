<?php
use App\Backend\UliCMSVersion;

$version = new UliCMSVersion();
$admin_logo = Settings::get('admin_logo');
if (! $admin_logo) {
    $admin_logo = 'gfx/logo.png';
}
?>
<p>
    <strong>Release <?php echo cms_version(); ?>
        "<?php echo $version->getCodeName(); ?>"</strong>
    <br /> <small><?php translate('build_date'); ?>:
        <?php echo $version->getBuildDate(); ?></small>
</p>
<p>
    <a href="http://www.ulicms.de" target="_blank">UliCMS</a> &copy; 2011 -
    <?php cms_release_year(); ?> by Ulrich Schmidt<br /> <a
        href="http://www.ckeditor.com" target="_blank">CKEditor</a> &copy;
    2003 - 2020 by CKSource<br /> 
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
           <?php translate('portal'); ?>
    </a>
    <a
        href="index.php?action=license"
        class="btn btn-info is-ajax"
        >
        <i class="fa fa-info-circle" aria-hidden="true"></i>
        <?php translate('license'); ?>
    </a>
    <a href="http://www.ulicms.de/kontakt.html" target="_blank"
       class="btn btn-info" role="button"><i class="fas fa-envelope"></i>
           <?php translate('feedback'); ?>
    </a>

    <a 
        href="index.php?action=changelog"
        class="btn btn-info is-ajax"
        >            
        <i class="fab fa-readme"></i>
        <?php translate('changelog'); ?>

    </a>
    <a href="<?php echo ModuleHelper::buildActionURL('legal_composer'); ?>" 
       class="btn btn-info is-ajax"
       data-url="">
        <i class="fas fa-file-contract"></i>
        <?php translate('legal'); ?>
    </a>
</div>

