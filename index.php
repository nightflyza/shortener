<?php

/**
 * Tiny URL shortener implementation
 */
class ShortenerService {
    /**
     * Contains default unique identifier lenght
     *
     * @var int
     */
    protected $randLen = 12; // 4.74*10^18 or 1:4738381338321617 collision probability

    /**
     * Contains allocated shorten names as id=>idx
     *
     * @var array
     */
    protected $allocList = array();

    //some predefined stuff here
    const DATA_PATH = 'data/';
    const ROUTE_SAVE='shorten';
    const ROUTE_REDIRECT='go';

    public function __construct() {
        $this->loadAlloc();
    }

    /**
     * Returns random alpha-numeric string of some lenght
     * 
     * @return string
     */
    function getRandString() {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $string = '';
        for ($p = 0; $p < $this->randLen; $p++) {
            $string .= $characters[mt_rand(0, (strlen($characters) - 1))];
        }

        return ($string);
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
        $dir = array();
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
        return ($dir);
    }

    /**
     * Preloads allocated shorten names
     *
     * @return void
     */
    protected function loadAlloc() {
        $all = $this->rcms_scandir(self::DATA_PATH);
        if (!empty($all)) {
            $this->allocList = array_flip($all);
        }
    }

    /**
     * Returns next free url ID
     *
     * @return void
     */
    public function getFreeId() {
        $result = '';
        $result = $this->getRandString();
        while (isset($this->allocList[$result])) {
            $result = $this->getRandString();
        }
        return ($result);
    }

    public function saveUrl($url) {
        $result = '';
        if (!empty($url)) {
            if (mb_strlen($url, 'UTF-8') < 255) {
                $newId = $this->getFreeId();
                // ORLY? Oo 
                if (!isset($this->allocList[$newId])) {
                    $url = trim($url);
                    $url = strip_tags($url);
                    if (is_writable(self::DATA_PATH)) {
                        file_put_contents(self::DATA_PATH . $newId, $url);
                        $result = $newId;
                    }
                }
            }
        }
        return ($result);
    }
}

$shortener = new ShortenerService();

if (isset($_GET[$shortener::ROUTE_SAVE])) {
    if (!empty($_GET[$shortener::ROUTE_SAVE])) {
        print($shortener->saveUrl($_GET[$shortener::ROUTE_SAVE]));
    }
}


if (isset($_GET[$shortener::ROUTE_REDIRECT])) {
    if (!empty($_GET[$shortener::ROUTE_REDIRECT])) {
        //TODO
    }
}
