<?php

namespace isrdxv\practice\kit;

interface Kit
{
  function giveTo(): bool;
  
  function getName(): string;
  
  function getMainName(): string;
  
  function equals($kit): bool;
  
  function extract(): array;
}