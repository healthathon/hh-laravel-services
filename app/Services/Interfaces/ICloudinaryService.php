<?php

namespace App\Services\Interfaces;


interface ICloudinaryService
{

    function upload($filePath, $publicId, array $options = []);

    function getUrl();
}