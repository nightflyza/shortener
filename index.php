<?php

/**
 * Tiny URL shortener implementation
 */
class ShortenerService {
    /**
     * Contains default unique URL identifier lenght
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

    /**
     * Generate header or JS redirect?
     *
     * @var bool
     */
    protected $useHeader = true;

    //some predefined stuff here
    const DATA_PATH = 'data/';
    const ROUTE_SAVE = 'shorten';
    const ROUTE_REDIRECT = 'go';

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

    /**
     * Redirects user to some specified URL
     * 
     * @param string $url URL to perform redirect
     * @param bool $header Use header redirect instead of JS document.location
     * 
     * @return void
     */
    protected function nav($url, $header = false) {
        if (!empty($url)) {
            if ($header) {
                @header('Location: ' . $url);
            } else {
                print('<script language="javascript">document.location.href="' . $url . '";</script>');
            }
        }
    }
    /**
     * Saves a given URL to a file and returns a unique identifier for the URL.
     *
     * @param string $url The URL to be saved.
     *
     * @return string|void
     */
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

        if (empty($result)) {
            header('HTTP/1.1 400 Saving failed', true, 400);
        }
        return ($result);
    }

    /**
     * Undocumented function
     *
     * @param string $urlId
     * 
     * @return void
     */
    public function navigateUrl($urlId = '') {
        $urlId=preg_replace('/\0/s', '', $urlId);
        $urlId=preg_replace('#[^a-zA-Z0-9]#u', '', $urlId);
        if (!empty($urlId)) {
            if (isset($this->allocList[$urlId])) {
                $urlBody=file_get_contents(self::DATA_PATH.$urlId);
                if (!empty($urlBody)) {
                    $this->nav($urlBody,$this->useHeader);
                } else {
                    header('HTTP/1.1 400 Request failed', true, 400);
                    die('Error: empty URL body');
                }
            } else {
                header('HTTP/1.1 400 Request failed', true, 400);
                die('Error: URL not found');
            }
        }
    }
}

$shortener = new ShortenerService();

//saving new url
if (isset($_GET[$shortener::ROUTE_SAVE])) {
    if (!empty($_GET[$shortener::ROUTE_SAVE])) {
        print($shortener->saveUrl($_GET[$shortener::ROUTE_SAVE]));
    }
}

//redirecting to some saved URL
if (isset($_GET[$shortener::ROUTE_REDIRECT])) {
    if (!empty($_GET[$shortener::ROUTE_REDIRECT])) {
        $shortener->navigateUrl($_GET[$shortener::ROUTE_REDIRECT]);
    }
}