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

use MediaWiki\Logger\LoggerFactory;

/**
 * Description of Matches
 *
 * @author Marco Ammon (Clubfan)
 */
class Matches {
	/*
	 * Switch by game
	 * @return: Returns highest matchID + 1
	 */

	public static function storeMatch($parser) {
		$matches = new Matches();
		$options = $matches->extractOptions(array_slice(func_get_args(), 1));
		if (!$matches->isParsingLatestRevision($parser)) {
			return false;
		}
		$match = array();
		$match['m_id'] = null;
		$title = $parser->getTitle();
		$pageID = $title->getArticleID();
		$match['page_id'] = $pageID;
		
		if (isset($options['date'])) {
			//try {				
				$match['m_date'] = $options['date']; //$date->format(DateTime::ATOM);
			//} catch (Exception $ex) {
			//	$match['m_date'] = null;
			//	return '<strong class="error">' . wfMessage('matches-date-invalid')->text() . '</strong>';
			//}
		} else {
			$match['m_date'] = null;
		}
		if (isset($options['player1'])) {
			//TODO: Ensure escapation before inserting into db!
			$match['participant_1'] = $options['player1'];
		} else {
			$match['participant_1'] = 'TBD';
		}
		if (isset($options['player2'])) {
			$match['participant_2'] = $options['player2'];
		} else {
			$match['participant_2'] = 'TBD';
		}
		if (isset($options['p1template'])) {
			$match['p1_template'] = $options['p1template'];
		} else {
			$match['p1_template'] = null;
		}
		if (isset($options['p2template'])) {
			$match['p2_template'] = $options['p2template'];
		} else {
			$match['p2_template'] = null;
		}
		if (isset($options['p1flag'])) {
			$match['p1_flag'] = $options['p1flag'];
		} else {
			$match['p1_flag'] = null;
		}
		if (isset($options['p2flag'])) {
			$match['p2_flag'] = $options['p2flag'];
		} else {
			$match['p2_flag'] = null;
		}
		if (isset($options['p1race'])) {
			$match['p1_race'] = $options['p1race'];
		} else {
			$match['p1_race'] = null;
		}
		if (isset($options['p2race'])) {
			$match['p2_race'] = $options['p2race'];
		} else {
			$match['p2_race'] = null;
		}		
		if (isset($options['tournament'])) {
			$match['tournament'] = $options['tournament'];
		} else {
			$match['tournament'] = null;
		}
		if (isset($options['tier'])) {
			//Maybe compare to array of allowed tiers
			$match['t_tier'] = $options['tier'];
		} else {
			$match['t_tier'] = null;
		}
		if (isset($options['tname'])) {
			$match['t_name'] = $options['tname'];
		} else {
			$match['t_name'] = null;
		}
		if (isset($options['ticon'])) {
			$match['t_icon'] = $options['ticon'];
		} else {
			$match['t_icon'] = null;
		}
		$match['finished'] = ($options['finished'] == 'true');
		if (isset($options['p1score'])) {
			if (is_numeric($options['p1score'])){
				$match['p1_score'] = $options['p1score'];
			} else {
				$match['p1_score'] = 0;
			}			
		} else {
			$match['p1_score'] = 0;
		}
		
		if (isset($options['p2score'])) {
			if (is_numeric($options['p2score'])){
				$match['p2_score'] = $options['p2score'];
			} else {
				$match['p2_score'] = 0;
			}
		} else {
			$match['p2_score'] = 0;
		}
		if (isset($options['winner'])){
			switch ($options['winner']) {
			case '1':
				$match['winner'] = 1;
				break;
			case '2':
				$match['winner'] = 2;
				break;
			case 'draw':
				$match['winner'] = 'd';
				break;
			default:
				$match['winner'] = null;
		}
		} else {
			$match['winner'] = null;
		}
		
		if (isset($options['walkover'])){
			if ($options['walkover'] == 1 OR $options['walkover'] == 2) {
			$match['walkover'] = $options['walkover'];
		} else {
			$match['walkover'] = null;
		}
		} else {
			$match['walkover'] = null;
		}
		
		if (isset($options['mode'])) {
			$match['mode'] = $options['mode'];
		} else {
			$match['mode'] = null;
		}
				//TODO: Handle details
		$details = array();
		if (isset($options['stream'])) {
			$details['stream'] = $options['stream'];
		} else {
			$details['stream'] = null;
		}
		if (isset($options['lrthread'])) {
			$details['lrthread'] = $options['lrthread'];
		}
		if (isset($options['vod'])) {
			$details['vod'] = $options['vod'];
		}
		if (isset($options['preview'])) {
			$details['preview'] = $options ['preview'];
		}
		if (isset($options['review'])) {
			$details['review'] = $options ['review'];
		}
		if (isset($options['recap'])) {
			$details['recap'] = $options ['recap'];
		}
		if (isset($options['interview'])) {
			if (isset($options['interview2'])) {
				$interviews = array();
				$interviews[0] = $options ['interview'];
				$interviews[1] = $options ['interview2'];
				$details['interview'] = $interviews;
			} else {
				$details['interview'] = $options ['interview'];
			}
		}
		global $wgScriptPath;
		$wiki = substr($wgScriptPath, 1);
		switch ($wiki) {
			case 'counterstrike':
				if (isset($options['hltv'])) {
					$details['hltv'] = $options ['hltv'];
				}
				if (isset($options['hltvlegacy'])) {
					$details['hltvlegacy'] = $options ['hltvlegacy'];
				}
				if (isset($options['stats'])) {
					$details['stats'] = $options ['stats'];
				}
				if (isset($options['cevo'])) {
					$details['cevo'] = $options ['cevo'];
				}
				if (isset($options['esl'])) {
					$details['esl'] = $options ['esl'];
				}
				if (isset($options['sltv'])) {
					$details['sltv'] = $options ['sltv'];
				}
				if (isset($options['faceit'])) {
					$details['faceit'] = $options ['faceit'];
				}
				if (isset($options['sostronk'])) {
					$details['sostronk'] = $options ['sostronk'];
				}
				break;
			case 'dota2':
				if (isset($options['dotabuff'])) {
					$details['dotabuff'] = $options ['dotabuff'];
				}
				break;
		}

		return self::storeMatchInDB($match, $details);
	}

