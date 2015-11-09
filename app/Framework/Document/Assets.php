<?php
namespace HMC\Document;

/**
 * Helper to ease inclusion of scripts and stylesheets with optional optimization.
 *
 * @author Ebben Feagan - ebben@hmcsoft.com
 * @author volter9
 * @author QsmaPL

 */

class Assets
{
    /**
     * @var array Asset templates
     */
    protected static $templates = array
    (
        'js'  => '<script src="%s" type="text/javascript"></script>',
        'css' => '<link href="%s" rel="stylesheet" type="text/css">'
    );

    /**
     * Template function that does the low-level output writing.
     *
     * @param string|array $files
     * @param string       $template
     */
    protected static function resource($files, $template)
    {
        $template = self::$templates[$template];

        if (is_array($files)) {
            foreach ($files as $file) {
                echo sprintf($template, $file) . "\n";
            }
        } else {
            echo sprintf($template, $files) . "\n";
        }
    }

    /**
     * Javascript (unoptimized) helper.
     *
     * @param array|string $file
     */
    public static function js($files)
    {
        static::resource($files, 'js');
    }

    /**
    * Javascript (optimized) helper.
    * All scripts are optimized for download size and formed into a single file.
    * Optimization is done by JShrink\Minimizer.
    *
    * This file is updated automatically if the files change.
    *
    * @param $files - array of files to include
    * @param $outputdir - where to place the generated files, default is typically adequate.
    */
    public static function combine_js($files,$outputdir = 'static/generated/')
    {
      if(\HMC\Config::SITE_ENVIRONMENT() == 'development') {
        Assets::js($files);
        return;
      }
      $ofiles = (is_array($files) ? $files : array($files));
      $hashFileName = md5(join($ofiles));
      $dirty = false;

      if(file_exists($outputdir.$hashFileName.'.js')) {
        $hfntime = filemtime($outputdir.$hashFileName.'.js');
        foreach($ofiles as $vfile) {
          $file = str_replace(\HMC\Config::SITE_URL(),\HMC\Config::SITE_PATH(),$vfile);
          if(!$dirty){
            $fmtime = filemtime($file);
            if($fmtime > $hfntime)  {
              $dirty = true;
            }
          }
        }
      } else {
        $dirty = true;
      }
      if($dirty) {
        $buffer = "";
        foreach ($ofiles as $vfile) {
          $jsFile = str_replace(\HMC\Config::SITE_URL(),\HMC\Config::SITE_PATH(),$vfile);
          $buffer .= "\n".file_get_contents($jsFile);
        }

        ob_start();

        // Write everything out
        echo($buffer);

        $fc = ob_get_clean();

        $minifiedCode = \JShrink\Minifier::minify($fc, array('flaggedComments' => false));

        file_put_contents(SITEROOT.$outputdir.$hashFileName.'.js',$minifiedCode);

      }
      static::resource(str_replace(':||','://',str_replace('//','/',str_replace('://',':||',\HMC\Config::SITE_URL().$outputdir.$hashFileName.'.js'))),'js');
    }

    /**
     * Output stylesheet
     *
     * @param string $file
     */
    public static function css($files)
    {
        static::resource($files, 'css');
    }

    /**
    * Output optimized stylesheet.
    * This function will combine multiple stylesheets into a single sheet.
    * It will also perform some basic optimizations to reduce download size.
    * @param $files - array of files to include
    * @param $outputdir - where to store generated file(s), default is typically adequate.
    */
    public static function combine_css($files,$outputdir = 'static/generated/')
    {
      if(\HMC\Config::SITE_ENVIRONMENT() == 'development') {
        Assets::css($files);
        return;
      }
      $ofiles = (is_array($files) ? $files : array($files));
      $hashFileName = md5(join($ofiles));
      $dirty = false;

      if(file_exists($outputdir.$hashFileName.'.css')) {
        $hfntime = filemtime($outputdir.$hashFileName.'.css');
        foreach($ofiles as $vfile) {
          $file = str_replace(\HMC\Config::SITE_URL(),\HMC\Config::SITE_PATH(),$vfile);
          if(!$dirty){
            $fmtime = filemtime($file);
            if($fmtime > $hfntime)  {
              $dirty = true;
            }
          }
        }
      } else {
        $dirty = true;
      }
      if($dirty) {
        $buffer = "";
        foreach ($ofiles as $vfile) {
          $cssFile = str_replace(\HMC\Config::SITE_URL(),\HMC\Config::SITE_PATH(),$vfile);
          $buffer .= "\n".file_get_contents($cssFile);
        }

        // Remove comments
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);

        // Remove space after colons
        $buffer = str_replace(': ', ':', $buffer);

        // Remove whitespace
        $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);

        ob_start();

        // Write everything out
        echo($buffer);

        $fc = ob_get_clean();

        file_put_contents(\HMC\Config::SITE_PATH().$outputdir.$hashFileName.'.css',$fc);

      }
      static::resource(str_replace(':||','://',str_replace('//','/',str_replace('://',':||',\HMC\Config::SITE_URL().$outputdir.$hashFileName.'.css'))),'css');
    }
}
