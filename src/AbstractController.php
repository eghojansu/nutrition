<?php

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
        if (!$app->exists('SESSION.history')) {
            $app->set('SESSION.history', []);
        }
        $current = Nutrition::currentUrl();
        $history = $app->get('SESSION.history');
        $this->previousPage = end($history);
        if ($current !== $this->previousPage) {
            $app->push('SESSION.history', $current);
        }
        $this->user = $app->get('app.user');
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
        return 1*Base::instance()->get('GET.page');
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
    protected function errorInternalServer($message = '')
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
     * Get homepage
     * @return string
     */
    protected function getHomepage()
    {
        return Base::instance()->get('app.homepage')?:Nutrition::baseUrl();
    }

    /**
     * Go to homepage
     */
    protected function goHome()
    {
        $this->redirectTo($this->getHomepage());
    }

    /**
     * Go back, beware get endless redirecting
     */
    protected function goBack()
    {
        if ($this->previousPage) {
            Base::instance()->pop('SESSION.history');
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
     * @param array $data
     */
    protected function JSONResponse(array $data)
    {
        Nutrition::jsonOut($data);
    }

    /**
     * Set/get flash session
     * @param  string $var
     * @param  mixed $val
     * @return mixed
     */
    protected function flash($var, $val = null)
    {
        return Nutrition::flash($var, $val);
    }
}