<?php

namespace isrdxv\practice\session\misc;

use pocketmine\network\mcpe\protocol\types\DeviceOS;

final class ClientDataInfo
{
	private array $extraData;
	
	private string $device;
	
	private string $deviceTouch;
	
	private string $deviceModel;
	
	function __construct(array $extraData)
	{
		//$this->extraData = $extraData;
		$this->device = $this->setDevice($extraData);
		$this->deviceTouch = $this->setDeviceTouch($extraData);
		$this->deviceModel = $this->setDeviceModel($extraData);
	}
	
	function setDevice(array $data): string
	{
		if ($data["DeviceOS"] === DeviceOS::ANDROID && $data["DeviceModel"] === "") {
			return "Linux";
		}
		return match ($data["DeviceOS"]){
            DeviceOS::UNKNOWN => "Unknown",
            DeviceOS::ANDROID => "Android",
            DeviceOS::IOS => "iOS",
            DeviceOS::OSX => "macOS",
            DeviceOS::AMAZON => "FireOS",
            DeviceOS::GEAR_VR => "GearVR",
            DeviceOS::HOLOLENS => "Hololens",
            DeviceOS::WINDOWS_10 => "Windows 10",
            DeviceOS::WIN32 => "Windows 7",
            DeviceOS::DEDICATED => "Dedicated",
            DeviceOS::TVOS => "tvOS",
            DeviceOS::PLAYSTATION => "PlayStation",
            DeviceOS::NINTENDO => "Nintendo Switch",
            DeviceOS::XBOX => "Xbox",
            DeviceOS::WINDOWS_PHONE => "Windows Phone",
            default => "Unknown"
         };
	}
	
	function setDeviceTouch(array $data): string
	{
		if ($data["DeviceOS"] === DeviceOS::ANDROID && $data["DeviceModel"] === "") {
			return "Keyboard";
		}
		return match ($data["DeviceOS"]){
			DeviceOS::UNKNOWN => "Unknown",
			DeviceOS::ANDROID => "Touch",
			DeviceOS::IOS => "Touch",
			DeviceOS::OSX => "Keyboard",
			DeviceOS::AMAZON => "Touch",
			DeviceOS::GEAR_VR => "Controller",
			DeviceOS::HOLOLENS => "Controller",
			DeviceOS::WINDOWS_10 => "Keyboard",
			DeviceOS::WIN32 => "Keyboard",
			DeviceOS::DEDICATED => "Dedicated",
			DeviceOS::TVOS => "Controller",
			DeviceOS::PLAYSTATION => "Joystick",
			DeviceOS::NINTENDO => "Joystick",
			DeviceOS::XBOX => "Joystick",
			DeviceOS::WINDOWS_PHONE => "Touch",
			default => "Unknown"
		};
	}
	
	function setDeviceModel(): string
	{
		return "none";
	}
	
	function getDevice(): string
	{
		return $this->device;
	}
	
	function getTouch(): string
	{
		return $this->deviceTouch;
	}
	
}