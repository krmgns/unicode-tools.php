<?php
/**
 * Unicode Tools
 * A buch of unicode functions that make sometimes coding PHP more easy...
 * 
 * @version  : v0.1
 * @copyright: Kerem Gunes (2013) <http://qeremy.com/>
 * @licence  : GNU General Public License v3.0 <http://www.gnu.org/licenses/gpl.html>
 * 
 * Note: These functions are work properly if the containing file is "encoded in UTF-8 (or/and without BOM)"
 */

mb_internal_encoding('utf-8'); // @important

/**
 * A proper (logical) substr alternative for unicode strings
 * 
 * $str = 'Büyük'               // Big
 * $s = 0                       // start from "0" (nth) char
 * $l = 3                       // get "3" chars
 * substr($str, $s, $l)         // Bü
 * mb_substr($str, $s, $l)      // Bü
 * substr_unicode($str, $s, $l) // Büy
 */
function substr_unicode($str, $s, $l = null) {
    return join('', array_slice(
        preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY), $s, $l));
}

/**
 * A alternative unicode string shuffle
 * 
 * $str = 'Şeker yârim'      // My sweet love
 * str_shuffle($str)         // i?eymrTekr ?
 * str_shuffle_unicode($str) // Sr mreyeikâ
 */
function str_shuffle_unicode($str) {
    $tmp = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
    shuffle($tmp);
    return join('', $tmp);
}

/**
 * An alternative for unicode string chunk
 * 
 * $str = 'Yarım kilo çay'      // Half kilo tea
 * chunk_split($str, 4)         // too long for doc, test yourself pls
 * chunk_split_unicode($str, 4) // too long for doc, test yourself pls
 */
function chunk_split_unicode($str, $l = 76, $e = "\r\n") {
    $tmp = array_chunk(
        preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY), $l);
    $str = '';
    foreach ($tmp as $t) {
        $str .= join('', $t) . $e;
    }
    return $str;
}

/**
 * A simple approach to unicode ucfirst/lcfirst & lcwords/ucwords
 */
function lcfirst_unicode($str) {
    return mb_convert_case(
        mb_substr($str, 0, 1), MB_CASE_LOWER) . 
        mb_substr($str, 1);
}
function ucfirst_unicode($str) {
    return mb_convert_case(
        mb_substr($str, 0, 1), MB_CASE_UPPER) . 
        mb_substr($str, 1);
}

function lcwords_unicode($str) {
    return preg_replace_callback('/(\w+)/u', function($s) {
        return lcfirst_unicode($s[1]);
    }, $str);
}

function ucwords_unicode($str) {
    return preg_replace_callback('/(\w+)/u', function($s) {
        return ucfirst_unicode($s[1]);
    }, $str);
}

/**
 * A proper Turkish solution for ucfirst/lcfirst
 * 
 * $str = 'iyilik güzelLİK'
 * ucfirst($str)         // Iyilik güzelLİK
 * ucfirst_turkish($str) // İyilik güzelLİK
 */
function ucfirst_turkish($str) {
    return mb_convert_case(
        str_replace('i', 'I', mb_substr($str, 0, 1)), MB_CASE_UPPER)
            . mb_substr($str, 1);
}

function lcfirst_turkish($str) {
    return mb_convert_case(
        str_replace('I', 'i', mb_substr($str, 0, 1)), MB_CASE_LOWER)
            . mb_substr($str, 1);
}

function ucfirst_turkish_v2($str) {
    $tmp = preg_split('//u', $str, 2, PREG_SPLIT_NO_EMPTY);
    return mb_convert_case(
        str_replace('i', 'I', $tmp[0]), MB_CASE_UPPER)
            . $tmp[1];
}

function lcfirst_turkish_v2($str) {
    $tmp = preg_split('//u', $str, 2, PREG_SPLIT_NO_EMPTY);
    return mb_convert_case(
        str_replace('I', 'i', $tmp[0]), MB_CASE_LOWER)
            . $tmp[1];
}

/**
 * A proper Turkish solution for ucwords/lcwords
 * 
 * $str = 'iyilik güzelLİK şeker'
 * ucwords($str)         // Iyilik GüzelLİK şeker
 * ucwords_turkish($str) // İyilik GüzelLİK Şeker
 */
function ucwords_turkish($str) {
    return preg_replace_callback('/(\w+)/u', function($s) {
        return ucfirst_turkish($s[1]);
    }, $str);
}

