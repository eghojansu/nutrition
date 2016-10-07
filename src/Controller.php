<?php

namespace Nutrition;

use Base;
use Template;
use Web;

class Controller
{
    /**
     * @var Nutrition\User
     */
    protected $user;

    /**
     * @var default template
     */
    protected $template;

    /**
     * @var template key
     */
    protected $templateKey = 'view';

    /**
     * @var boolean
     */
    protected $noTemplate = false;

    /**
     * @var array
     */
    protected $roles = [];

    /**
     * Construct controller
     */
    public function __construct()
    {
        $this->user = User::instance();
    }

    /**
     * @param  Base
     * @param  array
     */
    public function beforeroute(Base $base, array $params)
    {
        if ($this->roles) {
            $userRoles = $this->user->getRoles();
            $intersection = array_intersect($userRoles, $this->roles);
            if (empty($intersection)) {
                $this->notAllowed();
            }
        }
    }

    /**
     * @param  Base
     * @param  array
     */
    public function afterroute(Base $base, array $params)
    {
        // what should do after routing done?
    }

    /**
     * @param  string
     */
    protected function notFound($message = null)
    {
        Base::instance()->error(404, $message);
    }

    /**
     * @param  string
     */
    protected function notAllowed($message = null)
    {
        Base::instance()->error(405, $message);
    }

    /**
     * @return Object $this
     */
    protected function noTemplate()
    {
        $this->noTemplate = true;

        return $this;
    }

    /**
     * @param  string
     * @param  string
     * @param  string
     */
    protected function render($view, $template = null, $key = null)
    {
        $base = Base::instance();
        $base->set('app.user', $this->user);
        if ($this->noTemplate) {
            echo Template::instance()->render($view);
        } else {
            $template = $template ?: $this->template;
            $key = $key ?: $this->templateKey;

            $base->set($key, $view);
            echo Template::instance()->render($template);
        }
    }

    /**
     * @param  string|array|object
     */
    protected function json($data)
    {
        header('Content-type: application/json');

        echo is_string($data) ? $data : json_encode($data);
    }

    /**
     * @param  string
     * @param  boolean
     * @param  string
     * @param  string
     * @param  integer
     * @param  boolean
     */
    protected function file($file, $delete = false, $fileAs = null, $mime = null, $kbps = 0, $force = true)
    {
        $send = $file;
        if ($delete && $fileAs) {
            $send = dirname($file).'/'.$fileAs;
            rename($file, $send);
        }
        elseif ($fileAs) {
            $base = Base::instance();
            $temp = $base->get('TEMP').'files/';
            if (!is_dir($temp)) {
                mkdir($temp, Base::MODE, TRUE);
            }
            copy($file, $temp.$fileAs);
            $send = $fileAs;
        }

        Web::instance()->send($send, $mime, $kbps, $force);

        if ($fileAs) {
            unlink($send);
        }
    }

    /**
     * @param  string
     */
    protected function redirect($route)
    {
        Base::instance()->reroute($route);
    }

    /**
     * @param  string
     * @param  string
     * @return Object $this
     */
    protected function flash($name, $message)
    {
        Base::instance()->push($name, $message);

        return $this;
    }
}
