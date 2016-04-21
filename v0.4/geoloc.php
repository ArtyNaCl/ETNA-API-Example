<?php
	
	namespace geoloc
	{
		/**
		* Class for geolocalisation calculs
		*/
		class Geoloc
		{
			private 	$selfClientLat;
			private 	$selfClientLon;
			private 	$pi;
			
			function 		__construct($clientLat, $clientLon)
			{
				$this->pi = 3.14159265358979;
				$this->selfClientLat = $clientLat * $this->pi / 180;
				$this->selfClientLon = $clientLon * $this->pi / 180;
			}
			
			// arc cos (sin ϕA sin ϕB + cos ϕA cos ϕB cos dλ)

			public function isInRange($providerLat, $providerLon, $range)
			{
				$distance = 6378137 * acos((sin($this->selfClientLat) * sin($providerLat * $this->pi / 180)) + (cos($this->selfClientLat) * cos($providerLat * $this->pi / 180)) * cos($this->selfClientLon - ($providerLon * $this->pi / 180)));
				if ($distance <= $range + 200)
					return true;
				return false;
			}
		}
	}	