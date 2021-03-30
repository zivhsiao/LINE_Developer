<?php
// libraries/Twig.php
 
class Twig
{
    private $ci;
    private $twig;
 
    public function __construct()
    {
        $this->ci =& get_instance();
 
        $loader = new Twig_Loader_Filesystem(VIEWPATH);
        $this->twig = new Twig_Environment($loader);
 

        $this->twig->addGlobal('baseUrl', base_url());
        $this->twig->addFunction(new Twig\TwigFunction('setValue', 'set_value'));
        $this->twig->addFunction(new Twig\TwigFunction('formError', 'form_error'));
    }
 
    public function output($template, $data=[])
    {
        $this->ci->output->set_output(
            $this->twig->render($template . '.html', $data)
        );
    }
}