<?php
/**
 * Wikidot - free wiki collaboration software
 * Copyright (c) 2008, Wikidot Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * For more information about licensing visit:
 * http://www.wikidot.org/license
 *
 * @category Wikidot
 * @package Wikidot
 * @version $Id$
 * @copyright Copyright (c) 2008, Wikidot Inc.
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License
 */




class WDStringUtils {

    public static $CONVERT_ARRAY = array(
            'À'=>'A','À'=>'A','Á'=>'A','Á'=>'A','Â'=>'A','Â'=>'A',
            'Ã'=>'A','Ã'=>'A','Ä'=>'Ae','Ä'=>'A','Å'=>'A','Å'=>'A',
            'Æ'=>'Ae','Æ'=>'AE',
            'Ā'=>'A','Ą'=>'A','Ă'=>'A',
            'Ç'=>'C','Ç'=>'C','Ć'=>'C','Č'=>'C','Ĉ'=>'C','Ċ'=>'C',
            'Ď'=>'D','Đ'=>'D','Ð'=>'D','Ð'=>'D',
            'È'=>'E','È'=>'E','É'=>'E','É'=>'E','Ê'=>'E','Ê'=>'E','Ë'=>'E','Ë'=>'E',
            'Ē'=>'E','Ę'=>'E','Ě'=>'E','Ĕ'=>'E','Ė'=>'E',
            'Ĝ'=>'G','Ğ'=>'G','Ġ'=>'G','Ģ'=>'G',
            'Ĥ'=>'H','Ħ'=>'H',
            'Ì'=>'I','Ì'=>'I','Í'=>'I','Í'=>'I','Î'=>'I','Î'=>'I','Ï'=>'I','Ï'=>'I',
            'Ī'=>'I','Ĩ'=>'I','Ĭ'=>'I','Į'=>'I','İ'=>'I',
            'Ĳ'=>'IJ',
            'Ĵ'=>'J',
            'Ķ'=>'K',
            'Ł'=>'K','Ľ'=>'K','Ĺ'=>'K','Ļ'=>'K','Ŀ'=>'K',
            'Ñ'=>'N','Ñ'=>'N','Ń'=>'N','Ň'=>'N','Ņ'=>'N','Ŋ'=>'N',
            'Ò'=>'O','Ò'=>'O','Ó'=>'O','Ó'=>'O','Ô'=>'O','Ô'=>'O','Õ'=>'O','Õ'=>'O',
            'Ö'=>'Oe','Ö'=>'Oe',
            'Ø'=>'O','Ø'=>'O','Ō'=>'O','Ő'=>'O','Ŏ'=>'O',
            'Œ'=>'OE',
            'Ŕ'=>'R','Ř'=>'R','Ŗ'=>'R',
            'Ś'=>'S','Š'=>'S','Ş'=>'S','Ŝ'=>'S','Ș'=>'S',
            'Ť'=>'T','Ţ'=>'T','Ŧ'=>'T','Ț'=>'T',
            'Ù'=>'U','Ù'=>'U','Ú'=>'U','Ú'=>'U','Û'=>'U','Û'=>'U',
            'Ü'=>'Ue','Ū'=>'U','Ü'=>'Ue',
            'Ů'=>'U','Ű'=>'U','Ŭ'=>'U','Ũ'=>'U','Ų'=>'U',
            'Ŵ'=>'W',
            'Ý'=>'Y','Ý'=>'Y','Ŷ'=>'Y','Ÿ'=>'Y',
            'Ź'=>'Z','Ž'=>'Z','Ż'=>'Z',
            'Þ'=>'T','Þ'=>'T',
            'à'=>'a','á'=>'a','â'=>'a','ã'=>'a','ä'=>'ae',
            'ä'=>'ae',
            'å'=>'a','ā'=>'a','ą'=>'a','ă'=>'a','å'=>'a',
            'æ'=>'ae',
            'ç'=>'c','ć'=>'c','č'=>'c','ĉ'=>'c','ċ'=>'c',
            'ď'=>'d','đ'=>'d','ð'=>'d',
            'è'=>'e','é'=>'e','ê'=>'e','ë'=>'e','ē'=>'e',
            'ę'=>'e','ě'=>'e','ĕ'=>'e','ė'=>'e',
            'ƒ'=>'f',
            'ĝ'=>'g','ğ'=>'g','ġ'=>'g','ģ'=>'g',
            'ĥ'=>'h','ħ'=>'h',
            'ì'=>'i','í'=>'i','î'=>'i','ï'=>'i','ī'=>'i',
            'ĩ'=>'i','ĭ'=>'i','į'=>'i','ı'=>'i',
            'ĳ'=>'ij',
            'ĵ'=>'j',
            'ķ'=>'k','ĸ'=>'k',
            'ł'=>'l','ľ'=>'l','ĺ'=>'l','ļ'=>'l','ŀ'=>'l',
            'ñ'=>'n','ń'=>'n','ň'=>'n','ņ'=>'n','ŉ'=>'n',
            'ŋ'=>'n',
            'ò'=>'o','ó'=>'o','ô'=>'o','õ'=>'o','ö'=>'oe',
            'ö'=>'oe',
            'ø'=>'o','ō'=>'o','ő'=>'o','ŏ'=>'o',
            'œ'=>'oe',
            'ŕ'=>'r','ř'=>'r','ŗ'=>'r',
            'š'=>'s',
            'ù'=>'u','ú'=>'u','û'=>'u','ü'=>'ue','ū'=>'u',
            'ü'=>'ue',
            'ů'=>'u','ű'=>'u','ŭ'=>'u','ũ'=>'u','ų'=>'u',
            'ŵ'=>'w',
            'ý'=>'y','ÿ'=>'y','ŷ'=>'y',
            'ž'=>'z','ż'=>'z','ź'=>'z',
            'þ'=>'t',
            'ß'=>'ss',
            'ſ'=>'ss',
            'à'=>'a','á'=>'a','â'=>'a','ã'=>'a','ä'=>'ae',
            'å'=>'a','æ'=>'ae','ç'=>'c','ð'=>'d',
            'è'=>'e','é'=>'e','ê'=>'e','ë'=>'e',
            'ì'=>'i','í'=>'i','î'=>'i','ï'=>'i',
            'ñ'=>'n',
            'ò'=>'o','ó'=>'o','ô'=>'o','õ'=>'o','ö'=>'oe',
            'ø'=>'o',
            'ù'=>'u','ú'=>'u','û'=>'u','ü'=>'ue',
            'ý'=>'y','ÿ'=>'y',
            'þ'=>'t',
            'ß'=>'ss',
            ' '=>'-',
            ','=>'-',
            '/'=>'-',
            '.'=>'-'

        );

    public static function toUnixName($text){
        $text = trim($text);
        $text = strtr($text, self::$CONVERT_ARRAY);

        // and absolutely purify the string removing all unwanted characters
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\-:_]/', '-', $text);
        $text = preg_replace(';^_;', ':_', $text);
        $text = preg_replace(';(?<!:)_;', '-', $text);
        $text = preg_replace('/^\-*/','',$text);
        $text = preg_replace('/\-*$/','',$text);
        $text = preg_replace('/[\-]{2,}/','-',$text);
        $text = preg_replace('/[:]{2,}/',':',$text);

        $text = str_replace(':-', ':', $text);
        $text = str_replace('-:', ':', $text);
        $text = str_replace('_-', '_', $text);
        $text = str_replace('-_', '_', $text);

        $text = preg_replace('/^:/', '', $text);
        $text = preg_replace('/:$/', '', $text);

        return $text;

    }

}
