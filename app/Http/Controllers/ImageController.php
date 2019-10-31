<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Filesystem\Filesystem;

use League\Glide\ServerFactory;
use League\Glide\Responses\LaravelResponseFactory;

class ImageController extends Controller
{
    public function show(Filesystem $filesystem, $path)
    {
        $server = ServerFactory::create([
            'response' => new LaravelResponseFactory(),
            'source' => $filesystem->getDriver(),
            'cache' => $filesystem->getDriver(),
            'cache_path_prefix' => '.cache',
            'base_url' => 'storage',
        ]);

        return $server->getImageResponse($path, request()->all());
    }
}
