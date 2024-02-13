<?php

namespace isrdxv\practice\arena\misc;

interface ArenaMode
{
  //NORMALS
  const NO_DEBUFF = "nodebuff";
  
  const COMBO_FLY = "comboFly";

  const GAPPLE = "gapple";

  const CLASSIC = "classic";
  
  const FIST = "fist";
  
  const SUMO = "sumo";

  //ADVANCED
  const BRIDGE = "bridge";

  const SKYWARS = "skywars";

  const BEDWARS = "bedwars";

  //ARRAYS
  const MODES_NORMALS = [self::NO_DEBUFF, self::SUMO, self::FIST, self::GAPPLE, self::COMBO_FLY, self::CLASSIC, self::FIST];

  const MODES_ADVANCED = [self::BRIDGE, self::SKYWARS, self::BEDWARS];
}