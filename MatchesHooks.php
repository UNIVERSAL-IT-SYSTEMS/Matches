<?php

/* 
 * Copyright (C) 2016 Marco Ammon (Clubfan)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class MatchesHooks {
	
	/*
	/ Registering render callbacks with the parser
	*/
	public static function onParserFirstCallInit ( &$parser ) {
		$parser->setFunctionHook( 'storematch', array('Matches', 'storeMatch' ));
		$parser->setFunctionHook( 'storegame', array('Matches', 'storeGame' ));
		$mDB = MatchesDB::getInstance();
		$GLOBALS['matchesFirstParsingRun'] = true;
		return true;
	}
	
	/*
	 * Removes matches associated with pageId of deleted page
	 */
	public static function onArticleDeleteComplete( WikiPage &$article,
			User &$user, $reason, $id, Content $content = null, LogEntry $logEntry ) { 
		//pageid should NEVER be 0 or negative
		if ($id < 1) {
			return false;
		}
		return Matches::deleteMatchesAndGames($id);
	}
	
	/*
	 * Removes matches associated with old pageId before the move
	 * TODO: Chech if pages get purged after moving
	 */
	public static function onTitleMoveComplete( Title &$title, Title &$newtitle,
			User &$user, $oldid, $newid, $reason, Revision $revision ) {
		return Matches::deleteMatchesAndGames($oldid);		
	}
	
	/*
	 * Updates database tables after upgrading. If not installed before, tables are created
	 */
	public static function onLoadExtensionSchemaUpdates(DatabaseUpdater $updater ) {
		global $wgScriptPath;
		$wiki = substr($wgScriptPath, 1 );
		$updater->addExtensionTable('matches', __DIR__ . '/sql/matches.sql');
		$updater->addExtensionTable('games', __DIR__ . '/sql/games.sql');
		return true;
	}
	public static function onPageContentSaveComplete($article, $user, $content, $summary, $isMinor,
		$isWatch, $section, $flags, $status){
		$pageID = $article->getId();
		MatchesDB::$store = true;
		Matches::deleteMatchesAndGames($pageID);		
		$pageContents = $content->getNativeData();
		global $wgParser;
		// Special handling for the Approved Revs extension.
		$pageText = null;
		$approvedText = null;
		if ( class_exists( 'ApprovedRevs' ) ) {
			$approvedText = ApprovedRevs::getApprovedContent( $title );
		}
		if ( $approvedText != null ) {
			$pageText = $approvedText;
		} else {
			$pageText = $pageContents;
		}
		$wgParser->parse( $pageText, $article->getTitle(), new ParserOptions() );
	}
	public static function onArticlePurge(&$article) {
		$pageID = $article->getId();
		echo $pageID;
		return Matches::deleteMatchesAndGames($pageID);
	}
}
?>