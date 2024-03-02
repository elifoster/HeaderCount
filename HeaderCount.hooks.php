<?php

use MediaWiki\Hook\ParserFirstCallInitHook;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\MediaWikiServices;
use MediaWiki\Revision\SlotRecord;

class HeaderCountHooks implements ParserFirstCallInitHook {
    /**
     * Set as the render reason for the parser to skip #headcount processing. This is necessary to prevent infinite loops
     * caused by the parsing done by the #headcount function when passed a page name that also uses the #headcount function.
     * @var string
     */
    const RENDER_REASON = 'HeaderCount parser';
    
	public function onParserFirstCallInit($parser) {
        $parser->setFunctionHook('headcount', 'HeaderCountHooks::renderHeadCount');
    }

    public static function renderHeadCount(Parser $parser, $page = '', $level = '') {
        if ($parser->getOptions()->getRenderReason() == self::RENDER_REASON) {
            return 'Fake parse by HeaderCount extension. Skipping processing.';
        }
        
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
        
        $parserOpts = ParserOptions::newFromUser($parser->getUserIdentity());
        $parserOpts->setRenderReason(self::RENDER_REASON);
        $parserOutput = MediaWikiServices::getInstance()->getContentRenderer()->getParserOutput($content, $page, null, $parserOpts);
        
        $level = empty($level) ? 2 : intval($level);
        $count = 0;
        foreach ($parserOutput->getTOCData()->getSections() as $section) {
            if ($section->hLevel == $level) {
                $count++;
            }
        }
        return $count;
    }
}