	public static function storeGame(&$parser) {
		$options = extractOptions(array_slice(func_get_args(), 1));
		if (!isParsingLatestRevision($parser)) {
			return false;
		}
	}

	private function storeMatchInDB(array $match, array $details) {
		$mDB = new MatchesDB();
		$id = $mDB->insertmatch($match, $details);
		$mDB->close();
		return $id;
	}

	public static function deleteMatchesAndGames($pageID) {
		$logger = LoggerFactory::getInstance('matches-extension');
		$context = array();
		$context['match'] = $match;
		try {
			$dbw = wfGetDB(DB_MASTER);
			$conditions = array();
			$conditions['page_id'] = $pageID;
			$res = $dbw->delete(
					'matches', $conditions
			);
			$dbw->close();
			if ($res == true) {
				$logger->info('Matches successfully removed from DB', $context);
				return true;
			}
			return false;
		} catch (DBUnexpectedError $e) {
			$context = array();
			$context['error'] = $e;
			$context['pageID'] = $pageID;
			$logger->warning('Deletion of matches was not successfull', $context);
			return '<strong class="error">' . wfMessage('matches-could-not-be-deleted-from-db')->text() . '</strong>';
		}
	}

	/**
	 * Checks if currently parsed Revision is the latest
	 * @param Parser $parser
	 * @returns boolean
	 */
	private function isParsingLatestRevision(Parser $parser) {
		$mTitle = $parser->getTitle();
		if ($mTitle == null) {
			return false;
		}
		$parsingRevisionID = $parser->getRevisionId();
		$latestRevisionID = $mTitle->getLatestRevID();
		return $parsingRevisionID === $latestRevisionID;
	}

	/**
	 * From: https://www.mediawiki.org/wiki/Manual:Parser_functions
	 * Converts an array of values in form [0] => "name=value" into a real
	 * associative array in form [name] => value. If no = is provided,
	 * true is assumed like this: [name] => true
	 *
	 * @param array string $options
	 * @return array $results
	 */
	private function extractOptions(array $options) {
		$results = array();

		foreach ($options as $option) {
			$pair = explode('=', $option, 2);
			if (count($pair) === 2) {
				$name = trim($pair[0]);
				$value = trim($pair[1]);
				$results[$name] = $value;
			}

			if (count($pair) === 1) {
				$name = trim($pair[0]);
				$results[$name] = true;
			}
		}
		//Now you've got an array that looks like this:
		//  [foo] => "bar"
		//	[apple] => "orange"
		//	[banana] => true

		return $results;
	}

}
