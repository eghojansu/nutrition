<?php

namespace Nutrition;

use Base;
use InvalidArgumentException;
use Log;
use Nutrition\Utils\ExtendedTemplate;
use Prefab;
use ReflectionClass;
use Template;


class App extends Prefab
{
    /** @var Log */
    protected $logger;


    /**
     * Register custom error handler
     * @return $this
     */
    public function registerErrorHandler()
    {
        Base::instance()->set('ONERROR', [$this, 'error']);

        return $this;
    }

    /**
     * Get class name based on configuration
     * @param  string $key
     * @param  string $default   default instance
     * @param  string $interface expected instance
     * @param  bool $createClass
     * @param  array $params
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function getClassName($key, $default, $interface, $createClass = false, array $params = null)
    {
        $class = Base::instance()->get($key) ?: $default;

        if (!is_subclass_of($class, $interface)) {
            throw new InvalidArgumentException(sprintf(
                '%s must be an instance of %s',
                $key,
                $interface
            ));
        }

        if ($createClass) {
            $params = (array) $params;

            if (is_subclass_of($class, Prefab::class)) {
                return call_user_func_array([$class, 'instance'], $params);
            } else {
                $ref = new ReflectionClass($class);

                return $ref->hasMethod('__construct') && $params ?
                    $ref->newInstanceArgs($params) : $ref->newInstance();
            }
        }

        return $class;
    }

    /**
     * Log message
     * @param  string $logfile
     * @param  string $message
     * @return $this
     */
    public function log($logfile, $message)
    {
        $app = Base::instance();
        if ($logfile) {
            if (null === $this->logger) {
                $this->logger = new Log($logfile);
            }
            $this->logger->write($message);
        }

        return $this;
    }

    /**
     * Custom error handler with ability to log error and its trace
     * @param  Base   $app
     * @param  array  $params
     * @return void
     */
    public function error(Base $app, array $params)
    {
        $eol = PHP_EOL;
        $req = $app['VERB'].' '.$app['PATH'];
        $error = ($app['ERROR']?:[]) + [
            'text' => 'No-Message',
            'trace' => 'No-Trace',
            'status' => 'No-Status',
            'code' => '000',
        ];

        $this->log($app['LOG_FILE'], sprintf("[%s] %s %s",
            $req,
            $error['text'] ?: 'No-Message',
            $error['trace']
        ));

        if ($app['CLI']) {
            return;
        }

        if ($app['AJAX']) {
            echo json_decode($error);

            return;
        }

        if ($app['ERROR_TEMPLATE']) {
            echo ExtendedTemplate::instance()->render($app['ERROR_TEMPLATE']);

            return;
        }

        echo '<!DOCTYPE html>'.$eol.
            '<html lang="en">'.$eol.
            '<head>'.$eol.
                '<meta charset="utf-8">'.$eol.
                '<meta http-equiv="X-UA-Compatible" content="IE=edge">'.$eol.
                '<meta name="viewport" content="width=device-width, initial-scale=1">'.$eol.
                '<meta name="author" content="Eko Kurniawan">'.$eol.
                '<title>'.$error['code'].' '.$error['status'].'</title>'.$eol.
                '<style>'.$eol.
                'code{word-wrap:break-word;color:black}.comment,.doc_comment,.ml_comment{color:dimgray;font-style:italic}.variable{color:blueviolet}.const,.constant_encapsed_string,.class_c,.dir,.file,.func_c,.halt_compiler,.line,.method_c,.lnumber,.dnumber{color:crimson}.string,.and_equal,.boolean_and,.boolean_or,.concat_equal,.dec,.div_equal,.inc,.is_equal,.is_greater_or_equal,.is_identical,.is_not_equal,.is_not_identical,.is_smaller_or_equal,.logical_and,.logical_or,.logical_xor,.minus_equal,.mod_equal,.mul_equal,.ns_c,.ns_separator,.or_equal,.plus_equal,.sl,.sl_equal,.sr,.sr_equal,.xor_equal,.start_heredoc,.end_heredoc,.object_operator,.paamayim_nekudotayim{color:black}.abstract,.array,.array_cast,.as,.break,.case,.catch,.class,.clone,.continue,.declare,.default,.do,.echo,.else,.elseif,.empty.enddeclare,.endfor,.endforach,.endif,.endswitch,.endwhile,.eval,.exit,.extends,.final,.for,.foreach,.function,.global,.goto,.if,.implements,.include,.include_once,.instanceof,.interface,.isset,.list,.namespace,.new,.print,.private,.public,.protected,.require,.require_once,.return,.static,.switch,.throw,.try,.unset,.use,.var,.while{color:royalblue}.open_tag,.open_tag_with_echo,.close_tag{color:orange}.ini_section{color:black}.ini_key{color:royalblue}.ini_value{color:crimson}.xml_tag{color:dodgerblue}.xml_attr{color:blueviolet}.xml_data{color:red}.section{color:black}.directive{color:blue}.data{color:dimgray}'.
                '</style>'.$eol.
            '</head>'.$eol.
            '<body>'.$eol.
                '<h1>'.$error['status'].'</h1>'.$eol.
                '<p>'.$app->encode($error['text']?:$req).'</p>'.$eol.
                ($app['DEBUG']?('<pre>'.$error['trace'].'</pre>'.$eol):'').
            '</body>'.$eol.
            '</html>';
    }
}
