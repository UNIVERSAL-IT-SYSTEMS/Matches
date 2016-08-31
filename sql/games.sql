
Begin;

CREATE TABLE IF NOT EXISTS /*_*/games (
--Foreign Key
m_id INT UNSIGNED NOT NULL,
--Page ID
page_id INT UNSIGNED NOT NULL,
--Date
m_date TIMESTAMP,
--Participant 1 flag
p1_flag VARCHAR(31),
--Participant 2 flag
p2_flag VARCHAR(31),
--Participant 1 race/heads
p1_race VARCHAR(31),
--Participant 2 race/heads
p2_race VARCHAR(31),
--Game length in seconds
g_length INT,
--Map
map VARCHAR(127) default 'TBD',
--Finished
finished BOOLEAN default FALSE,
--Winner (either 1, 2 or d for draw)
g_winner CHAR,
--Mode
mode VARCHAR(31),
--Details as JSON-Object (such as hero picks/bans)
g_details BLOB,
--Mapping m_id to matches (m_id)
CONSTRAINT `fk_m_id` FOREIGN KEY (m_id) REFERENCES /*_*/matches (m_id) ON DELETE CASCADE ON UPDATE CASCADE
)/*$wgDBTableOptions*/;

--Automatically genereated for foreign keys
--CREATE INDEX /*_*//*i*/games_m_id ON /*_*/games (m_id);
CREATE INDEX IF NOT EXISTS /*_*//*i*/games_p_id ON /*_*/games (page_id);
CREATE INDEX IF NOT EXISTS /*_*//*i*/games_winner ON /*_*/games (g_winner);
CREATE INDEX IF NOT EXISTS /*_*//*i*/games_map ON /*_*/games (map);
CREATE INDEX IF NOT EXISTS /*_*//*i*/games_p1_race ON /*_*/games (p1_race);
CREATE INDEX IF NOT EXISTS /*_*//*i*/games_p2_race ON /*_*/games (p2_race);

Commit;