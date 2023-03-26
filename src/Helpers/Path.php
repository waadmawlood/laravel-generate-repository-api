<?php

namespace Waad\Repository\Helpers;

class Path
{
    /**
     * get Path Direction with Seperator system
     * @param array|string $path
     * @param string $seperator
     * @return string
     */
    public static function getPath(array|string $path, string $seperator = '/')
    {
        if (is_array($path)) {
            return implode(DIRECTORY_SEPARATOR, $path);
        }

        return self::getPath(explode($seperator, $path));
    }

    /**
     * create directory
     * @param string $dir
     * @param int|null $permission
     * @param bool|null $recursive
     * @return bool
     */
    public static function createDir(string $dir, int|null $permission = 0777, bool|null $recursive = true)
    {
        $dir = static::getPath($dir);

        if (!is_dir($dir)) {
            $oldmask = umask(0);
            $flag = mkdir($dir, $permission, $recursive);
            umask($oldmask);
            return $flag;
        }

        return false;
    }

    /**
     * create File use `file_put_contents`
     * @param string $filename
     * @param string|null $content
     * @param bool|null $is_php
     * @return bool
     */
    public static function createFile(string $filename, string|null $content = null, bool|null $is_php = false)
    {
        $content = $is_php ? self::addPhpTag($content) : $content;
        $status = file_put_contents($filename, $content);

        return (bool) $status;
    }


    /**
     * get Content Stub
     *
     * @param mixed $name_stub
     * @return bool|null|string
     */
    public static function getContentStub($name_stub)
    {
        $path = self::getPath(__DIR__ . "/../stubs/" . $name_stub);

        if(file_exists($path)){
            return file_get_contents($path);
        }

        return null;
    }


    /**
     * replace Contents stubs
     * @param string $content
     * @param array $options
     * @return void
     */
    public static function replaceContents(string &$content, array $options)
    {
        foreach($options as $key => $value){
            $content = str_replace($key, $value, $content);
        }
    }

    /**
     * put content in file stream use `fopen`
     * @param string $filename
     * @param string $content
     * @return bool
     */
    public static function putContent(string $filename, string $content)
    {

        $file = fopen($filename, "w");

        if(! $file){
            return false;
        }

        fwrite($file, $content);
        fclose($file);

        return true;
    }

    /**
     * add PHP tag in begin content
     * @param mixed $content
     * @return string
     */
    private static function addPhpTag($content = null)
    {
        return "<?php\n\n" . $content;
    }
}
