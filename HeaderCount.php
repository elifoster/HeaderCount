<?php
/**
 * HeaderCount
 *
 * @file
 * @ingroup Extensions
 * @version 1.0.0
 * @author Eli Foster <elifosterwy@gmail.com>
 */

$wgExtensionCredits['parserhooks'][] = array(
    'path' => __FILE__,
    'name' => 'HeaderCount',
    'descriptionmsg' => 'headercount-desc',
    'author' => 'Eli Foster',
    'url' => 'TODO'
);

$wgAutoloadClasses['HeaderCountHooks'] = __DIR__ . '/HeaderCount.hooks.php';

$wgExtensionMessagesFiles['HeaderCount'] = __DIR__ . '/HeaderCount.i18n.php';
$wgExtensionMessagesFiles['HeaderCountMagic'] = __DIR__ . '/HeaderCount.i18n.magic.php';

$wgHooks['ParserFirstCallInit'][] = 'HeaderCountHooks::setupParser';
