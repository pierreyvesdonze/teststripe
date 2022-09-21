<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;

class ImageManager
{
    private $imageDirectory;

    public function __construct(
        $imageDirectory
    ) {
        $this->imageDirectory = $imageDirectory;
    }

    public function upload(UploadedFile $file)
    {
        // Répertoire de destination des images
        $imageDirectory = null;

        $fileName = 'assets/images/products/' . uniqid() . '.' . $file->getClientOriginalExtension();
        $imageDirectory = $this->getImageDirectory();

        try {
            $file->move($imageDirectory, $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    public function getImageDirectory()
    {
        return $this->imageDirectory;
    }

    public function deleteImage(string $fileName): void
    {
        $fileSystem = new Filesystem();
        $fileSystem->remove($fileName);
    }
}
