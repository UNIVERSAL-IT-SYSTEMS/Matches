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

	public static function getMatchID($parser) {
		//TODO: Add DB query for highest matchID
		$dbRead = wfGetDB(DB_MASTER);
		$res = $dbRead->select(
				'matches', 'm_id', '
');
		$matchID = $d;
		return $matchID + 1;
	}

	public static function storeMatch(&$parser) {
		$options = extractOptions(array_slice(func_get_args(), 1));
		if (!isParsingLatestRevision($parser)) {
			return false;
		}
		$match = array();
		$match['m_id'] = null;
		if (is_numeric($options['pageid'])) {
			if ($options['pageid'] > 0) {
				$match['page_id'] = $options['pageid'];
			} else {
				return '<strong class="error">' . wfMessage('matches-pageid-must-be-a-number-greater-than-0')->text() . '</strong>';
			}
		} else {
			return '<strong class="error">' . wfMessage('matches-pageid-cannot-be-empty')->text() . '</strong>';
		}
		if (isset($options['date'])) {
			try {
				$date = new DateTime($options['date']);
				$match['m_date'] = $date;
			} catch (Exception $ex) {
				$match['m_date'] = null;
				return '<strong class="error">' . wfMessage('matches-date-invalid')->text() . '</strong>';
			}
		} else {
			$match['m_date'] = null;
		}
		if (is_string($options['player1'])) {
			//TODO: Ensure escapation before inserting into db!
			$match['participant_1'] = $options['player1'];
		} else {
			$match['participant_1'] = 'TBD';
		}
		if (is_string($options['player2'])) {
			$match['participant_2'] = $options['player2'];
		} else {
			$match['participant_2'] = 'TBD';
		}
		if (is_string($options['p1flag'])) {
			$match['p1_flag'] = $options['p1flag'];
		} else {
			$match['p1_flag'] = null;
		}
		if (is_string($options['p2flag'])) {
			$match['p2_flag'] = $options['p2flag'];
		} else {
			$match['p2_flag'] = null;
		}
		if (is_string($options['p1race'])) {
			$match['p1_race'] = $options['p1race'];
		} else {
			$match['p1_race'] = null;
		}
		if (is_string($options['p2race'])) {
			$match['p2_race'] = $options['p2race'];
		} else {
			$match['p2_race'] = null;
		}
		if (is_string($options['p1template'])) {
			$match['p1_template'] = $options['p1template'];
		} else {
			$match['p1_template'] = null;
		}
		if (is_string($options['p2template'])) {
			$match['p2_template'] = $options['p2template'];
		} else {
			$match['p2_template'] = null;
		}
		if (is_string($options['tournament'])) {
			$match['tournament'] = $options['tournament'];
		} else {
			$match['tournament'] = null;
		}
		if (is_string($options['tier'])) {
			//Maybe compare to array of allowed tiers
			$match['t_tier'] = $options['tier'];
		} else {
			$match['t_tier'] = null;
		}
		if (is_string($options['tname'])) {
			$match['t_name'] = $options['tname'];
		} else {
			$match['t_name'] = null;
		}
		if (is_string($options['ticon'])) {
			$match['t_icon'] = $options['ticon'];
		} else {
			$match['t_icon'] = null;
		}
		$match['finished'] = ($options['finished'] == 'true');
		if (is_numeric($options['p1score'])) {
			$match['p1_score'] = $options['p1score'];
		} else {
			$match['p1_score'] = 0;
		}
		if (is_numeric($options['p2score'])) {
			$match['p2_score'] = $options['p2score'];
		} else {
			$match['p2_score'] = 0;
		}
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
		if ($options['walkover'] == 1 OR $options['walkover'] == 2) {
			$match['walkover'] = $options['walkover'];
		} else {
			$match['walkover'] = null;
		}
		if (is_string($options['mode'])) {
			$match['mode'] = $options['mode'];
		} else {
			$match['mode'] = null;
		}
		if (is_string($options['stream'])) {
			$match['stream'] = $options['stream'];
		} else {
			$match['stream'] = null;
		}
		//TODO: Handle details
		$details = array();
		if (is_string($options['lrthread'])) {
			$details['lrthread'] = $options['lrthread'];
		}
		if (is_string($options['vod'])) {
			$details['vod'] = $options['vod'];
		}
		if (is_string($options['preview'])) {
			$details['preview'] = $options ['preview'];
		}
		if (is_string($options['review'])) {
			$details['review'] = $options ['review'];
		}
		if (is_string($options['recap'])) {
			$details['recap'] = $options ['recap'];
		}
		if (is_string($options['interview'])) {
			if (is_string($options['interview2'])) {
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
				if (is_string($options['hltv'])) {
					$details['hltv'] = $options ['hltv'];
				}
				if (is_string($options['hltvlegacy'])) {
					$details['hltvlegacy'] = $options ['hltvlegacy'];
				}
				if (is_string($options['stats'])) {
					$details['stats'] = $options ['stats'];
				}
				if (is_string($options['cevo'])) {
					$details['cevo'] = $options ['cevo'];
				}
				if (is_string($options['esl'])) {
					$details['esl'] = $options ['esl'];
				}
				if (is_string($options['sltv'])) {
					$details['sltv'] = $options ['sltv'];
				}
				if (is_string($options['faceit'])) {
					$details['faceit'] = $options ['faceit'];
				}
				if (is_string($options['sostronk'])) {
					$details['sostronk'] = $options ['sostronk'];
				}
				break;
			case 'dota2':
				if (is_string($options['dotabuff'])) {
					$details['dotabuff'] = $options ['dotabuff'];
				}
				break;
		}

		return storeMatchInDB($match, $details);
	}

	public static function storeGame(&$parser) {
		$options = extractOptions(array_slice(func_get_args(), 1));
		if (!isParsingLatestRevision($parser)) {
			return false;
		}
	}

	private function storeMatchInDB(array $match, array $details) {
		$logger = LoggerFactory::getInstance('matches-extension');
		$context = array();
		$context['match'] = $match;
		$keys = array_keys($match);
		global $wgDBPrefix;
		$sql = 'INSERT INTO '.$wgDBPrefix.'matches (';
		$first = true;
		foreach($keys as $tableHead) {
			if (!$first) {
				$sql .= ', ';
			} else {
				$first = false;
			}
			$sql .= $tableHead;
		}
		$sql .= ') VALUES (';
		$first = true;
		foreach($match as $value) {
			if (!$first) {
				$sql .= ', ';
			} else {
				$first = false;
			}
			$value = mysqli_real_escape_string($value);
			$sql .= $value;
		}
		$sql .= ', COLUMN_CREATE(';
		$first = true;
		foreach ($details as $key => $value){
			if (!$first) {
				$sql .= ', ';
			} else {
				$first = false;
			}
			$sql .= '\''.$key.'\',\''.mysqli_real_escape_string($value).'\'';
		}
		$sql .= ' ));';
		try {
			$dbw = wfGetDB(DB_MASTER);
			$res = $dbw->query(	$sql);
			$matchId = $dbw->inserId();
			$dbw->close();
			if ($res == true) {
				$logger->info('Match successfully stored in DB', $context);
				return $matchId;
			}
			return false;
		} catch (DBQueryError $e) {
			$context = array();
			$context['error'] = $e;
			$context['match'] = $match;
			$logger->warning('Insertion of match failed', $context);
			return '<strong class="error">' . wfMessage('match-could-not-be-stored-in-db')->text() . '</strong>';
		}
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
					'matches',
					$conditions
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
	function isParsingLatestRevision(Parser $parser) {
		$mTitle = $parser->getTitle();
		if ($mTitle == null) {
			return false;
		}
		$parsingRevisionID = $parser->getRevisionId();
		$latestRevisionID = $mTitle->getLatestRevisionId();
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
	function extractOptions(array $options) {
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
