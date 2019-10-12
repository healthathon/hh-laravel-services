<?php

namespace App\Services;


use App\Services\Interfaces\ICloudinaryService;
use JD\Cloudder\Facades\Cloudder;

class CloudinaryService implements ICloudinaryService
{

    function upload($filePath, $publicId, array $options = [])
    {
        Cloudder::upload($filePath, $publicId, $options);
    }

    function getUrl()
    {
        $uploadResult = Cloudder::getResult();
        return $uploadResult["url"];
    }
}