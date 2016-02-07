<?php

class HeaderCountHooks {
    function setupParser(Parser &$parser) {
        $parser->setFunctionHook('headcount', 'HeaderCountHooks::renderHeadCount');
    }

    function renderHeadCount(Parser &$parser, $page = '', $level = '') {
        if (empty($page)) {
            $title = $parser->getTitle();
        } else {
            $title = Title::newFromText($page);
            if (!$title->exists()) {
                return "'''$title does not exist!'''";
            }
        }
        $rev = Revision::newFromTitle($title);
        $content = $rev->getContent(Revision::RAW);
        if ($content == null) {
            return "'''Could not extract text from $title.'''";
        }
        $level = empty($level) ? 2 : intval($level);
        $header = str_repeat('=', $level);
        $serialized = $content->serialize();
        $count = preg_match_all("/^$header" . "[^=]+" . "$header$/m", $serialized);
        return $count == false ? 0 : $count;
    }
}