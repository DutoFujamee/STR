<?php

namespace plugin\dutoland\fileHelper\util;

use plugin\dutoland\fileHelper\bean\FileEntry;
use plugin\dutoland\fileHelper\exception\FileException;

class FileUtils {

	public static function normalizePath(string $path) {
		$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);

		$parts = array();
		foreach (explode(DIRECTORY_SEPARATOR, $path) as $part) {
			if ($part !== '')
				$parts[] = $part;
		}

		$absolutes = array();
		foreach ($parts as $part) {
			if ($part === '.')
				continue;

			if ($part === '..') {
				if (count($absolutes) === 0
						|| (count(array_unique($absolutes)) === 1 && $absolutes[0] === '..')) {
					$absolutes[] = $part;
				} else {
					array_pop($absolutes);
				}
			} else {
				$absolutes[] = $part;
			}
		}
		return implode(DIRECTORY_SEPARATOR, $absolutes);
	}

	public static function getExtensionFromFileName(string $fileName) {
		if ($fileName === '')
			return '';
		$result = strrchr(substr($fileName, 1), '.');
		return $result === false ? '' : substr($result, 1);
	}

	/**
	 * @param FileEntry[] $fileEntries
	 * @param string[] $extensions
	 * @return FileEntry[]
	 */
	public static function filterExtensions(array $fileEntries, array $extensions) {
		$extensionsIndexed = array_flip($extensions);

		$newFileEntries = array();
		foreach ($fileEntries as $fileEntry) {
			if (array_key_exists($fileEntry->getExtension(), $extensionsIndexed))
				$newFileEntries[] = $fileEntry;
		}

		return $newFileEntries;
	}

	/**
	 * @param string $folderPath
	 * @param bool $isRecursive
	 * @return FileEntry[]
	 * @throws FileException
	 */
	public static function getFileEntryInFolder(string $folderPath, bool $isRecursive = false) {
		if (!is_dir($folderPath))
			throw new FileException('Folder "' . $folderPath . '" doesn\'t exists');

		$fileEntries = array();
		foreach (array_diff(scandir($folderPath), array('..', '.')) as $path) {
			$filePath = $folderPath . DIRECTORY_SEPARATOR . $path;
			if (is_dir($filePath) && $isRecursive)
				$fileEntries = array_merge($fileEntries, self::getFileEntryInFolder($filePath, $isRecursive));
			else
				$fileEntries[] = new FileEntry($filePath);
		}

		return $fileEntries;
	}
}












