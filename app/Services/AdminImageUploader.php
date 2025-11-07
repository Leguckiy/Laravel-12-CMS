<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;

class AdminImageUploader
{
    private ImageManager $manager;

    public function __construct()
    {
        $driver = config('image.driver', 'gd');
        $driverInstance = match ($driver) {
            'imagick' => new ImagickDriver(),
            default => new GdDriver(),
        };

        $this->manager = new ImageManager($driverInstance);
    }

    public function uploadImage(
        string $imageName,
        string $directory,
        UploadedFile $file,
        ?int $width = null,
        ?int $height = null
    ): string {
        $format = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        if (!in_array($format, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            $format = 'jpg';
        }

        $normalisedFormat = match ($format) {
            'jpeg' => 'jpg',
            default => $format,
        };

        $baseName = Str::slug($imageName) ?: 'image';

        $computedWidth = $width ?? 800;
        $computedHeight = $height ?? 800;

        $background = config('media.background_color', '#ffffff');

        $image = $this->manager->read($file->getPathname())
            ->orient()
            ->contain($computedWidth, $computedHeight, $background);

        $encodeOptions = match ($normalisedFormat) {
            'jpg', 'webp' => [85],
            default => [],
        };

        $encoded = $image->encodeByExtension($normalisedFormat, ...$encodeOptions);

        $filename = $baseName . '.' . $normalisedFormat;

        $disk = Storage::disk(config('media.disk'));
        $disk->put(trim($directory, '/') . '/' . $filename, (string) $encoded);

        return $filename;
    }

    public function delete(?string $path): void
    {
        if (!$path) {
            return;
        }

        Storage::disk(config('media.disk'))->delete($path);
    }
}

