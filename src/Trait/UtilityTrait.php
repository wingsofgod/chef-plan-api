<?php
declare(strict_types=1);

namespace App\Trait;

use Symfony\Component\HttpFoundation\Request;

const CONVERT_SEARCH = array("Ğ", "Ü", "Ş", "İ", "Ö", "Ç", "ğ", "ü", "ş", "ı", "ö", "ç", "I", "”", "“", "'", '&nbsp;', ' ');
const CONVERT_REPLACE = array("g", "u", "s", "i", "o", "c", "g", "u", "s", "i", "o", "c", "i", "", "", "", ' ', ' ');

const CONVERT_UPPER = array("I", "Ğ", "Ü", "Ş", "İ", "Ö", "Ç");
const CONVERT_LOWER = array("ı", "ğ", "ü", "ş", "i", "ö", "ç");

trait UtilityTrait
{
    public function isMobileDevice(Request $request): bool|int
    {
        return true; //todo remove after fixed huawei user-agent problem
        if (!$request->headers->has('User-Agent')) {
            return false;
        }
        return preg_match("/(android|ios|iOS|avantgo|blackberry|bolt|boost|cricket|docomo
|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos|mobile)/i"
            , $request->headers->get('User-Agent'));
    }

    public function emailPlusExtensionClear($mail): string
    {
        $p = '/\s*+\+([\w -.])+@/';
        return preg_replace($p, '@', $mail);
    }

    public function convertToSearchFormat($str): string
    {
        return strtolower(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE',
            str_replace(
                CONVERT_SEARCH,
                CONVERT_REPLACE, $str)));

    }

    public function convertToKeyValue(array $data, string $key): array
    {
        $results = [];
        foreach ($data as $datum) {
            $results[$datum[$key]] = $datum;
        }
        return $results;
    }

    public function stripNullValuesFromArray(array &$arr): void
    {
        foreach ($arr as $key => $value) {
            if (is_array($value) && empty($value)) {
                unset($arr[$key]);
            } else if (is_array($value)) {
                $this->stripNullValuesFromArray($arr[$key]);
                if (is_array($arr[$key]) && empty($arr[$key])) {
                    unset($arr[$key]);
                }
            } else if (is_null($value)) {
                unset($arr[$key]);
            }
        }
    }

    public function convertToFileName($str): string
    {
        $str = trim($str);
        $str = trim($str, chr(0xC2) . chr(0xA0));
        $str = str_replace([" ", '&nbsp;', ' '], ["-", "-", "-"], $str);
        return strtolower(@iconv('UTF-8', 'ISO-8859-1//IGNORE', str_replace(CONVERT_SEARCH, CONVERT_REPLACE, $str)));
    }

    public function removeDuplicatesFromArrayObjects(array $objects): array
    {
        return array_values(array_unique($objects, SORT_REGULAR));
    }
}