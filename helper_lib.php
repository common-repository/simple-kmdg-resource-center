<?php
namespace KMDG\SIMPLERC;
/**
 * Tests if a string starts with another string.
 * @param $needle
 * @param $haystack
 * @return bool
 */
function starts_with($needle,$haystack){
    return (substr($haystack, 0, strlen($needle) ) === $needle);
}

/**
 * Tests if a string ends with given string.
 * @param $needle
 * @param $haystack
 * @return bool
 */
function ends_with($needle,$haystack){
    return (substr($haystack,-strlen($needle)) === $needle);
}

/**
 * Returns the extension of a file.
 * @param $fileName
 * @return false|string
 */
function get_extension($fileName){
    return substr(strrchr($fileName, '.'), 1);
}

/**
 * Given a root path and optional extensions to look for, will walk all directories in the given path and find files
 * that end in the extension.
 * @param false $path
 * @param array $extensions
 * @param array $files
 * @return array|mixed
 */
function file_walker($path = false,$extensions = [],$files = []){
    if (!is_array($extensions)){
        $extensions = [$extensions];
    }
    if (!$path){
        $to_process = scandir($path);
        foreach($to_process as $processing){
            if(is_dir($processing)){
                if(!starts_with(".",$processing)){
                    file_walker($processing,$extensions,$files);
                }
            }
            else {
                if($extensions.length < 1 or in_array(get_extension($processing),$extensions))
                    $files[] = $processing;
            }
        }
    }
    return $files;
}

/**
 * Given an array of an exploded directory, adds slashes between them.
 *
 * @param $directory_array
 * @param string $separator
 * @return string
 */
function slash_it($directory_array,$separator = DIRECTORY_SEPARATOR){
    return implode($separator,$directory_array);
}

/**
 * Recursivly creates directories up to the given directory.
 *
 * @param $directory Array of Strings or String of Directory
 * @return false|mixed|string Returns false if errors out or cannot create the directory, otherwise returns the string of the directory.
 */
function create_dir($directory){
    if (is_array($directory)){
        $directory = slash_it($directory);
    }
    if(is_file($directory)){
        return false;
    }
    elseif(is_dir($directory)){
        return $directory;
    }
    else {
        create_dir(dirname($directory));
        return create_dir($directory);
    }
}

/**
 * Given a file that should be located in the plugin, provides the directory to it.
 * @param $file
 * @return string
 */
function plugin_path_helper($file=""){
    $plugin_dir = dirname(__FILE__);
    if(is_array($file)){
        $file = slash_it($file);
    }
    return slash_it([$plugin_dir,$file]);
}

/**
 * Given a file that should be located by URL, provides the URL path to it.
 * @param string $file
 * @return string
 */
function plugin_url_helper($file=""){
    $plugin_url = plugin_dir_url(__FILE__);
    if(is_array($file)){
        $file = slash_it($file,'/');
    }
    return $plugin_url.$file;
}

/**
 * Given a file that should be located in the theme, provides the directory to it.
 * @param $file
 * @return string
 */
function theme_path_helper($file){
    $theme_dir = get_template_directory();
    return slash_it([$theme_dir,$file]);
}

/**
 * Given a filepath, gives the file name. Given a directory, gives the top level directory name.
 * @param $path
 * @return mixed|string
 */
function get_top_level_from_path($path){
    if(!is_array($path)) {
        $path = $path.explode(DIRECTORY_SEPARATOR);
    }
    return end($path);
}

/**
 * Given a presumably Vimeo or YouTube link, parses it to give its embed URL.
 * @param $url
 * @return string
 */
function parse_video_link($url){
    if     (strpos($url,"youtu")){ return parse_youtube_link($url); }
    elseif (strpos($url,"vimeo")){ return parse_vimeo_link($url);   }
    return '';
}

/**
 * Parses a youtube link to get its embed url
 * @param $url
 * @return string
 */
function parse_youtube_link($url){
    $matches = [];
    $val = '';
    if (preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches)){
        $val = $matches[1];
    }
    $val = "https://www.youtube.com/embed/".$val;
    return $val;
}

/**
 * Parses a Vimeo link to get its embed url
 * @param $url
 * @return string
 */
function parse_vimeo_link($url){
    $matches = [];
    $val = '';
    if (preg_match('%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $url, $matches)){
        $val = $matches[1];
    }
    $val = "https://player.vimeo.com/video/".$val."?controls=0";
    return $val;
}

/**
 * A funciton to hook ento to register and queue our styles.
 */
function enqueue_rc_sources(){
    $css_url = plugin_url_helper(['dist','css','resources.css']);
    wp_register_style("kmdg-simple-rc-styles",$css_url);
    wp_enqueue_style("kmdg-simple-rc-styles");
    # wp_enqueue_scripts("kmdg-simple-rc-scripts",plugin_url_helper('dist/js/resources.min.js'));
}

/**
 * A simple version of a string trimmer to limit by characters instead of words IE wp_trim_words
 * @param $trim_string
 * @param int $length
 * @return string
 */
function kmdg_trim($trim_string,$length=50)
{
    return mb_strimwidth($trim_string, 0, $length, '...');
}

function kmdg_esc($str){
    echo(esc_html($str));
}
