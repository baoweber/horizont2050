<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 */

namespace App;

use Latte,
    Latte\CompileException,
    Latte\MacroNode,
    Latte\PhpWriter,
    Latte\Macros\MacroSet;


/**
 * Basic macros for Latte.
 *
 * @author     David Grudl
 */
class ThumbMacro extends MacroSet
{

    public static function register(Latte\Compiler $compiler)
    {
        $me = new static($compiler);

        $me->addMacro('aaa', array($me, 'macroAaa'), array($me, 'macroEndAaa'));
    }


    /**
     * Finishes template parsing.
     * @return array(prolog, epilog)
     */
    public function finalize()
    {
        return array('list($_b, $_g, $_l) = $template->initialize('
            . var_export($this->getCompiler()->getTemplateId(), TRUE) . ', '
            . var_export($this->getCompiler()->getContentType(), TRUE)
            . ')');
    }


    /********************* macros ****************d*g**/




    /**
     * {dump ...}

    public function macroDump(MacroNode $node, PhpWriter $writer)
    {
        $args = $writer->formatArgs();
        return 'Tracy\Debugger::barDump(' . ($node->args ? $writer->write("array(%var => $args)", $args) : 'get_defined_vars()')
        . ', "Template " . str_replace(dirname(dirname($template->getName())), "\xE2\x80\xA6", $template->getName()))';
    }
     */

    public function macroAaa(MacroNode $node, PhpWriter $writer)
    {
        return 'aaa';
    }

    public function macroEndAaa(MacroNode $node, PhpWriter $writer)
    {
        return 'Endaaa';
    }

}
