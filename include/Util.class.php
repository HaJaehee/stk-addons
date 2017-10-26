<?php
/**
 * copyright 2011      Stephen Just <stephenjust@users.sf.net>
 *           2014-2015 Daniel Butum <danibutum at gmail dot com>
 * This file is part of stk-addons.
 *
 * stk-addons is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * stk-addons is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with stk-addons. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Inspired from https://github.com/brandonwamboldt/utilphp/
 */
class Util
{
    /**
     * A constant representing the number of seconds in a minute
     * @var int
     */
    const SECONDS_IN_A_MINUTE = 60;

    /**
     * A constant representing the number of seconds in an hour
     * @var int
     */
    const SECONDS_IN_A_HOUR = 3600;

    /**
     * Alias of SECONDS_IN_A_HOUR
     * @var int
     */
    const SECONDS_IN_AN_HOUR = 3600;

    /**
     * A constant representing the number of seconds in a day
     * @var int
     */
    const SECONDS_IN_A_DAY = 86400;

    /**
     * A constant representing the number of seconds in a week
     * @var int
     */
    const SECONDS_IN_A_WEEK = 604800;

    /**
     * A constant representing the number of seconds in a month (30 days),
     * @var int
     */
    const SECONDS_IN_A_MONTH = 2592000;

    /**
     * A constant representing the number of seconds in a year (365 days),
     * @var int
     */
    const SECONDS_IN_A_YEAR = 31536000;

    /**
     * Length of our salt
     * @var int
     */
    const SALT_LENGTH = 32;

    /**
     * The length in characters of a sha256 hash
     * @var int
     */
    const SHA256_LENGTH = 64;

    /**
     * The length in characters of a md5 hash
     * @var int
     */
    const MD5_LENGTH = 32;

    /**
     * Returns the first element in an array.
     *
     * @param  array $array
     *
     * @return mixed
     */
    public static function array_first(array $array)
    {
        return reset($array);
    }

    /**
     * Returns the last element in an array.
     *
     * @param  array $array
     *
     * @return mixed
     */
    public static function array_last(array $array)
    {
        return end($array);
    }

    /**
     * Returns the first key in an array.
     *
     * @param  array $array
     *
     * @return int|string
     */
    public static function array_first_key(array $array)
    {
        reset($array);

        return key($array);
    }

    /**
     * Returns the last key in an array.
     *
     * @param  array $array
     *
     * @return int|string
     */
    public static function array_last_key(array $array)
    {
        end($array);

        return key($array);
    }

    /**
     * Checks if the given key or index exists in the array
     *
     * @param array $array
     * @param mixed $key
     *
     * @return bool return true if the given key is set in the array. key can be any value possible for an array index.
     */
    public static function array_key_exists(array $array, $key)
    {
        return isset($array[$key]) || array_key_exists($array, $key);
    }

    /**
     * Output buffer a file and return it's content
     *
     * @param $path
     *
     * @return string
     */
    public static function ob_get_require_once($path)
    {
        ob_start();
        require_once($path);

        return ob_get_clean();
    }

    /**
     * Flatten a multi-dimensional array into a one dimensional array.
     *
     * @param array   $array         The array to flatten
     * @param boolean $preserve_keys Whether or not to preserve array keys.  Keys from deeply nested arrays will
     *                               overwrite keys from shallow nested arrays
     *
     * @return array
     */
    public static function array_flatten(array $array, $preserve_keys = true)
    {
        $flattened = [];

        foreach ($array as $key => $value)
        {
            if (is_array($value))
            {
                $flattened = array_merge($flattened, static::array_flatten($value, $preserve_keys));
            }
            else
            {
                if ($preserve_keys)
                {
                    $flattened[$key] = $value;
                }
                else
                {
                    $flattened[] = $value;
                }
            }
        }

        return $flattened;
    }

    /**
     * A time is old enough if the current time is greater than the user time + the max age
     *
     * @param int $time    current time in seconds
     * @param int $max_age max time in seconds
     *
     * @return bool
     */
    public static function isOldEnough($time, $max_age)
    {
        return time() > ($time + $max_age);
    }

    /**
     * Strip all whitespace from the given string.
     *
     * @param  string $string The string to strip
     *
     * @return string
     */
    public static function str_strip_space($string)
    {
        return preg_replace('/\s+/', '', $string);
    }

    /**
     * Check if a string starts with the given string.
     *
     * @param  string $string
     * @param  string $starts_with
     *
     * @return bool
     */
    public static function str_starts_with($string, $starts_with)
    {
        return mb_strpos($string, $starts_with) === 0;
    }

