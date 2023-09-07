<?php

namespace App\Infrastructure\Doctrine\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

class Pi extends FunctionNode
{
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'PI()';
    }

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
