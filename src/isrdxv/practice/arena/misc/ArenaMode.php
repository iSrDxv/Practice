<?php

namespace isrdxv\practice\arena\misc;

interface ArenaMode
{
  //FFA
  const SUMO_FFA = "SumoFFA";

  const COMBO_FFA = "ComboFFA";

  const GAPPLE_FFA = "GappleFFA";

  const CLASSIC_FFA = "ClassicFFA";

  const FIST_FFA = "FistFFA";

  const BUILD_UHC_FFA = "BuildUHC FFA";

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
  const MODES_NORMALS = [self::SUMO_FFA, self::FIST_FFA, self::GAPPLE_FFA, self::COMBO_FFA, self::BUILD_UHC_FFA, self::CLASSIC_FFA, self::NO_DEBUFF, self::SUMO, self::FIST, self::GAPPLE, self::COMBO_FLY, self::CLASSIC, self::FIST];

  const MODES_ADVANCED = [self::BRIDGE, self::SKYWARS, self::BEDWARS];
}