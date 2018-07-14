<?php

namespace plugin\dutoland\fileHelper\bean;

use plugin\dutoland\fileHelper\exception\FileException;
use plugin\dutoland\fileHelper\util\FileUtils;

class FileEntry extends PathEntry {

	public function getExtension() {
		return FileUtils::getExtensionFromFileName($this->path);
	}

	public function isExisting(?bool $mustExistOption = null) {
		$isExisting = file_exists($this->path);
		if ($mustExistOption !== null) {
			if (!$mustExistOption && $isExisting)
				throw new FileException('File "' . $this->path . '" already exist');
			if ($mustExistOption && !$isExisting)
				throw new FileException('File "' . $this->path . '" doesn\'t exist');
		}
		return $isExisting;
	}

	public function getContent() {
		$this->isExisting(true);
		return file_get_contents($this->path);
	}

	public function create(string $content = '') {
		$this->isExisting(false);
		file_put_contents($this->path, $content);
	}

}