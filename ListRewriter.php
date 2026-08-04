<?php

namespace dokuwiki\plugin\creole;

use dokuwiki\Parsing\Handler\Lists;

/**
 * CallWriter rewriter for Creole style ordered and unordered lists.
 *
 * Creole counts repeated marker characters (`**` = depth 2). Only when there is
 * a single marker character do we fall back to DokuWiki's rule of counting
 * two-space indents. The base class normalises the returned depth to a 1-based
 * level itself, so the raw depth is returned here.
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  Esther Brunner <wikidesign@gmail.com>
 */
class ListRewriter extends Lists
{
    /**
     * Determine list depth and type from a matched list marker.
     *
     * @param string $match matched list marker including leading whitespace
     * @return array{depth: int, type: string} raw depth and 'u' or 'o' for the list type
     */
    protected function interpretSyntax(string $match): array
    {
        $type = str_ends_with($match, '*') ? 'u' : 'o';
        $depth = strlen(trim($match));
        if ($depth <= 1) {
            $c = count(explode('  ', str_replace("\t", '  ', $match)));
            if ($c > $depth) $depth = $c;
        }
        return ['depth' => $depth, 'type' => $type];
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
