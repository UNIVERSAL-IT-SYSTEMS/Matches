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

	public function __construct() {
		global $wgDBserver, $wgDBuser, $wgDBpassword, $wgDBname;
		$this->db = new mysqli($wgDBserver, $wgDBuser, $wgDBpassword, $wgDBname);
		if ($this->db->connect_errno) {
			echo "Could not connect to" . $wgDBserver . "/" . $wgDBname . ": " . $this->db->connect_errno . "-" . $this->db->connect_error;
		}
	}

	public function insertMatch(array $match, array $details) {
		if ($this->db == null) {
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
			$sql .= '\''.$value.'\'';
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
		return $this->db->insert_id;
	}
	
	public function close() {
		if ($this->db==null){
			return false;
		}
		return $this->db->close();
	}
}
