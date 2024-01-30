-- #! sqlite
-- #{ practice

-- # { init.user
CREATE TABLE IF NOT EXISTS data_user(xuid VARCHAR(23) NOT NULL UNIQUE PRIMARY KEY, name VARCHAR(30), custom_name VARCHAR(30) NULL, rank TEXT, language TEXT, coin INT, elo INT, firstplayed TEXT, lastplayed TEXT, kills INT, wins INT, deaths INT, address TEXT, device TEXT, control TEXT);
-- # }

-- # { init.settings
CREATE TABLE IF NOT EXISTS settings(xuid VARCHAR(23) NOT NULL UNIQUE PRIMARY KEY, scoreboard BOOLEAN, queue BOOLEAN, cps BOOLEAN, auto_join BOOLEAN);
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
INSERT INTO data_user(xuid, name, custom_name, rank, language, coin, elo, firstplayed, lastplayed, kills, wins, deaths, address, device, control) VALUES (:xuid, :name, :custom_name, :rank, :language, :coin, :elo, :firstplayed, :lastplayed, :kills, :wins, :deaths, :address, :device, :control);
-- # }

-- # { settings
-- # :xuid string
-- # :scoreboard bool
-- # :queue bool
-- # :cps bool
-- # :auto_join bool
INSERT INTO settings(xuid, scoreboard, queue, cps, auto_join) VALUES (:xuid, :scoreboard, :queue, :cps, :auto_join);
-- # }

-- # }