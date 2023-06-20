<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;


trait ImageManager
{
    public function uploads(UploadedFile $file, string $path): bool|string
    {
            $fileName   = time() . '_' . $file->getClientOriginalName();
            $publicPath = env('APP_URL') . '/storage/';
//            Storage::disk('public')->put($path . $fileName, File::get($file));
//            $file_name  = $file->getClientOriginalName();
//            $file_type  = $file->getClientOriginalExtension();
//            $filePath   = $path . $fileName;
//            $imagePath = $image->store('team_images', 'public');
//            dd($file->storeAs($path, $fileName));
            $imagePath = $file->storeAs($path, $fileName, 'public');
            return $publicPath . $imagePath;
//            return $file = [
//                'fileName' => $file_name,
//                'fileType' => $file_type,
//                'filePath' => $filePath,
//                'fileSize' => $this->fileSize($file)
//            ];
    }
}
