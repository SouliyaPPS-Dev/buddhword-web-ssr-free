<?php

function isVercel() {
    // Check common Vercel environment indicators
    if (isset($_ENV['VERCEL']) && $_ENV['VERCEL']) return true;
    if (getenv('VERCEL') === '1') return true;
    if (isset($_SERVER['VERCEL'])) return true;
    
    // Check for Vercel runtime (serverless functions)
    if (isset($_SERVER['HTTP_X_VERCEL_ID'])) return true;
    if (isset($_SERVER['HTTP_X_Vercel'])) return true;
    
    return false;
}

function getCacheDir() {
    // Vercel: use /tmp (writable) for serverless functions
    if (isVercel()) {
        $dir = sys_get_temp_dir() . '/buddhaword_cache';
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        return $dir;
    }
    
    // HuggingFace and local: use storage/cache
    $cacheDir = __DIR__ . '/../../storage/cache';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0777, true);
    }
    return $cacheDir;
}

function getCachePath($filename) {
    return getCacheDir() . '/' . $filename;
}

function readCache($filename, $maxAge = 86400) {
    $path = getCachePath($filename);
    if (file_exists($path) && (time() - filemtime($path) < $maxAge)) {
        return @file_get_contents($path);
    }
    return null;
}

function writeCache($filename, $data) {
    $path = getCachePath($filename);
    $dir = dirname($path);
    
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    
    $result = @file_put_contents($path, $data);
    return $result !== false;
}

function getCacheVersion($filename) {
    $path = getCachePath($filename);
    return file_exists($path) ? @filemtime($path) : 0;
}

function shouldRefreshCache($filename, $debounce = 30) {
    $path = getCachePath($filename);
    return !file_exists($path) || (time() - filemtime($path) >= $debounce);
}
