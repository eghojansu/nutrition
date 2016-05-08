<?php

/**
 * This file is part of eghojansu/nutrition
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

namespace Nutrition;

use Base;
use Nutrition;
use Template;

abstract class AbstractController
{
    protected $template;
    protected $templateKey = 'content';
    protected $previousPage;
    protected $user;

    public function beforeroute($app, $params)
    {
        $this->template = $this->template?:$app->get('MAIN_TEMPLATE');
        $this->user = $app->get('app.user');
        if (!$app->exists('SESSION.history')) {
            $app->set('SESSION.history', []);
        }
        if ('GET' === $app->get('VERB')) {
            $current = Nutrition::currentUrl();
            $history = $app->get('SESSION.history');
            $this->previousPage = end($history);
            if ($current !== $this->previousPage) {
                $app->push('SESSION.history', $current);
            }
        }
    }

    /**
     * Check wether current user is guest or redirect to homepage
     */
    protected function guestOnly()
    {
        $this->user->isGuest() || $this->goHome();
    }

    /**
     * Check wether current user is user or redirect to homepage
     */
    protected function userOnly()
    {
        $this->user->wasLogged() || $this->goHome();
    }

    /**
     * Get page number
     * @return int
     */
    protected function getPageNumber()
    {
        return 1*Base::instance()->get('GET.pos');
    }

    /**
     * Get page limit
     * @return int
     */
    protected function getPageLimit()
    {
        return 10;
    }

    /**
     * Send error page
     * @param  int $code
     * @param  string $message
     */
    protected function error($code, $message = '')
    {
        Base::instance()->error($code, $message);
    }

    /**
     * Send error not found page
     * @param  string $message
     */
    protected function errorNotFound($message = '')
    {
        $this->error(404, $message);
    }

    /**
     * Send forbidden error page
     * @param  string $message
     */
    protected function errorForbidden($message = '')
    {
        $this->error(403, $message);
    }

    /**
     * Send internal server error page
     * @param  string $message
     */
    protected function errorServer($message = '')
    {
        $this->error(500, $message);
    }

    /**
     * Redirect to $destination
     * @param  string $destination route or absoulte url
     */
    protected function redirectTo($destination)
    {
        Base::instance()->reroute($destination);
    }

    /**
     * Redirect to $route
     * @param  string $route route
     */
    protected function redirectToRoute($route)
    {
        Base::instance()->reroute('@'.$route);
    }

    /**
     * Get homepage
     * @return string
     */
    protected function getHomepage()
    {
        return Base::instance()->get('app.homepage');
    }

    /**
     * Go to homepage
     */
    protected function goHome()
    {
        $this->redirectTo($this->getHomepage());
    }

    /**
     * Go back to previous page, beware get endless redirecting
     */
    protected function goBack()
    {
        $app = Base::instance();
        if ($this->previousPage) {
            $app->pop('SESSION.history');
            $this->redirectTo($this->previousPage);
        }

        $this->goHome();
    }

    /**
     * Render template view
     * @param  string $view
     * @param  bool $noTemplate
     * @return null
     */
    protected function render($view, $noTemplate = false)
    {
        $app = Base::instance();
        if ($noTemplate || !$this->template) {
            echo Template::instance()->render($view);
        } else {
            $app->set($this->templateKey, $view);
            echo Template::instance()->render($this->template);
        }
    }

    /**
     * Send JSON Response
     * @see Nutrition::jsonOut
     */
    protected function JSONResponse()
    {
        call_user_func_array(['Nutrition', 'jsonOut'], func_get_args());
    }

    /**
     * Set/get flash session
     * @see Nutrition::flash
     */
    protected function flash()
    {
        return call_user_func_array(['Nutrition', 'flash'], func_get_args());
    }

    /**
     * Is request post
     * @return boolean
     */
    protected function isPost()
    {
        return strtolower(Base::instance()->get('VERB'))==='post';
    }

    /**
     * Is request ajax
     * @return boolean
     */
    protected function isAjax()
    {
        return Base::instance()->get('AJAX');
    }
}