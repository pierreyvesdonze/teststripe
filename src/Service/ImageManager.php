<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Symfony\Component\Filesystem\Filesystem;

class ImageManager
{
    private $imageProductDirectory;
    private $imageBannerDirectory;
    private $imagine;

    private const MAX_WIDTH  = 1024;
    private const MAX_HEIGHT = 768;

    public function __construct(
        $imageProductDirectory,
        $imageBannerDirectory
    ) {
        $this->imageProductDirectory = $imageProductDirectory;
        $this->imageBannerDirectory  = $imageBannerDirectory;
        $this->imagine               = new Imagine();
    }

    public function upload(UploadedFile $file, $type)
    {
        $imageDirectory = null;

        if ('product' === $type) {
            
            $fileName = 'assets/images/products/' . uniqid() . '.' . $file->getClientOriginalExtension();
            $imageDirectory = $this->getImageProductDirectory();
        } elseif ('banner' === $type) {
            $fileName = 'assets/images/banners/' . uniqid() . '.' . $file->getClientOriginalExtension();
            $imageDirectory = $this->getImageBannerDirectory();
        }

        try {
            $file->move($imageDirectory, $fileName);
        } catch (FileException $e) {
            echo($e);
        }

        return $fileName;
    }

    public function resize(string $filename): void
    {
        list($iwidth, $iheight) = getimagesize($filename);
             $ratio             = $iwidth / $iheight;
             $width             = self::MAX_WIDTH;
             $height            = self::MAX_HEIGHT;
        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }

        $photo = $this->imagine->open($filename);
        $photo->resize(new Box($width, $height))->save($filename);
    }

    public function getImageProductDirectory()
    {
        return $this->imageProductDirectory;
    }

    public function getImageBannerDirectory()
    {
        return $this->imageBannerDirectory;
    }

    public function deleteImage($fileName): void
    {
        if ($fileName !==null) {
            $fileSystem = new Filesystem();
            $fileSystem->remove($fileName);
        }
    }
}