    /**
     * Check if a string ends with the given string.
     *
     * @param  string $string
     * @param  string $ends_with
     *
     * @return bool
     */
    public static function str_ends_with($string, $ends_with)
    {
        return mb_substr($string, -mb_strlen($ends_with)) === $ends_with;
    }

    /**
     * Check if a string contains another string.
     *
     * @param  string $haystack
     * @param  string $needle
     *
     * @return bool
     */
    public static function str_contains($haystack, $needle)
    {
        return mb_strpos($haystack, $needle) !== false;
    }

    /**
     * Check if a string contains another string. This version is case
     * insensitive.
     *
     * @param  string $haystack
     * @param  string $needle
     *
     * @return bool
     */
    public static function str_icontains($haystack, $needle)
    {
        return mb_stripos($haystack, $needle) !== false;
    }

    /**
     * Checks if the captcha keys are set in the config, otherwise it will display an error and exit
     */
    public static function validateCaptchaKeysSet()
    {
        $has_captcha_keys = defined('CAPTCHA_SITE_KEY') && defined('CAPTCHA_SECRET') && !empty(CAPTCHA_SITE_KEY) &&
                            !empty(CAPTCHA_SECRET);
        if (!$has_captcha_keys)
        {
            // Display nice message to developer warning him about missing captcha keys
            $message = <<<MSG
<p>If you do not have keys already then visit
<a href = "https://www.google.com/recaptcha/admin">https://www.google.com/recaptcha/admin</a> 
to generate them. Edit the config.php file and set the respective keys in CAPTCHA_SITE_KEY and
CAPTCHA_SECRET. Reload the page after this.</p>
MSG;

            exit(StkTemplate::get('error-page.tpl')
                ->assign('error', ['title' => 'Add your captcha keys', 'message' => $message])->toString());
        }
    }

    /**
     * Checks to see if the page is requested by an AJAX (xmlhttprequest) request
     *
     * @return bool
     */
    public static function isAJAXRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Checks to see if the page is being server over SSL or not
     *
     * @return bool
     */
    public static function isHTTPS()
    {
        return isset($_SERVER["HTTPS"]) && $_SERVER['HTTPS'] === "on";
    }

    /**
     * Do a HTTP redirect.
     * This functions exits after the redirect. Use with care.
     *
     * @param string $url       the url to redirect
     * @param bool   $permanent is the request permanent or not.
     */
    public static function redirectTo($url, $permanent = false)
    {
        header("Location: " . $url, true, $permanent ? 301 : 302);
        exit;
    }

    /**
     * Do a HTTP redirect to the STK error page
     * TODO add message option
     *
     * @param int  $error     the http error
     * @param bool $permanent is the request permanent or not.
     */
    public static function redirectError($error, $permanent = false)
    {
        static::redirectTo(ROOT_LOCATION . sprintf("error.php?e=%d", (int)$error), $permanent);
    }

    /**
     * Get url address
     *
     * @param bool $request_params      retrieve the url tih the GET params
     * @param bool $request_script_name retrieve the url with only the script name
     *
     * Possible usage: getCurrentUrl(true, false) - the default, get the full url
     *                 getCurrentUrl(false, true) - get the url without the GET params only the script name
     *                 getCurrentUrl(false, false) - get the url's directory path only
     *
     * @return string
     */
    public static function getCurrentUrl($request_params = true, $request_script_name = false)
    {
        // begin buildup
        $page_url = "http";

        // add for ssl secured connections
        if (static::isHTTPS())
        {
            $page_url .= "s";
        }
        $page_url .= "://";

        // find the end part of the url
        if ($request_params) // full url with requests
        {
            $end_url = $_SERVER["REQUEST_URI"];
        }
        elseif ($request_script_name) // full url without requests
        {
            $end_url = $_SERVER["SCRIPT_NAME"];
        }
        else // url directory path
        {
            $end_url = dirname($_SERVER["SCRIPT_NAME"]) . "/";
        }

        // add host
        $page_url .= $_SERVER["SERVER_NAME"];

        if ((int)$_SERVER["SERVER_PORT"] !== 80)
        {
            $page_url .= ":" . $_SERVER["SERVER_PORT"] . $end_url;
        }
        else
        {
            $page_url .= $end_url;
        }

        return $page_url;
    }

