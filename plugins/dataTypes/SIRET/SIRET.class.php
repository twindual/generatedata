<?php

/**
 * @package DataTypes
 * @author Fabrice Marquès <fabrice.marques@gmail.com>
 */

class DataType_SIRET extends DataTypePlugin {

	protected $isEnabled = true;
	protected $dataTypeName = "SIRET";
	protected $hasHelpDialog = true;
	protected $dataTypeFieldGroup = "human_data";
	protected $dataTypeFieldGroupOrder = 100;

	public static function generateSiret() {	    	 	
		$sumSiren = 0;
		$sumSiret = 0;
		$cleSiren= 1;
		$cleSiret= 2;
		$minRan = 0;
		$maxRan = 9;
		$siren = '';
		$siret = '';
		
		// generation d'un siren valide
		for($i=0;$i<8;$i++) {
			// on génére un nombre entre 0 et 9 
			$rand = mt_rand($minRan,$maxRan);

			// on concatène se nombre au siret
			$siren .= $rand; 

			/* Le numéro SIRET est composé de 14 chiffres,
			 *  dont un chiffre de contrôle (le dernier) qui permet de vérifier la validité du numéro de SIRET (SIREN + NIC).
			 *   Celui-ci est calculé suivant la formule de Luhn.
			 *   Le principe est le suivant : on multiplie les chiffres de rang impair à partir de la droite par 1, ceux de rang pair par 2 ;
			 *    la somme des chiffres obtenus doit être multiple de 10.
			 */
			$ctrlSiren = $rand * $cleSiren;
			$ctrlSiret = $rand * $cleSiret;

			// Si la valeur obtenu et supérieur ou egale à 10 il faut décomposer en 1+0 
			// ce qui équivaux à lui retirer 9
			// contôle pour le siren
			if($ctrlSiren > 9){
				$sumSiren += ($ctrlSiren-9);
			}else{
				$sumSiren += $ctrlSiren;
			}
			
			// contôle pour le siret
			if($ctrlSiret > 9){
				$sumSiret += ($ctrlSiret - 9);
			}else{
				$sumSiret += $ctrlSiret;
			}
			
			// mise à jour des clés
			if($cleSiren == 1){
				$cleSiren = 2;
				$cleSiret = 1;
			}else{
				$cleSiren = 1;
				$cleSiret = 2;
			}
		}  

		// la somme doit être congrue à zéro modulo 10
		$moduloSiren = ($sumSiren % 10);
		if($moduloSiren == 0){
			$diffSiren = 0;
		}else{
			$diffSiren = 10 - $moduloSiren;
		}

		$siren .= $diffSiren;

		// la cle du siren est ajouté au calcul du siret
		$ctrlSiret = $diffSiren * $cleSiret;

		// contôle pour le siret
		if($ctrlSiret > 9){
			$sumSiret += ($ctrlSiret - 9);
		}else{
			$sumSiret += $ctrlSiret;
		}
		
		// aon ajoute un début de NIC au siren
		$siret = $siren . "0000";

		// la somme doit être congrue à zéro modulo 10
		$moduloSiret = ($sumSiret % 10);
		if($moduloSiret == 0){
			$diffSiret = 0;
		}else{
			$diffSiret = 10 - $moduloSiret;
		}

		$siret .= $diffSiret;
		
		return $siret;
	}

	public function generate($generator, $generationContextData) {
		$SIRET = self::generateSiret();
		return array(
			"display" => $SIRET
		);
	}

	public function getDataTypeMetadata() {
		return array(
			"SQLField" => "varchar(14)",
			"SQLField_Oracle" => "varchar2(14)",
			"SQLField_MSSQL" => "VARCHAR(14) NULL"
		);
	}

	public function getHelpHTML() {
		return "<p>{$this->L["help"]}</p>";
	}
	
	
}
