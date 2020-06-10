<?php
    
/*
	Create documentation from code commentaries

	Usage: php -f update_docs.php
*/

class ParseDocumentation {

	const DEFAULT_FILE = './Default.md';
	const DOC_FILE = '../README.md';
	const SCAN_DIR = '../';
	const IMAGES_DIR = '/script/images';

	static function run() {
		$defaultContent = file_get_contents(self::DEFAULT_FILE);
		file_put_contents(self::DOC_FILE, $defaultContent . self::parse());
	}

	static function parse() {
		$result  = "\n\n## Application Structure ##\n";
		$result .= self::parseDirs();
		$result .= "\n\n## Code Description ##\n";
		$result .= self::parseComments();
		return $result;
	}

	static function parseDirs($path = self::SCAN_DIR, $level = 0) {
		$result = '';
		$dirs = self::dirs($path, 0, 'swift');
		foreach ($dirs as $dir) {
			$spaces = str_repeat('&nbsp;', $level + 1);
			if ($dir['hasFiles']) {
				$result .= '<img src="' . self::IMAGES_DIR . '/folder.png" width=64>' . $spaces . ' ### ' . $dir['name'] . " ###\n";
			}
			foreach ($dir['subdirs'] as $subdir) {
				$result .= self::parseDirs($subdir['path'], $level + 1);
			}
		}
		return $result;
	}

	static function parseComments($path = self::SCAN_DIR, $level = 0) {

	}

	static function dirs($dir, $level = 0, $fileExtension = null) {
	    $result = [];
	    foreach (scandir($dir) as $file) {
	        if ($file == '.' || $file == '..') continue;
	        $path = realpath($dir . DIRECTORY_SEPARATOR . $file);
	        if (is_dir($path)) {
	        	$subdirs = self::dirs($path, $level + 1, $fileExtension);
	        	$files = self::files($path, $fileExtension);
	        	$hasFiles = count($files) > 0 || array_reduce($subdirs, function($result, $item) {
	        		return $result || $item['hasFiles'];
	        	}, false);
	            $result []= [
	                    'level' => $level,
	                    'name' => $file,
	                    'path' => $path,
	                    'subdirs' => $subdirs,
	                    'files' => $files,
	                    'hasFiles' => $hasFiles
	            ];
	        }
	    }
	    return $result;
	}

	static function files($dir, $extension = null) {
		$result = [];		
	    foreach (scandir($dir) as $file) {
	        if ($file == '.' || $file == '..') continue;
	        $path = realpath($dir . DIRECTORY_SEPARATOR . $file);
	        if (is_file($path)) {
	        	$parts = pathinfo($path);
	        	if ($extension != null && strtolower($parts['extension']) != strtolower($extension)) continue;
	            $result []= [
	                    'name' => $parts['filename'],
	                    'extension' => $parts['extension'],
	                    'basename' => $parts['basename']
	            ];
	        }
	    }
	    return $result;
	}
}

ParseDocumentation::run();