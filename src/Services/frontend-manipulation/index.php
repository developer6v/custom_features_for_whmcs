<?php

$dirIterator = new RecursiveDirectoryIterator(
    __DIR__,
    FilesystemIterator::SKIP_DOTS
);

$iterator = new RecursiveIteratorIterator($dirIterator);

foreach ($iterator as $fileInfo) {
    /** @var SplFileInfo $fileInfo */
    if (!$fileInfo->isFile()) {
        continue;
    }

    if ($fileInfo->getExtension() !== 'php') {
        continue; 
    }

    if ($fileInfo->getFilename() === 'index.php' && $fileInfo->getPath() === __DIR__) {
        continue;
    }

    $relativePath = str_replace(__DIR__, '', $fileInfo->getPathname());

    require_once __DIR__ . $relativePath;
}
