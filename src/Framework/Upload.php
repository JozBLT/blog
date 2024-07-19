<?php

namespace Framework;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Psr\Http\Message\UploadedFileInterface;

class Upload
{

    protected string $path;

    protected array $formats = [];

    public function __construct(?string $path = null)
    {
        if ($path) {
            $this->path = $path;
        }
    }

    public function upload(UploadedFileInterface $file, ?string $oldFile = null, ?string $filename = null): ?string
    {
        if ($file->getError() === UPLOAD_ERR_OK) {
            $this->delete($oldFile);
            $targetPath = $this->addCopySuffix(
                $this->path .
                DIRECTORY_SEPARATOR .
                ($filename ?: $file->getClientFilename())
            );
            $dirname = pathinfo($targetPath, PATHINFO_DIRNAME);

            if (!file_exists($dirname)) {
                mkdir($dirname, 777, true);
            }

            $file->moveTo($targetPath);
            $this->generateFormats($targetPath);

            return pathinfo($targetPath)['basename'];
        }

        return null;
    }

    private function addCopySuffix(string $targetPath): string
    {
        if (file_exists($targetPath)) {
            return $this->addCopySuffix($this->getPathWithSuffix($targetPath, 'copy'));
        }

        return $targetPath;
    }

    public function delete(?string $oldFile): void
    {
        if ($oldFile) {
            $oldFile = $this->path . DIRECTORY_SEPARATOR . $oldFile;

            if (file_exists($oldFile)) {
                unlink($oldFile);
            }

            foreach ($this->formats as $format => $_) {
                $oldFileWithFormat = $this->getPathWithSuffix($oldFile, $format);

                if (file_exists($oldFileWithFormat)) {
                    unlink($oldFileWithFormat);
                }
            }
        }
    }

    private function getPathWithSuffix(string $path, string $suffix): string
    {
        $info = pathinfo($path);

        return $info['dirname'] . DIRECTORY_SEPARATOR .
            $info['filename'] . '_' . $suffix . '.' . $info['extension'];
    }

    private function generateFormats(string $targetPath): void
    {
        foreach ($this->formats as $format => $size) {
            $manager = new ImageManager(new Driver());
            $destination = $this->getPathWithSuffix($targetPath, $format);
            [$width, $height] = $size; // $width = $size[0]; $height = $size[1]
            $manager->read($targetPath)->resize($width, $height)->save($destination);
        }
    }
}
