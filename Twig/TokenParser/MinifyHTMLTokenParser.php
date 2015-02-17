<?php

namespace Sema\Bundle\MinifierBundle\Twig\TokenParser;

use Sema\Bundle\MinifierBundle\Twig\Node\MinifyHTMLNode;

class MinifyHTMLTokenParser extends \Twig_TokenParser
{
    /**
     * Parses a token and returns a node.
     *
     * @param Twig_Token $token A Twig_Token instance
     *
     * @return Twig_NodeInterface A Twig_NodeInterface instance
     */
    public function parse(\Twig_Token $token)
    {
        $lineno = $token->getLine();

        $this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideSpacelessEnd'), true);
        $this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);

        return new MinifyHTMLNode($body, $lineno, $this->getTag());
    }

    public function decideSpacelessEnd(\Twig_Token $token)
    {
        return $token->test('endminifyhtml');
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'minifyhtml';
    }
}
