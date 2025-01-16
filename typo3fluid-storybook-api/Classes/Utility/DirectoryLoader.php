<?php

declare(strict_types=1);

namespace PixelCoda\TYPO3StorybookApi\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class DirectoryLoader
{
    /**
     * Get all subdirectories of a given base directory.
     *
     * @param string $basePath
     * @return array<string> List of subdirectory paths.
     */
    public static function getAllSubdirectories(string $basePath): array
    {
        $absoluteBasePath = GeneralUtility::getFileAbsFileName($basePath);
        if (! is_dir($absoluteBasePath)) {
            return [];
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($absoluteBasePath, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $subdirectories = [];
        foreach ($iterator as $fileInfo) {
            if ($fileInfo instanceof \SplFileInfo && $fileInfo->isDir()) {
                $subdirectories[] = $fileInfo->getPathname();
            }
        }

        return $subdirectories;
    }
}
