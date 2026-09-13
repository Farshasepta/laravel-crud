<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    public function show(Request $request)
    {
        $filepath = $request->input('filepath', '');
        $text = $request->input('text', '0');
        $size = $request->input('size', '20');
        $orientation = $request->input('orientation', 'horizontal');
        $codeType = $request->input('codetype', 'code128');
        $print = $request->input('print') === 'true';
        $sizeFactor = (int) $request->input('sizefactor', '1');

        $png = $this->barcode($filepath, $text, $size, $orientation, $codeType, $print, $sizeFactor);

        return response($png, 200)->header('Content-Type', 'image/png');
    }

    private function barcode($filepath = '', $text = '0', $size = '20', $orientation = 'horizontal', $codeType = 'code128', $print = false, $sizeFactor = 1)
    {
        $codeString = '';

        if (in_array(strtolower($codeType), ['code128', 'code128b'], true)) {
            $checksum = 104;
            $codeArray = $this->code128Map();
            $codeKeys = array_keys($codeArray);
            $codeValues = array_flip($codeKeys);

            for ($x = 1; $x <= strlen($text); $x++) {
                $activeKey = substr($text, $x - 1, 1);
                if (! isset($codeArray[$activeKey])) {
                    $activeKey = ' ';
                }
                $codeString .= $codeArray[$activeKey];
                $checksum = $checksum + ($codeValues[$activeKey] * $x);
            }
            $codeString .= $codeArray[$codeKeys[$checksum - (int) floor($checksum / 103) * 103]];
            $codeString = '211214'.$codeString.'2331112';
        } elseif (strtolower($codeType) === 'code128a') {
            $checksum = 103;
            $text = strtoupper($text);
            $codeArray = $this->code128aMap();
            $codeKeys = array_keys($codeArray);
            $codeValues = array_flip($codeKeys);

            for ($x = 1; $x <= strlen($text); $x++) {
                $activeKey = substr($text, $x - 1, 1);
                if (! isset($codeArray[$activeKey])) {
                    $activeKey = ' ';
                }
                $codeString .= $codeArray[$activeKey];
                $checksum = $checksum + ($codeValues[$activeKey] * $x);
            }
            $codeString .= $codeArray[$codeKeys[$checksum - (int) floor($checksum / 103) * 103]];
            $codeString = '211412'.$codeString.'2331112';
        } else {
            $codeString = '2112142331112';
        }

        $codeLength = 20;
        $textHeight = $print ? 30 : 0;

        for ($i = 1; $i <= strlen($codeString); $i++) {
            $codeLength += (int) substr($codeString, $i - 1, 1);
        }

        if (strtolower($orientation) === 'horizontal') {
            $imgWidth = $codeLength * $sizeFactor;
            $imgHeight = (int) $size;
        } else {
            $imgWidth = (int) $size;
            $imgHeight = $codeLength * $sizeFactor;
        }

        $image = imagecreate($imgWidth, $imgHeight + $textHeight);
        $black = imagecolorallocate($image, 0, 0, 0);
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $white);

        if ($print) {
            imagestring($image, 5, 31, $imgHeight, $text, $black);
        }

        $location = 10;
        for ($position = 1; $position <= strlen($codeString); $position++) {
            $curSize = $location + (int) substr($codeString, $position - 1, 1);
            $color = $position % 2 === 0 ? $white : $black;

            if (strtolower($orientation) === 'horizontal') {
                imagefilledrectangle($image, $location * $sizeFactor, 0, $curSize * $sizeFactor, $imgHeight, $color);
            } else {
                imagefilledrectangle($image, 0, $location * $sizeFactor, $imgWidth, $curSize * $sizeFactor, $color);
            }
            $location = $curSize;
        }

        ob_start();
        imagepng($image);
        $png = ob_get_clean();
        imagedestroy($image);

        return $png;
    }

    private function code128Map(): array
    {
        return [
            ' ' => '212222', '!' => '222122', '"' => '222221', '#' => '121223', '$' => '121322',
            '%' => '131222', '&' => '122213', "'" => '122312', '(' => '132212', ')' => '221213',
            '*' => '221312', '+' => '231212', ',' => '112232', '-' => '122132', '.' => '122231',
            '/' => '113222', '0' => '123122', '1' => '123221', '2' => '223211', '3' => '221132',
            '4' => '221231', '5' => '213212', '6' => '223112', '7' => '312131', '8' => '311222',
            '9' => '321122', ':' => '321221', ';' => '312212', '<' => '322112', '=' => '322211',
            '>' => '212123', '?' => '212321', '@' => '232121', 'A' => '111323', 'B' => '131123',
            'C' => '131321', 'D' => '112313', 'E' => '132113', 'F' => '132311', 'G' => '211313',
            'H' => '231113', 'I' => '231311', 'J' => '112133', 'K' => '112331', 'L' => '132131',
            'M' => '113123', 'N' => '113321', 'O' => '133121', 'P' => '313121', 'Q' => '211331',
            'R' => '231131', 'S' => '213113', 'T' => '213311', 'U' => '213131', 'V' => '311123',
            'W' => '311321', 'X' => '331121', 'Y' => '312113', 'Z' => '312311', '[' => '332111',
            '\\' => '314111', ']' => '221411', '^' => '431111', '_' => '111224', '`' => '111422',
            'a' => '121124', 'b' => '121421', 'c' => '141122', 'd' => '141221', 'e' => '112214',
            'f' => '112412', 'g' => '122114', 'h' => '122411', 'i' => '142112', 'j' => '142211',
            'k' => '241211', 'l' => '221114', 'm' => '413111', 'n' => '241112', 'o' => '134111',
            'p' => '111242', 'q' => '121142', 'r' => '121241', 's' => '114212', 't' => '124112',
            'u' => '124211', 'v' => '411212', 'w' => '421112', 'x' => '421211', 'y' => '212141',
            'z' => '214121', '{' => '412121', '|' => '111143', '}' => '111341', '~' => '131141',
        ];
    }

    private function code128aMap(): array
    {
        return [
            ' ' => '212222', '!' => '222122', '"' => '222221', '#' => '121223', '$' => '121322',
            '%' => '131222', '&' => '122213', "'" => '122312', '(' => '132212', ')' => '221213',
            '*' => '221312', '+' => '231212', ',' => '112232', '-' => '122132', '.' => '122231',
            '/' => '113222', '0' => '123122', '1' => '123221', '2' => '223211', '3' => '221132',
            '4' => '221231', '5' => '213212', '6' => '223112', '7' => '312131', '8' => '311222',
            '9' => '321122', ':' => '321221', ';' => '312212', '<' => '322112', '=' => '322211',
            '>' => '212123', '?' => '212321', '@' => '232121', 'A' => '111323', 'B' => '131123',
            'C' => '131321', 'D' => '112313', 'E' => '132113', 'F' => '132311', 'G' => '211313',
            'H' => '231113', 'I' => '231311', 'J' => '112133', 'K' => '112331', 'L' => '132131',
            'M' => '113123', 'N' => '113321', 'O' => '133121', 'P' => '313121', 'Q' => '211331',
            'R' => '231131', 'S' => '213113', 'T' => '213311', 'U' => '213131', 'V' => '311123',
            'W' => '311321', 'X' => '331121', 'Y' => '312113', 'Z' => '312311', '[' => '332111',
            '\\' => '314111', ']' => '221411', '^' => '431111', '_' => '111224',
        ];
    }
}
