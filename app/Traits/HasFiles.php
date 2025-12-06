<?php


namespace App\Traits;

use App\Models\Fileable;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HasFiles
{



    public function files(): MorphMany
    {
        return $this->morphMany(Fileable::class, 'fileable');
    }
    public function storeFile(UploadedFile $file, string $folder = 'files'): string
    {
        $path = $file->store($folder, 'public');
        $this->files()->create([
            'path' => $path,
        ]);
        return $path;
    }

    public function deleteFile(string $path): void
    {


        $is_deleted = Storage::delete($path);
        if (!$is_deleted) {
            throw new \Exception('Failed to delete file');
        }
        $this->files()->where('path', $path)->delete();
    }

    public function deleteById(int $id): void
    {

        $file = $this->files()->find($id);
        if (!$file) {
            throw new \Exception('File not found');
        }
        $is_deleted = Storage::delete($file->path);
        if (!$is_deleted) {
            throw new \Exception('Failed to delete file');
        }
        $this->deleteFile($file->path);
    }
}
