-- #!sqlite
-- #{ practice

-- # { init.user
CREATE TABLE IF NOT EXISTS user(xuid VARCHAR(23) NOT NULL UNIQUE PRIMARY KEY, name VARCHAR(30), custom_name VARCHAR(30) NULL, rank TEXT, language TEXT, coin INT, elo INT, firstplayed TEXT, lastplayed TEXT, kills INT, wins INT, deaths INT, address TEXT, device TEXT, control TEXT);
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

-- # { insert
-- #   { data
-- #     :xuid string
-- #     :name string
-- #     :custom_name string
-- #     :rank string
-- #     :language string
-- #     :coin int
-- #     :elo int
-- #     :firstplayed string
-- #     :lastplayed string
-- #     :kills int
-- #     :wins int
-- #     :deaths int
-- #     :address string
-- #     :device string
-- #     :control string
INSERT OR REPLACE INTO user(xuid, name, custom_name, rank, language, coin, elo, firstplayed, lastplayed, kills, wins, deaths, address, device, control) VALUES (:xuid, :name, :custom_name, :rank, :language, :coin, :elo, :firstplayed, :lastplayed, :kills, :wins, :deaths, :address, :device, :control);
-- #   }
-- #   { settings
-- #     :xuid string
-- #     :scoreboard bool
-- #     :queue bool
-- #     :cps bool
-- #     :auto_join bool
INSERT OR REPLACE INTO settings(xuid, scoreboard, queue, cps, auto_join) VALUES (:xuid, :scoreboard, :queue, :cps, :auto_join);
-- #   }
-- #   { staff.ban
-- #     :name string
-- #     :reason string
-- #     :duration string
-- #     :staff_name string
INSERT OR REPLACE INTO bans(name, reason, duration, staff_name) VALUES (:name, :reason, :duration, :staff_name);
-- #   }
-- #   { staff.stats
-- #     :xuid string
-- #     :name string
-- #     :bans int
-- #     :kicks int
-- #     :mutes int
-- #     :reports int
INSERT OR REPLACE INTO staff_stats(xuid, name,  bans, kicks, mutes, reports) VALUES (:xuid :name, :bans, :kicks, :mutes, :reports);
-- #   }
-- # }

-- # { leaderboard
-- #   { kills
SELECT * FROM user ORDER BY kills DESC LIMIT 10;
-- #   }
-- #   { deaths
SELECT * FROM user ORDER BY deaths DESC LIMIT 10;
-- #   }
-- #   { wins
SELECT * FROM user ORDER BY wins DESC LIMIT 10;
-- #   }
-- # }

-- # }
