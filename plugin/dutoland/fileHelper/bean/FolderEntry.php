<?php

namespace plugin\dutoland\fileHelper\bean;

use plugin\dutoland\fileHelper\util\FileUtils;

class FolderEntry extends PathEntry {

	public function createNewUniqueFile(string $content = '', string $extension = '') {
		do {
			$fileEntry = new FileEntry($this->path . DIRECTORY_SEPARATOR . uniqid('', true) . ($extension !== '' ? '.' . $extension : ''));
		} while ($fileEntry->isExisting());
		$fileEntry->create($content);
		return $fileEntry;
	}

	/**
	 * @param bool $isRecursive
	 * @return FileEntry[]
	 */
	public function getFileEntries(bool $isRecursive = false) {
		return FileUtils::getFileEntryInFolder($this->path, $isRecursive);
	}

}