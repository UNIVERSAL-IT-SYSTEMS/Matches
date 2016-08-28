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
 * Author:  Marco Ammon (Clubfan)
 * Created: 28.08.2016
 */
Begin;

CREATE TABLE /*_*/games(
--Primary Key
m_id INT UNSIGNED NOT NULL FOREIGN KEY REFERENCES /*_*/matches (m_id) ON DELETE CASCADE ON UPDATE CASCADE,
--Page ID
page_id INT UNSIGNED NOT NULL,
--Date
m_date TIMESTAMP,
--Participant 1 flag
p1_flag VARCHAR(31),
--Participant 2 flag
p2_flag VARCHAR(31),
--Game length in seconds
g_length INT,
--Map
map VARCHAR(127).
--Finished
finished BOOLEAN default FALSE,
--Winner (either 1, 2 or d for draw)
g_winner CHAR,
--Details as JSON-Object (such as hero picks/bans)
g_details JSON
)/*wgDBTableOptions*/;

Commit;