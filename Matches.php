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
		global $wgScriptPath;
		$wiki = substr($wgScriptPath, 1);
		//TODO: Add DB query for highest matchID
		$matchID = 0;
		return $matchID + 1;
	}

	public static function storeMatch(&$parser) {
		$options = extractOptions(array_slice(func_get_args(), 1));
		if (!isParsingLatestRevision($parser)) {
			return false;
		}
		$match = array();
		if (isset($options['pageid'])) {
			if ($options['pageid'] > 0) {
				$match['pageid'] = $options['pageid'];
			} else {
				return '<strong class="error">' . wfMessage('matches-pageid-must-be-a-number-greater-than-0')->text() . '</strong>';
			}
		} else {
			return '<strong class="error">' . wfMessage('matches-pageid-cannot-be-empty')->text() . '</strong>'; 
		}
		if (isset($options['matchid'])){
			if ($options['matchid'] >= 0) {
				$match['matchid'] = $options['matchid'];
			} else {
				return '<strong class="error">' . wfMessage('matches-matchid-must-be-a-positive-number')->text() . '</strong>';
			}
		} else {
			return '<strong class="error">' . wfMessage('matches-matchid-cannot-be-empty')->text() . '</strong>'; 
		}
		if (isset($options['date'])) {
			try {
				$date = new DateTime($options['date']);
				$match['date'] = $date;
			} catch (Exception $ex) {
				return '<strong class="error">' . wfMessage('matches-date-invalid')->text() . '</strong>';
			}			
		}
		if (is_string($options['player1'])){
			//TODO: Ensure escapation before inserting into db!
			$match['participation1'] = $options['player1'];
		} else {
			$match['participation1'] = 'TBD';
		}
		if (is_string($options['player2'])){
			//TODO: Ensure escapation before inserting into db!
			$match['participant2'] = $options['player2'];
		} else {
			$match['participant2'] = 'TBD';
		}
		if (is_string($options['tournament'])){
			$match['tournament'] = $options['tournament'];
		}
		if (is_string($options['tier'])){
			//Maybe compare to array of allowed tiers
			$match['tier'] = $options['tier'];
		}
		if (is_string($options['tname'])){
			$match['tname'] = $options['tname'];
		}
		if (is_string($options['ticon'])){
			$match['ticon'] = $options['ticon'];
		}
		$match['finished'] = ($options['finished'] == 'true');
		if (is_numeric($options['p1score'])){
			$match['p1score'] = $options['p1score'];
		}
		if (is_numeric($options['p2score'])){
			$match['p2score'] = $options['p2score'];
		}
		switch($options['winner']){
			case '1':
				$match['winner'] = 1;
				break;
			case '2':
				$match['winner'] = 2;
				break;
			case 'draw':
				$match['winner'] = 'd';
				break;
		}
		if($options['walkover'] == 1 OR $options['walkover'] == 2){
			$match['walkover'] = $options['walkover'];
		}
		if (is_string($options['mode'])){
			$match['mode'] = $options['mode'];
		}
		//TODO: Pass $match to DB function
	}

	public static function storeGame(&$parser) {
		$options = extractOptions(array_slice(func_get_args(), 1));
		if (!isParsingLatestRevision($parser)) {
			return false;
		}
	}

	public static function deleteMatchesAndGames($pageID) {
		
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