    /**
     * Get an hash map of all the url vars where the key is the name
     *
     * @param string $query
     *
     * @return array
     */
    public static function getQueryVars($query)
    {
        // build vars
        $vars = [];
        $hashes = explode("&", $query);
        foreach ($hashes as $hash)
        {
            $hash = explode("=", $hash);
            $len_hash = count($hash);

            // At least the key exists
            if ($len_hash != 0)
            {
                $key = $hash[0];

                $value = '';
                // the value exists
                if  ($len_hash >= 1)
                    $value = $hash[1];

                // key => value
                $vars[$key] = $value;
            }
        }

        return $vars;
    }

    /**
     * Removes an item or list from the query string.
     *
     * @param string|array $keys Query key or keys to remove.
     * @param string       $url
     *
     * @return string
     */
    public static function removeQueryArguments(array $keys, $url)
    {
        $parsed = parse_url($url);
        $url = rtrim($url, "?&");

        // the query is empty
        if (empty($parsed["query"]))
        {
            return $url . "?";
        }

        $vars = static::getQueryVars($parsed["query"]);

        // remove query
        foreach ($keys as $key)
        {
            unset($vars[$key]);
        }

        $query = empty($vars) ? "" : http_build_query($vars) . "&";

        $new_url = $parsed["scheme"] . "://" . $parsed["host"] . $parsed["path"] . "?" . $query;

        return $new_url;
    }

    /**
     * Returns ip address of the client
     *
     * Source :
     * http://stackoverflow.com/questions/1634782/what-is-the-most-accurate-way-to-retrieve-a-users-correct-ip-address-in-php?
     * @return string|bool return the ip of the user or false in case of error
     */
    public static function getClientIp()
    {
        $ip_pool = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED'
        ];

        foreach ($ip_pool as $ip)
        {
            if (!empty($_SERVER[$ip]))
            {
                if (static::isIP($_SERVER[$ip]))
                {
                    return $_SERVER[$ip];
                }
            }
        }

