<?php

namespace Sema\Bundle\MinifierBundle\Twig\Node;

class MinifyHTMLNode extends \Twig_Node
{
    public function __construct(\Twig_NodeInterface $body, $lineno, $tag = 'minifyhtml')
    {
        parent::__construct(array('body' => $body), array(), $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param Twig_Compiler A Twig_Compiler instance
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write("ob_start();\n")
            ->subcompile($this->getNode('body'))
            ->write("echo Minify_HTML::minify(ob_get_clean(),array('cssMinifier' => array('Minify_CSS', 'minify'),'jsMinifier' => array('JSMinPlus', 'minify'), 'xhtml' => false));\n")
        ;
    }
}
