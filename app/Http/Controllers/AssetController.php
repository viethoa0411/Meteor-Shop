<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class AssetController extends Controller
{
    /**
     * Serve CSS file from resources/css
     */
    public function css($file)
    {
        $path = resource_path("css/{$file}.css");
        
        if (!File::exists($path)) {
            abort(404);
        }

        $content = File::get($path);
        
        return Response::make($content, 200)
            ->header('Content-Type', 'text/css')
            ->header('Cache-Control', 'public, max-age=31536000');
    }

    /**
     * Serve JS file from resources/js
     */
    public function js($file)
    {
        $path = resource_path("js/{$file}.js");
        
        if (!File::exists($path)) {
            abort(404);
        }

        $content = File::get($path);
        
        return Response::make($content, 200)
            ->header('Content-Type', 'application/javascript')
            ->header('Cache-Control', 'public, max-age=31536000');
    }
}

