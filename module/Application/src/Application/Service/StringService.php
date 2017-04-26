<?php
namespace Application\Service;

use Zend\Filter;

class StringService
{
    const JSON_ENCODE_SETTINGS = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;

    /**
     * @param string $string
     * @param int $length
     * @param array $options
     * @return string
     */
    public static function clearString($string, $length = 0, array $options = [])
    {
        $defaults = [
            'ellipsis' => false,
            'pattern' => '/\W/',
        ];
        $options = array_merge($defaults, $options);

        $filter = new Filter\StripTags();
        $clean_string = $filter->filter($string);
        $filter = new Filter\StringTrim();
        $clean_string = $filter->filter($clean_string);
        if ($options['pattern'] !== '') {
            $clean_string = preg_replace($options['pattern'], '', $clean_string);
        }
        if ($length !== 0 && mb_strlen($clean_string) > $length) {
            $clean_string = mb_substr($clean_string, 0, $length);
            if ($options['ellipsis'] === true) {
                $clean_string .= ' ...';
            }
        }

        return $clean_string;
    }

    /**
     * @return string
     */
    public static function siteURL()
    {
        return self::siteProtocol() . $_SERVER['HTTP_HOST'];
    }

    public static function siteProtocol()
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            $protocol = $_SERVER['HTTP_X_FORWARDED_PROTO'] . '://';
        } else {
            $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                (int)$_SERVER['SERVER_PORT'] === 443) ? 'https://' : 'http://';
        }
        return $protocol;
    }

    /**
     * @param string $input
     * @return string
     */
    public static function fromCamelCase($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match === strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    /**
     * @param string $string
     * @param bool $capitalizeFirstCharacter
     * @return string
     */
    public static function toCamelCase($string, $capitalizeFirstCharacter = false)
    {
        $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }
        return $str;
    }
}
