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
        Storage::delete($path);
        $this->files()->where('path', $path)->delete();
    }

    public function deleteById(int $id): void
    {

        $file = $this->files()->find($id);
        Storage::delete($file->path);
        $this->deleteFile($file->path);
    }
}
