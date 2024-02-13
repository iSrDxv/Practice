<?php

namespace isrdxv\practice\arena\misc;

interface ArenaMode
{
  //NORMALS
  const NO_DEBUFF = "NoDebuff";
  
  const COMBO_FLY = "ComboFly";

  const GAPPLE = "Gapple";

  const CLASSIC = "Classic";
  
  const FIST = "Fist";
  
  const SUMO = "Sumo";
  //ADVANCED
  const BRIDGE = "Bridge";

  const SKYWARS = "SkyWars";

  const BEDWARS = "BedWars";

  //ARRAYS
  const MODES_NORMALS = [self::NO_DEBUFF, self::SUMO, self::FIST, self::GAPPLE, self::COMBO_FLY, self::CLASSIC, self::FIST];

  const MODES_ADVANCED = [self::BRIDGE, self::SKYWARS, self::BEDWARS];
}