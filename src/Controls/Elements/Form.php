<?php namespace inkvizytor\FluentForm\Controls\Elements;

use inkvizytor\FluentForm\Base\Control;
use inkvizytor\FluentForm\Traits\CssContract;
use inkvizytor\FluentForm\Traits\DataContract;

/**
 * Class Form
 *
 * @package inkvizytor\FluentForm
 */
class Form extends Control
{
    use CssContract, DataContract;

    /** @var array */
    protected $guarded = ['mode', 'url', 'route', 'action', 'files'];

    /** @var string */
    protected $mode;
    
    /** @var string */
    protected $method = 'POST';
    
    /** @var array */
    protected $url;

    /** @var array */
    protected $route;
    
    /** @var array */
    protected $action;
    
    /** @var bool */
    protected $files = false;

    /**
     * @return $this
     */
    public function open()
    {
        $this->mode = 'form:open';

        return $this;
    }

    /**
     * @return $this
     */
    public function close()
    {
        $this->mode = 'form:close';

        return $this;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }
    
    /**
     * @param string|array $url
     * @return $this
     */
    public function url($url)
    {
        $this->url = is_array($url) ? $url : [$url];

        return $this;
    }

    /**
     * @param string|array $route
     * @return $this
     */
    public function route($route)
    {
        $this->route = is_array($route) ? $route : [$route];
        
        return $this;
    }
    
    /**
     * @param string|array $action
     * @return $this
     */
    public function action($action)
    {
        $this->action = is_array($action) ? $action : [$action];
        
        return $this;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function method($method)
    {
        $this->method = $method;
        
        return $this;
    }

    /**
     * @param bool $enabled
     * @return $this
     */
    public function files($enabled)
    {
        $this->files = $enabled;

        return $this;
    }

    /**
     * @param \Illuminate\Support\MessageBag|\Illuminate\Support\ViewErrorBag $errors
     * @return $this
     */
    public function errors($errors)
    {
        $this->renderer()->errors($errors);

        return $this;
    }

    /**
     * @param array $rules
     * @return $this
     */
    public function rules(array $rules)
    {
        $this->renderer()->rules($rules);

        return $this;
    }

    /**
     * Set size of the controls in horizontal form
     * 
     * @param int $lg
     * @param int $md
     * @param int $sm
     * @param int $xs
     * @return $this
     */
    public function fieldSize($lg = null, $md = null, $sm = null, $xs = null)
    {
        $this->renderer()->setFieldSize($lg, $md, $sm, $xs);

        return $this;
    }
    
    /**
     * Set size of the label in horizontal form
     * 
     * @param int $lg
     * @param int $md
     * @param int $sm
     * @param int $xs
     * @return $this
     */
    public function labelSize($lg = null, $md = null, $sm = null, $xs = null)
    {
        $this->renderer()->setLabelSize($lg, $md, $sm, $xs);

        return $this;
    }

    /**
     * @param int $formSize
     * @param int $labelSize
     * @return $this
     */
    public function large($formSize, $labelSize)
    {
        $this->renderer()->large($formSize, $labelSize);

        return $this;
    }

    /**
     * @param int $formSize
     * @param int $labelSize
     * @return $this
     */
    public function medium($formSize, $labelSize)
    {
        $this->renderer()->medium($formSize, $labelSize);

        return $this;
    }

    /**
     * @param int $formSize
     * @param int $labelSize
     * @return $this
     */
    public function small($formSize, $labelSize)
    {
        $this->renderer()->small($formSize, $labelSize);

        return $this;
    }

    /**
     * @param int $formSize
     * @param int $labelSize
     * @return $this
     */
    public function tiny($formSize, $labelSize)
    {
        $this->renderer()->tiny($formSize, $labelSize);

        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        if ($this->getMode() == 'form:open')
        {
            $options = $this->getOptions();
            $method = strtoupper(array_get($options, 'method', 'POST'));
            
            $options['method'] = $method != 'GET' ? 'POST' : $method;
            $options['action'] = $this->getFormAction();
            $options['accept-charset'] = 'UTF-8';
            
            if (isset($options['files']) && $options['files'] !== null)
            {
                $options['enctype'] = 'multipart/form-data';
            }

            $html = $this->html()->tag('form', $options);

            if (in_array($method, ['DELETE', 'PATCH', 'PUT']))
            {
                $html .= $this->html()->tag('input', [
                    'type' => 'hidden', 
                    'name' => '_method', 
                    'value' => $method
                ]);
            }

            if ($method != 'GET')
            {
                $html .= $this->html()->tag('input', [
                    'type' => 'hidden', 
                    'name' => '_token', 
                    'value' => $this->session()->token()
                ]);
            }
            
            return $html;
        }

        if ($this->getMode() == 'form:close')
        {
            $this->binder()->model(null);
            
            return $this->html()->close('form');
        }
        
        return '';
    }

    /**
     * @return string
     */
    private function getFormAction()
    {
        if (!empty($this->url))
        {
            return $this->locator()->to(head($this->url), array_slice($this->url, 1));
        }
        if (!empty($this->route))
        {
            return $this->locator()->route(head($this->route), array_slice($this->route, 1));
        }
        if (!empty($this->action))
        {
            return $this->locator()->action(head($this->url), array_slice($this->url, 1));
        }

        return $this->locator()->current();
    }
}