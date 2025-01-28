<?php

/**
 * Tiny URL shortener implementation
 */
class ShortenerService {
    protected $randLen = 8;
    protected $allocList = array();

    //some predefined stuff here
    const DATA_PATH = 'data/';

    public function __construct() {
        $this->loadAlloc();
    }

    /**
     * Advanced scandir analog wit some filters
     * 
     * @param string $directory Directory to scan
     * @param string $exp  Filter expression - like *.ini or *.dat
     * @param string $type Filter type - all or dir
     * @param bool $do_not_filter
     * 
     * @return array
     */
    function rcms_scandir($directory, $exp = '', $type = 'all', $do_not_filter = false) {
        $dir = $ndir = array();
        if (!empty($exp)) {
            $exp = '/^' . str_replace('*', '(.*)', str_replace('.', '\\.', $exp)) . '$/';
        }
        if (!empty($type) && $type !== 'all') {
            $func = 'is_' . $type;
        }
        if (is_dir($directory)) {
            $fh = opendir($directory);
            while (false !== ($filename = readdir($fh))) {
                if (substr($filename, 0, 1) != '.' || $do_not_filter) {
                    if ((empty($type) || $type == 'all' || $func($directory . '/' . $filename)) && (empty($exp) || preg_match($exp, $filename))) {
                        $dir[] = $filename;
                    }
                }
            }
            closedir($fh);
            natsort($dir);
        }
        return $dir;
    }

    protected function loadAlloc() {
        $all = $this->rcms_scandir(self::DATA_PATH);

        print_r($all);
    }
}

$shortener = new ShortenerService();