        return !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
    }

    /**
     * Get the current running script path
     *
     * @param bool $basename to return script filename without the path
     *
     * @return string the full path
     */
    public static function getScriptFilename($basename = true)
    {
        if ($basename) return basename($_SERVER["SCRIPT_FILENAME"]);

        return $_SERVER["SCRIPT_FILENAME"];
    }

    /**
     * Get the html purifier config with all necessary settings preset
     *
     * @return HTMLPurifier_Config
     */
    public static function getHTMLPurifierConfig()
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set("Core.Encoding", "UTF-8");
        $config->set("Cache.SerializerPath", CACHE_PATH);
        $config->set(
            "HTML.AllowedElements",
            ["h3", "h4", "h5", "h6", "p", "img", "a", "ol", "li", "ul", "b", "i", "u", "small", "blockquote"]
        );
        $config->set("HTML.MaxImgLength", 480);
        $config->set("CSS.MaxImgLength", "480px");
        $config->set("Attr.AllowedFrameTargets", ["_blank", "_self", "_top", "_parent"]);

        return $config;
    }

    /**
     * Purify a string (html escape) with the default config
     *
     * @param string $string
     *
     * @return string the string purified
     */
    public static function htmlPurify($string)
    {
        static $instance;
        if (!$instance)
        {
            $instance = HTMLPurifier::getInstance(static::getHTMLPurifierConfig());
        }

        return $instance->purify($string);
    }

    /**
     * Apply the html purify on each key of a matrix
     *
     * @param array  $array
     * @param string $apply_key the key to apply the purify
     */
    public static function htmlPurifyApply(array &$array, $apply_key)
    {
        foreach ($array as $index => $data)
        {
            $array[$index][$apply_key] = static::htmlPurify($array[$index][$apply_key]);
        }
    }

    /**
     * See if an checkbox is checked
     *
     * @param array  $pool         array to search for
     * @param string $checkbox_key the key of the checkbox
     *
     * @return bool
     */
    public static function isCheckboxChecked(array $pool, $checkbox_key)
    {
        return empty($pool[$checkbox_key]) ? false : $pool[$checkbox_key] === "on";
    }

    /**
     * Check if valid email
     *
     * @param string $email
     *
     * @return bool
     */
    public static function isEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Check if valid url
     *
     * @param string $url
     *
     * @return bool
     */
    public static function isURL($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Check if valid IPv4 or IPv6 address, except private ranges and reserved ranges
     *
     * @param string $ip
     *
     * @return bool
     */
    public static function isIP($ip)
    {
        $flags = FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;

        return filter_var($ip, FILTER_VALIDATE_IP, $flags) !== false;
    }

    /**
     * Checks if the password is salted
     *
     * @param string $hash_password the hash value of a password
     *
     * @return bool
     */
    public static function isPasswordSalted($hash_password)
    {
        return mb_strlen($hash_password) === (static::SALT_LENGTH + static::SHA256_LENGTH);
    }

    /**
     * Get the salt part of a password
     *
     * @param string $hash_password the hash value of a password
     *
     * @return string
     */
    public static function getSaltFromPassword($hash_password)
    {
        return mb_substr($hash_password, 0, static::SALT_LENGTH);
    }

    /**
     * Generate the hash for a password
     *
     * @param string      $raw_password the plain password
     * @param null|string $salt         optional, give it own salt
     *
     * @return string
     */
    public static function getPasswordHash($raw_password, $salt = null)
    {
        if (!$salt) // generate our own salt
        {
            // when we retrieve it from the database it will be already utf8, because we encoded it as utf8
            $salt = utf8_encode(mcrypt_create_iv(static::SALT_LENGTH, MCRYPT_DEV_URANDOM));
        }

        return $salt . hash("sha256", $salt . $raw_password);
    }

    /**
     * Generate a alphanumerical session id
     *
     * @return string session id of length 24
     */
    public static function getClientSessionId()
    {
        return mb_substr(md5(uniqid(mt_rand(), true)), 0, 24);
    }

    /**
     * Generates a string of random characters.
     *
     * @param   integer $length             The length of the string to generate
     * @param   boolean $human_friendly     Whether or not to make the string human friendly by removing characters
     *                                      that can be confused with other characters (O and 0, l and 1, etc)
     * @param   boolean $include_symbols    Whether or not to include symbols in the string. Can not be enabled if
     *                                      $human_friendly is true
     * @param   boolean $no_duplicate_chars Whether or not to only use characters once in the string.
     *
     * @throws LengthException
     * @return  string
     */
    public static function getRandomString(
        $length,
        $human_friendly = true,
        $include_symbols = false,
        $no_duplicate_chars = false
    ) {
        $nice_chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefhjkmnprstuvwxyz23456789';
        $all_an = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
        $symbols = '!@#$%^&*()~_-=+{}[]|:;<>,.?/"\'\\`';
        $string = '';

        // Determine the pool of available characters based on the given parameters
        if ($human_friendly)
        {
            $pool = $nice_chars;
        }
        else
        {
            $pool = $all_an;

            if ($include_symbols)
            {
                $pool .= $symbols;
            }
        }

        // Don't allow duplicate letters to be disabled if the length is
        // longer than the available characters
        if ($no_duplicate_chars && mb_strlen($pool) < $length)
        {
            throw new \LengthException('$length exceeds the size of the pool and $no_duplicate_chars is enabled');
        }

        // Convert the pool of characters into an array of characters and
        // shuffle the array
        $pool = str_split($pool);
        shuffle($pool);

        // Generate our string
        for ($i = 0; $i < $length; $i++)
        {
            if ($no_duplicate_chars)
            {
                $string .= array_shift($pool);
            }
            else
            {
                $string .= $pool[0];
                shuffle($pool);
            }
        }

        return $string;
    }

    /**
     * Get the stk version formatted
     *
     * @param int    $format the version format
     * @param string $addon_type
     *
     * @return string
     */
    public static function getVersionFormat($format, $addon_type)
    {
        $format = (int)$format;
        switch ($addon_type)
        {
            case Addon::KART:
                // latest version number should be set here
                // https://svn.code.sf.net/p/supertuxkart/code/media/trunk/blender_25/stk_kart.py
                if ($format === 1)
                {
                    return 'Pre-0.7';
                }
                if ($format === 2)
                {
                    return '0.7.0 - ' . _h("Latest development version");
                }

                return _h('Unknown');
                break;

            case Addon::TRACK:
            case Addon::ARENA:
                // latest version number should be set here
                // https://svn.code.sf.net/p/supertuxkart/code/media/trunk/blender_25/stk_track.py
                if ($format === 1 || $format === 2)
                {
                    return 'Pre-0.7';
                }
                if ($format >= 3 && $format <= 5)
                {
                    return '0.7.0 - 0.8.1';
                }
                if ($format === 6)
                {
                    return '0.9.0 - ' . _h("Latest development version");
                }

                return _h('Unknown');
                break;

            default:
                return _h('Unknown');
        }
    }

    /**
     * Parse a comma string list to an array
     *
     * @param string $string a comma string like 1, 2, 3, 4
     *
     * @return array [1, 2, 3, 4]
     */
    public static function commaStringToArray($string)
    {
        return array_map("trim", explode(',', $string));
    }
}
 