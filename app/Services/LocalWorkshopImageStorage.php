<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class LocalWorkshopImageStorage implements WorkshopImageStorage
{
    /**
     * @return array{path: string, url: string}
     */
    public function store(UploadedFile $image): array
    {
        $path = $image->store('workshops', 'public');

        if (! is_string($path) || $path === '') {
            throw new RuntimeException('Gambar workshop gagal disimpan ke disk lokal.');
        }

        return [
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
        ];
    }

    public function delete(string $path): void
    {
        $disk = Storage::disk('public');

        if ($disk->exists($path) && ! $disk->delete($path)) {
            throw new RuntimeException('Gambar workshop lama gagal dihapus dari disk lokal.');
        }
    }
}
