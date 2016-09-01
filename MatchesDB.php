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
 * Description of MatchesDB
 *
 * @author Marco Ammon (Clubfan)
 */
class MatchesDB {

	private $db = null;
	protected static $_instance = null;
	private $isFirstParsingRun = true;
	public static $store = false;
	public static function getInstance() {
		if (null === self::$_instance) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	protected function __clone() {
		
	}

	protected function __construct() {
		
	}
	public function isFirstParsingRun(){
		return $this->isFirstParsingRun;
	}
	public function setFirstParsingRun($firstRun){
		$this->isFirstParsingRun = $firstRun;
	}
	public function connect() {
		if (!self::$store){
			return true;
		}
		global $wgDBserver, $wgDBuser, $wgDBpassword, $wgDBname;
		$this->db = new mysqli($wgDBserver, $wgDBuser, $wgDBpassword, $wgDBname);
		if ($this->db->connect_errno) {
			echo "Could not connect to" . $wgDBserver . "/" . $wgDBname . ": " . $this->db->connect_errno . "-" . $this->db->connect_error;
		}
	}

	public function insertMatch(array $match, array $details) {
		if (!self::$store){
			return;
		}
		if ($this->db == null) {
			return false;
		}
		if ($match['page_id'] == 0) {
			return false;
		}
		global $wgDBPrefix;
		$sql = 'INSERT INTO ' . $wgDBPrefix . 'matches (';
		$keys = array_keys($match);
		$first = true;
		foreach ($keys as $tableHead) {
			if (!$first) {
				$sql .= ', ';
			} else {
				$first = false;
			}
			$sql .= $tableHead;
		}
		$sql .= ', details) VALUES (';
		$first = true;
		foreach ($match as $value) {
			if (!$first) {
				$sql .= ', ';
			} else {
				$first = false;
			}
			$value = $this->db->real_escape_string($value);
			$sql .= '\'' . $value . '\'';
		}
		if (!empty($details)) {
			$sql .= ', COLUMN_CREATE(';
			$first = true;
			foreach ($details as $key => $value) {
				if (!$first) {
					$sql .= ', ';
				} else {
					$first = false;
				}
				$sql .= '\'' . $key . '\',\'' . $this->db->real_escape_string($value) . '\'';
			}
			$sql .= ' )';
		}

		$sql .= ');';
		//echo $sql;
		if (!$this->db->query($sql)) {
			echo "Insertion of match failed! " . $this->db->errno . " - " . $this->db->error;
		}
		echo $this->db->insert_id;
		return $this->db->insert_id;
	}
	
	public function insertGame(array $game, array $details) {
//		if ($this->db == null) {
//			return false;
//		}
//		if ($game['page_id'] == 0) {
//			return false;
//		}
//		if ($GLOBALS['matchesFirstParsingRun'] == false) {
//			return false;
//		}
		if (!self::$store){
			return;
		}
		global $wgDBPrefix;
		$sql = 'INSERT INTO ' . $wgDBPrefix . 'games (';
		$keys = array_keys($game);
		$first = true;
		foreach ($keys as $tableHead) {
			if (!$first) {
				$sql .= ', ';
			} else {
				$first = false;
			}
			$sql .= $tableHead;
		}
		$sql .= ', g_details) VALUES (';
		$first = true;
		foreach ($game as $value) {
			if (!$first) {
				$sql .= ', ';
			} else {
				$first = false;
			}
			$value = $this->db->real_escape_string($value);
			$sql .= '\'' . $value . '\'';
		}
		if (!empty($details)) {
			$sql .= ', COLUMN_CREATE(';
			$first = true;
			foreach ($details as $key => $value) {
				if (!$first) {
					$sql .= ', ';
				} else {
					$first = false;
				}
				$sql .= '\'' . $key . '\',\'' . $this->db->real_escape_string($value) . '\'';
			}
			$sql .= ' )';
		}

		$sql .= ');';
		//echo $sql;
		if (!$this->db->query($sql)) {
			echo "Insertion of game failed! " . $this->db->errno . " - " . $this->db->error;
		}
		return true;
	}
	
	public function deleteMatches($pageID) {
		if ($this->db == null) {
			return false;
		}
		global $wgDBPrefix;
		$sql = 'DELETE FROM ' . $wgDBPrefix . 'matches WHERE page_id = ' . $pageID . ';';
		if (!$this->db->query($sql)) {
			echo "Deletion of matches failed! " . $this->db->errno . " - " . $this->db->error;
		}
		return $this->db->affected_rows;
	}
	public function deleteGames($pageID) {
		if ($this->db == null) {
			return false;
		}
		global $wgDBPrefix;
		$sql = 'DELETE FROM ' . $wgDBPrefix . 'games WHERE page_id = ' . $pageID . ';';
		if (!$this->db->query($sql)) {
			echo "Deletion of games failed! " . $this->db->errno . " - " . $this->db->error;
		}
		return $this->db->affected_rows;
	}

	public function close() {
		if ($this->db == null) {
			return false;
		}
		return $this->db->close();
	}

}
