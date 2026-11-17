<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

interface WorkshopImageStorage
{
    /**
     * @return array{path: string, url: string}
     */
    public function store(UploadedFile $image): array;

    public function delete(string $path): void;
}
