<?php

declare(strict_types=1);

use App\Helpers\ImageScaleHelper;
use App\Utils\CacheUtil;
use App\Utils\File;

class LogoController extends Controller
{
    public function _buildFileName(
        string $filename,
        string $originalName
    ): string {
        $extension = File::getExtension($originalName);
        $hash = md5_file($filename);
        return $hash . '.' . $extension;
    }

    public function _buildFilePath(
        string $filename,
        string $originalName
    ): string {
        return ULICMS_ROOT . '/content/images/' .
                $this->_buildFileName($filename, $originalName);
    }

    public function upload(): void
    {
        // Logo Upload
        if (! empty($_FILES['logo_upload_file']['name'])) {
            $logo_upload = $_FILES['logo_upload_file'];
            $type = $logo_upload['type'];

            if (str_starts_with($type, 'image/')) {
                $originalName = $logo_upload['name'];
                $newPath = $this->_buildFilePath(
                    $logo_upload['tmp_name'],
                    $originalName
                );

                do_event('before_upload_logo');
                move_uploaded_file($logo_upload['tmp_name'], $newPath);

                ImageScaleHelper::scaleDown($newPath);

                Settings::set('logo_image', basename($newPath));
                Settings::set('logo_disabled', 'no');

                CacheUtil::clearPageCache();
                do_event('after_upload_logo_successful');
            }

            do_event('after_upload_logo');
        }


        Response::redirect(ModuleHelper::buildActionURL('logo'));
    }

    public function _deleteLogo(): bool
    {
        $logoImage = Settings::get('logo_image');
        $path = ULICMS_ROOT . "/content/images/{$logoImage}";

        if (empty($logoImage) || ! is_file($path)) {
            return false;
        }

        @unlink($path);

        Settings::set('logo_image', '');
        Settings::set('logo_disabled', 'yes');
        return true;
    }

    public function deleteLogo(): void
    {
        $success = $this->_deleteLogo();
        if ($succes) {
            CacheUtil::clearPageCache();
        }
        Response::sendHttpStatusCodeResultIfAjax(
            $success ?
                    HttpStatusCode::OK :
                    HttpStatusCode::INTERNAL_SERVER_ERROR,
            ModuleHelper::buildActionURL('logo')
        );
    }

    public function _hasLogo(): bool
    {
        return ! empty(Settings::get('logo_image')) &&
                Settings::get('logo_disabled') !== 'yes';
    }
}
