Begin;

CREATE TABLE IF NOT EXISTS /*_*/matches(
--Primary Key
m_id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
--Page ID
page_id INT UNSIGNED NOT NULL,
--Date
m_date TIMESTAMP,
--Participant 1
participant_1 VARCHAR(255) NOT NULL,
--Participant 2
participant_2 VARCHAR(255) NOT NULL,
--Participant 1 team template
p1_template VARCHAR(255),
--Participant 2 team template
p2_template VARCHAR(255),
--Participant 1 flag
p1_flag VARCHAR(31),
--Participant 2 flag
p2_flag VARCHAR(31),
--Participant 1 race/heads
p1_race VARCHAR(31),
--Participant 2 race/heads
p2_race VARCHAR(31),
--Tournament Page Title
tournament VARCHAR(255) NOT NULL,
--Liquipedia Tier
t_tier VARCHAR(63),
--Tournament Name (for ticker etc)
t_name VARCHAR(255),
--Tournmant icon (File Page)
t_icon VARCHAR(255),
--Finished
finished BOOLEAN NOT NULL default FALSE,
--Participant 1 score
p1_score INT,
--Participant 2 score
p2_score INT,
--Winner (either 1, 2 or d for draw)
winner CHAR,
--If walkover, either 1 or 2 as winner
walkover INT,
--Mode (team, indiv, teamindiv or special modes such as archon or doubles)
mode VARCHAR(31),
--Additional details as JSON-Object
details BLOB
)/*$wgDBTableOptions*/;

CREATE INDEX IF NOT EXISTS /*_*//*i*/matches_p1 ON /*_*/matches (participant_1);
CREATE INDEX IF NOT EXISTS /*_*//*i*/matches_p2 ON /*_*/matches (participant_2);
CREATE INDEX IF NOT EXISTS /*_*//*i*/matches_date ON /*_*/matches (m_date);

Commit;