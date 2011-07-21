<?php

class GeneralFunctions {
	static function getCategories(&$ref, $extensionName, $includeDescription = true){
		$values = Array(0 => $ref->localization->translate('category',$extensionName),
					1 => $ref->localization->translate('category1',$extensionName),
					2 => $ref->localization->translate('category2',$extensionName),
					3 => $ref->localization->translate('category3',$extensionName),
					4 => $ref->localization->translate('category4',$extensionName),
					5 => $ref->localization->translate('category5',$extensionName));
		if(!$includeDescription){
			unset($values[0]);
		}
		return $values;	
	}
	
	static function getIndustries(&$ref, $extensionName, $includeDescription = true){
		$values = Array(0 => $ref->localization->translate('industry',$extensionName),
					1 => $ref->localization->translate('industry1',$extensionName),
					2 => $ref->localization->translate('industry2',$extensionName),
					3 => $ref->localization->translate('industry3',$extensionName),
					4 => $ref->localization->translate('industry4',$extensionName));
		if(!$includeDescription){
			unset($values[0]);
		}
		return $values;	
	}
	
	static function getCompanySizes(&$ref, $extensionName, $includeDescription = true){
		$values = Array(0 => $ref->localization->translate('size',$extensionName),
					1 => $ref->localization->translate('size1',$extensionName),
					2 => $ref->localization->translate('size2',$extensionName),
					3 => $ref->localization->translate('size3',$extensionName),
					4 => $ref->localization->translate('size4',$extensionName));
		if(!$includeDescription){
			unset($values[0]);
		}
		return $values;
	}
	
	static function getPages(&$ref, $extensionName, $includeDescription = true){
		$values = Array(0 => $ref->localization->translate('page',$extensionName),
					1 => $ref->localization->translate('page1',$extensionName),
					2 => $ref->localization->translate('page2',$extensionName),
					3 => $ref->localization->translate('page3',$extensionName),
					4 => $ref->localization->translate('page4',$extensionName));
		if(!$includeDescription){
			unset($values[0]);
		}
		return $values;
	}
	
	static function getLanguages(&$ref, $extensionName, $includeDescription = true){
		$values = Array();
		for($i=1;$i<10;$i++){
			$values[$i] = $i;
		}
		return $values;
	}
	
	static function removeNotSet(&$ref, &$request, &$allowedCategories, &$allowedIndustries, &$allowedCompanySizes){
		$category = 0;
		if($request->hasArgument('category')){
			$category = intval($request->getArgument('category')); // 5
		}
		$industry = 0;
		if($request->hasArgument('industry')){
			$industry = intval($request->getArgument('industry')); // 4
		}
		$companySize = 0;
		if($request->hasArgument('companySize')){
			$companySize = intval($request->getArgument('companySize')); // 4
		}
		
		$remove = Array();
		for($i=1;$i<count($allowedCategories); $i++){
			$count = $ref->referenceRepository->countByOption($i,$industry,$companySize,$ref->showDeactivated);
			if($count == 0){
				$remove[$i] = 'remove';
			}
		}
		$allowedCategories = array_diff_key($allowedCategories,$remove);
		$remove = Array();
		for($i=1;$i<count($allowedIndustries); $i++){
			$count = $ref->referenceRepository->countByOption($category,$i,$companySize,$ref->showDeactivated);
			if($count == 0){
				$remove[$i] = 'remove';
			}
		}
		$allowedIndustries = array_diff_key($allowedIndustries,$remove);
		$remove = Array();
		for($i=1;$i<count($allowedCompanySizes); $i++){
			$count = $ref->referenceRepository->countByOption($category,$industry,$i,$ref->showDeactivated);
			if($count == 0){
				$remove[$i] = 'remove';
			}
		}
		$allowedCompanySizes = array_diff_key($allowedCompanySizes,$remove);
	}
}
?>