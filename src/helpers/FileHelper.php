<?php

namespace luya\helpers;

/**
 * Extending the Yii File Helper class.
 *
 * @author nadar
 */
class FileHelper extends \yii\helpers\BaseFileHelper
{
    public static function humanReadableFilesize($size)
    {
        $mod = 1024;
        $units = explode(' ', 'B KB MB GB TB PB');
        for ($i = 0; $size > $mod; ++$i) {
            $size /= $mod;
        }

        return round($size, 2).' '.$units[$i];
    }

    public static function appendExtensionToString($file, $extension)
    {
        $info = pathinfo($file);
        if (!isset($info['extension'])) {
            $file = $info['filename'].'.'.$extension;
        }

        return $file;
    }
    
    public static function getFileInfo($sourceFile)
    {
        $path = pathinfo($sourceFile);
    
        return (object) [
            'extension' => $path['extension'],
            'name' => $path['filename'],
        ];
    }
    
    public static function getFileHash($sourceFile)
    {
        return hash_file('md5', $sourceFile);
    }
}
