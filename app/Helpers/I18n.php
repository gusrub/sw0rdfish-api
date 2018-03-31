<?php

namespace Sw0rdfish\Helpers;

use \InvalidArgumentException as InvalidArgumentException;

/**
 * This class contain helper methods to localize and translate the application.
 */
class I18n
{
    /**
     * @var The original text that is going to be translated
     */
    private $originalText;

    /**
     * @var An array containing key-value pairs for any replacement needed to be
     *  done within the translation string
     */
    private $translationArguments;

    /**
     * @var The translated text to the target language. Note that this does not
     *  have yet the interpolated values from $translationArguments
     */
    private $translatedText;

    /**
     * Creates a new instance of a translator.
     *
     * @param string $str The string to be translated.
     * @param Array $args A key value-pair array containing replacements to be
     *  done in the string.
     * @param string $language If this is set, it will override the system
     *  language or the one configured globally in the application.
     * @return I18n A translation object.
     */
    private function __construct($str, Array $args = null, $language = null)
    {
        $this->setLanguage($language);
        $this->originalText = $str;
        $this->translationArguments = $args;
    }

    /**
     * Sets the language to be used to translate.
     *
     * This method will, by default, try to get the LOCALE from argument given,
     * if none is given, which is the default, it will try to get a default from
     * the request if one was given or from the application configuration as a
     * fallback.
     *
     * @param string $language The language to use for the translation. e.g.
     *  'es_MX.utf8'
     */
    private function setLanguage($language = null)
    {
        if (is_null($language)) {
            $language = $this->getDefaultLanguage();
        }

        putenv("LC_ALL=$language");
        putenv("LC_LANG=$language");
        setlocale(LC_ALL, $language);

        // // Set the text domain as 'messages'
        $domain = 'messages';
        bindtextdomain($domain, __DIR__.'/../../locales');
        textdomain($domain);
    }

    /**
     * Gets the default LOCALE code from either the request or from the
     * application configuration.
     *
     * @return string The LOCALE code, e.g. 'es_MX.utf8'
     */
    private function getDefaultLanguage()
    {
        // let's see if client is requesting a specific language
        if (array_key_exists('HTTP_X_LANGUAGE', $_SERVER)) {
            $language = $_SERVER['HTTP_X_LANGUAGE'];
        } else {
            $language = getenv('APP_LANG');
        }

        return $language;
    }

    /**
     * Checks that the given arguments to replace values in the translation
     * match the actual amount of arguments in the translation string.
     */
    private function validateArguments()
    {
        $matches = array();
        preg_match_all('/{+(.*?)}/', $this->originalText, $matches);
        $matches = $matches[1];
        $matchCount = count($matches);
        $wrongArgs = false;

        if ($matchCount > 0 && is_null($this->translationArguments)) {
            $wrongArgs = true;
        } else if ($matchCount > 0 && isset($this->translationArguments)) {
            $diff = array_diff($matches, array_keys($this->translationArguments));
            if (count($diff) > 0) {
                $wrongArgs = true;
            }
        }

        if ($wrongArgs) {
            throw new InvalidArgumentException("Wrong number of arguments for translation");
        }
    }

    /**
     * Extracts the argument values and replaces ocurrences in the translated
     * string .
     *
     * @return string The translated string with the patterns replaced.
     */
    private function extractArguments()
    {
        $patterns = array();
        $replacements = array();
        $counter = 0;

        if (isset($this->translationArguments)) {
            foreach ($this->translationArguments as $key => $value) {
                $patterns[$counter] = '/'."{{$key}}".'/';
                $replacements[$counter] = $value;
                $counter++;
            }

            return preg_replace($patterns, $replacements, $this->translatedText);
        }

        return $this->translatedText;
    }

    /**
     * Translates a given string of text by using gettext.
     *
     * This function will take a string of text and try to locale a translateion
     * based either on the language given as argument or by searching on the
     * reququest, if neither are present then it fallsback to the application
     * default configuration.
     *
     * It can also receive an argument with an array of key-value pairs with
     * strings that should be replaced with certain values.
     *
     * @param string $str The string to be translated.
     * @param Array $args A key-value pair array of ocurrences to be replaced in
     *  the translated string. Defaults to null.
     * @param string $language If given, it will override the request info or
     *  global application configuration and try to translate with this locale.
     *
     * @return string The translated string.
     */
    public static function translate($str, Array $args = null, $language = null)
    {
        $translation = new self($str, $args, $language);
        $translation->validateArguments();
        $translation->translatedText = _($translation->originalText);

        return $translation->extractArguments();
    }
}
