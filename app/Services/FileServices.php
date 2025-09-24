<?php

namespace App\Services;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

class FileServices
{
    public function upload($file, $path = '/uploads', $fileName = false, $hasThumb = false, $thumbWidth = 100, $thumbHeight = 100): array
    {
        try {
            $name       = !$fileName ? $file->getClientOriginalName() : $fileName;
            $extension  = $file->getClientOriginalExtension();
            $size       = $file->getSize();
            $mimeType   = $file->getMimeType();
            $type       = explode('/', $mimeType)[0];

            /**
             * Format the file name
             */
            $name = Str::slug(pathinfo($name, PATHINFO_FILENAME));
            $name = !$fileName ? time() . '-' . $name . '.' . $extension : $name . '.' . $extension;

            /**
             * Move the uploaded file
             */
            if (!Storage::disk(config('filesystems.default'))->putFileAs($path, $file, $name)) {
                return [
                    'ok'      => false,
                    'message' => __('File upload failed.'),
                    'data'    => []
                ];
            }

            /**
             * Create thumbnail if it's an image
             */
            if ($hasThumb && str_starts_with($mimeType, 'image/')) {
                try {
                    $manager = new ImageManager(new Driver());
                    $image = $manager->read(Storage::disk(config('filesystems.default'))->get("$path/$name"));
                    $image->cover($thumbWidth, $thumbHeight);

                    /**
                     * Save the thumbnail
                     */
                    Storage::disk(config('filesystems.default'))->put("$path/thumbs/$name", $image->toPng());
                } catch (\Throwable $e) {
                    // Log thumbnail creation error but continue with the upload
                    \Log::error('Thumbnail creation failed: ' . $e->getMessage());
                }
            }

            /**
             * Success response
             */
            return [
                'ok'        => true,
                'message'   => __('File uploaded successfully.'),
                'data'      => [
                    'ext'           => $extension,
                    'file_name'     => $name,
                    'path'          => $path,
                    'size'          => $size,
                    'mime_type'     => $mimeType,
                    'type'          => $type,
                    'full_link'     => url(Storage::disk(config('filesystems.default'))->url("$path/$name")),
                ]
            ];
        } catch (\Throwable $e) {
            \Log::error('File upload failed: ' . $e->getMessage());
            return [
                'ok'      => false,
                'message' => __('File upload failed.'),
                'data'    => []
            ];
        }
    }
}
