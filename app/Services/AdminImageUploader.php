<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;

class AdminImageUploader
{
    private const SUPPORTED_FORMATS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    private const DEFAULT_DIMENSION = 800;

    private ImageManager $manager;

    private string $diskName;

    private string $backgroundColor;

    public function __construct()
    {
        $driver = config('media.driver');
        $driverInstance = $driver === 'imagick' ? new ImagickDriver : new GdDriver;

        $this->manager = new ImageManager($driverInstance);
        $this->diskName = config('media.disk');
        $this->backgroundColor = config('media.background_color', '#ffffff');
    }

    public function uploadImage(
        string $imageName,
        string $directory,
        UploadedFile $file,
        ?int $width = null,
        ?int $height = null,
        bool $resize = true
    ): string {
        $format = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        if (! in_array($format, self::SUPPORTED_FORMATS, true)) {
            $format = 'jpg';
        }
        $normalisedFormat = ['jpeg' => 'jpg'][$format] ?? $format;
        $baseName = trim(preg_replace('/[^A-Za-z0-9_-]+/', '-', $imageName), '-');
        if ($baseName === '') {
            $baseName = 'image';
        }

        $image = $this->manager->read($file->getPathname())->orient();

        if ($resize) {
            $computedWidth = $width ?? self::DEFAULT_DIMENSION;
            $computedHeight = $height ?? self::DEFAULT_DIMENSION;

            $image = $image->contain($computedWidth, $computedHeight, $this->backgroundColor);
        }

        $encodeOptions = in_array($normalisedFormat, ['jpg', 'webp'], true) ? [85] : [];

        $encoded = $image->encodeByExtension($normalisedFormat, ...$encodeOptions);

        $filename = $baseName.'.'.$normalisedFormat;

        $disk = Storage::disk($this->diskName);
        $disk->put(trim($directory, '/').'/'.$filename, (string) $encoded);

        return $filename;
    }

    public function delete(?string $path): void
    {
        if (! $path) {
            return;
        }

        Storage::disk($this->diskName)->delete($path);
    }
}
