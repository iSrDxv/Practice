-- #! mysql
-- #{ practice

-- # { init.user
CREATE TABLE IF NOT EXISTS data_user(xuid VARCHAR(23) NOT NULL UNIQUE PRIMARY KEY, name VARCHAR(30), custom_name VARCHAR(30) NULL, rank TEXT, language TEXT, coin INT, elo INT, firstplayed TEXT, lastplayed TEXT, kills INT, wins INT, deaths INT, address TEXT, device TEXT, control TEXT);
-- # }

-- # { init.settings
CREATE TABLE IF NOT EXISTS settings(xuid VARCHAR(23) NOT NULL UNIQUE PRIMARY KEY, scoreboard BOOLEAN, queue BOOLEAN, cps BOOLEAN, auto_join BOOLEAN);
-- # }

-- # { init.ban
CREATE TABLE IF NOT EXISTS bans(name VARCHAR(30) NOT NULL UNIQUE PRIMARY KEY, reason VARCHAR(20), duration TEXT, staff_name VARCHAR(30));
-- # }

-- # { init.staff
CREATE TABLE IF NOT EXISTS staff_stats(xuid VARCHAR(23) NOT NULL UNIQUE PRIMARY KEY, name VARCHAR(30), bans INT, kicks INT, mutes INT, reports INT);
-- # }

-- # { data
-- # :xuid string
-- # :name string
-- # :custom_name string
-- # :rank string
-- # :language string
-- # :coin int
-- # :elo int
-- # :firstplayed string
-- # :lastplayed string
-- # :kills int
-- # :wins int
-- # :deaths int
-- # :address string
-- # :device string
-- # :control string
INSERT INTO data_user(xuid, name, custom_name, rank, language, coin, elo, firstplayed, lastplayed, kills, wins, deaths, address, device, control) VALUES (:xuid, :name, :custom_name, :rank, :language, :coin, :elo, :firstplayed, :lastplayed, :kills, :wins, :deaths, :address, :device, :control) ON DUPLICATE KEY UPDATE xuid=:xuid, name=:name;
-- # }

-- # { settings
-- # :xuid string
-- # :scoreboard bool
-- # :queue bool
-- # :cps bool
-- # :auto_join bool
INSERT INTO settings(xuid, scoreboard, queue, cps, auto_join) VALUES (:xuid, :scoreboard, :queue, :cps, :auto_join) ON DUPLICATE KEY UPDATE xuid=:xuid;
-- # }

-- # { staff.ban
-- # :name string
-- # :reason string
-- # :duration string
-- # :staff_name string
INSERT INTO bans(name, reason, duration, staff_name) VALUES (:name, :reason, :duration, :staff_name) ON DUPLICATE KEY UPDATE name=:name;
-- # }

-- # { staff.stats
-- # :xuid string
-- # :name string
-- # :bans int
-- # :kicks int
-- # :mutes int
-- # :reports int
INSERT INTO staff_stats(xuid, name,  bans, kicks, mutes, reports) VALUES (:xuid :name, :bans, :kicks, :mutes, :reports) ON DUPLICATE KEY UPDATE xuid=:xuid, name=:name;
-- # }

-- # }