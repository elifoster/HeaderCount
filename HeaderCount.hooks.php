<?php

use MediaWiki\Hook\ParserFirstCallInitHook;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\MediaWikiServices;
use MediaWiki\Revision\SlotRecord;

class HeaderCountHooks implements ParserFirstCallInitHook {
	public function onParserFirstCallInit($parser) {
        $parser->setFunctionHook('headcount', 'HeaderCountHooks::renderHeadCount');
    }

    public static function renderHeadCount(Parser $parser, $page = '', $level = '') {
        $title = empty($page) ? $parser->getPage()->getText() : $page;
        $page = MediaWikiServices::getInstance()->getPageStore()->getExistingPageByText($title);

        if ($page === null) {
            return "'''$title does not exist.'''";
        }

        $rev = MediaWikiServices::getInstance()->getRevisionStore()->getRevisionByTitle($page);

        if ($rev === null) {
            return "'''Could not retrieve revision from $title.'''";
        }

        $content = $rev->getContent(SlotRecord::MAIN);
        if ($content === null) {
            return "'''Could not extract text from $title.'''";
        }

        $level = empty($level) ? 2 : intval($level);
        $header = str_repeat('=', $level);
        $serialized = $content->serialize();
        $count = preg_match_all("/^$header" . "[^=]+" . "$header$/m", $serialized);

        return $count == false ? 0 : $count;
    }
}