function lcwords_turkish($str) {
    return preg_replace_callback('/(\w+)/u', function($s) {
        return lcfirst_turkish($s[1]);
    }, $str);
}

/**
 * A proper unicode string split
 * 
 * $str = 'Ilık süt'
 * str_split($s, 3)
 * str_split_unicode($s, 3)
 */
function str_split_unicode($str, $l = 0) {
    if ($l > 0) {
        for ($i = 0, $len = mb_strlen($str); $i < $len; $i += $l) {
            $ret[] = mb_substr($str, $i, $l);
        }
        return $ret;   
    }
    return preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
}

/**
 * A personal approach counting unicode chars
 * 
 * $str = 'şeker şeker yâriiiiiiiiiimmmmm' // sugar sugar love
 * count_chars_unicode($str, 'â'))         // frequency of "â"
 * count_chars_unicode($str))              // count of uniq chars
 * count_chars_unicode($str, true))        // all chars with own frequency
 */
function count_chars_unicode($str, $x = false) {
    $tmp = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
    foreach ($tmp as $c) {
        $chr[$c] = isset($chr[$c]) ? $chr[$c] + 1 : 1;
    }
    return is_bool($x)
        ? ($x ? $chr : count($chr))
        : $chr[$x];
}

/**
 * A proper unicode string padder
 * 
 * $str = '.'
 * str_pad($str, 10, 'AO', STR_PAD_BOTH)
 * str_pad_unicode($str, 10, 'ÄÖ', STR_PAD_BOTH)
 */
function str_pad_unicode($str, $pad_len, $pad_str = ' ', $dir = STR_PAD_RIGHT) {
    $str_len = mb_strlen($str);
    $pad_str_len = mb_strlen($pad_str);
    if (!$str_len && ($dir == STR_PAD_RIGHT || $dir == STR_PAD_LEFT)) {
        $str_len = 1; // @debug
    }
    if (!$pad_len || !$pad_str_len || $pad_len <= $str_len) {
        return $str;
    }
    
    $result = null;
    if ($dir == STR_PAD_BOTH) {
        $length = ($pad_len - $str_len) / 2;
        $repeat = ceil($length / $pad_str_len);
        $result = mb_substr(str_repeat($pad_str, $repeat), 0, floor($length)) 
                    . $str 
                       . mb_substr(str_repeat($pad_str, $repeat), 0, ceil($length));
    } else {
        $repeat = ceil($str_len - $pad_str_len + $pad_len);
        if ($dir == STR_PAD_RIGHT) {
            $result = $str . str_repeat($pad_str, $repeat);
            $result = mb_substr($result, 0, $pad_len);
        } else if ($dir == STR_PAD_LEFT) {
            $result = str_repeat($pad_str, $repeat) . $str;
            $result = mb_substr($result, -$pad_len);
        }
    }
    
    return $result;
}

/**
 * A weird unicode string replacer
 * 
 * $str = 'äbc äbc'
 * strtr($str, 'ä', 'a')                  // a�bc a�bc
 * strtr($str, 'äåö', 'äåö')              // oabc oabc ??
 * strtr_unicode($str, 'ä', 'a')          // abc abc
 * strtr_unicode($str, 'äåö', 'äåö')      // abc abc
 * strtr_unicode($str, array('ä' => 'a')) // abc abc
 */
function strtr_unicode($str, $a = '', $b = '') {
    if (is_array($a)) {
        return strtr($str, $a);
    }
    $translate = array();
    $a = preg_split('~~u', $a, -1, PREG_SPLIT_NO_EMPTY);
    $b = preg_split('~~u', $b, -1, PREG_SPLIT_NO_EMPTY);
    $translate = array_combine(
        array_values($a),
        array_values($b)
    );
    return strtr($str, $translate);
}

/**
 * A simple approach to unicode words count
 * 
 * $str = 'äb"c äb3c a_b!'
 * echo str_word_count($str)         // 6
 * echo str_word_count_unicode($str) // 6
 * 
 * print_r(str_word_count($str, 1))
 * print_r(str_word_count_unicode($str, 1))
 */
function str_word_count_unicode($str, $format = 0) {
    $words = preg_split('~[\s0-9_]|[^\w]~u', $str, -1, PREG_SPLIT_NO_EMPTY);
    return ($format === 0) ? count($words) : $words;
}
