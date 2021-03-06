<?php

namespace Ozone\Framework;



/**
 * Form object.
 *
 */
class Form {

//	private $formxml; //definition of the form!

	private $name;
	private $formKey="_0"; //default formKey

	private $fields;

	private $fieldValues = array();

	private $errorMessages = array();
	private $isValidAll = true;
	private $isValidArray = array();

	private $validated = false;

	private $validatorName = null;

	private $retrieved = false;

	public function __construct($formName, $formKey = "_0"){
		$this->name = $formName;
		$this->formKey = $formKey;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
	}

	public function setFormKey($formKey){
		$this->formKey = $formKey;
	}

	public function getFieldType($name){
		$fields = FormXMLStorage::getFormFields($this->name);
		return $fields["$name"]->rendering[0]['type'];
	}

	public function getFieldValue($name){
		$tvalue = $this->fieldValues["$name"];
		$fieldType = $this->getFieldType($name);
		if($fieldType == 'text' || $fieldType == 'password' || $fieldType == 'textarea'|| $fieldType=='select'|| $fieldType=='hidden'){
			if($tvalue !== null){
				return $tvalue;
			} else{
				$fields = FormXMLStorage::getFormFields($this->name);
				$tvalue = $fields["$name"]['defaultValue'];
				return $tvalue;
			}
		}
		if($fieldType=='checkbox'){
			if($tvalue !== null){
				return $tvalue;
			} else{
				$fields = FormXMLStorage::getFormFields($this->name);
				$tvalue = $fields["$name"]['defaultValue'];
				if($tvalue == 'true' || $tvalue == 'on' || $tvalue == 'yes'){
					return true;
				}else{
					return false;
				}
			}
		}

		if($fieldType=='file'){
			$fu = new FileUpload();
			return $fu->getFileItem($this->getFieldLabel($name));
		}

	}

	public function setFieldValue($fieldName, $value){
		$this->fieldValues["$fieldName"] = trim($value);
	}

	public function getFieldTitle($name){
		$fields = FormXMLStorage::getFormFields($this->name);
		$text = xml_localized_text($fields["$name"]->title);
		return trim($text);
	}

	public function getFieldSubTitle($name){
		$fields = FormXMLStorage::getFormFields($this->name);
		$text = xml_localized_text($fields["$name"]->subtitle);
		return trim($text);
	}

	public function getFieldLabel($name){
		return $this->name . $this->formKey . $name;
	}

	public function getFieldMaxLength($name){
		$fields = FormXMLStorage::getFormFields($this->name);
		return $fields[$name]->rendering[0]['maxlength'];
	}

	/**
	 * Returns additional field attribute as defined in the <Extra attribute="value".../> tag
	 * for a given field.
	 * @param string $fieldName
	 * @param string $attributeName
	 * @return string attribute value
	 */
	public function getExtraAttribute($fieldName, $attributeName){
		$fields = FormXMLStorage::getFormFields($this->name);
		return $fields["$fieldName"]->extra[0]["$attributeName"];
	}

	public function setRetrieved($retrieved){
		$this->retrieved = $retrieved;
	}

	public function validate($fieldName = null){
		$this->validated = true;
		$fieldNames = FormXMLStorage::getFormFieldNames($this->name);
		$fields = FormXMLStorage::getFormFields($this->name);
		if($fieldName == null){
			$this->errorMessages = array();
			// validate all the fields
			$this->isValidAll = true;
			foreach ( $fieldNames as $fname){
				$this->validate($fname);
				if($this->isValidArray["$fname"] == false){
					$this->isValidAll = false; // one false is enough to spoil the whole form!
				}
			}
		} else {
			//ok, validate the field $fieldName
			$this->isValidArray["$fieldName"] = true;
			// get rule-chain
			if($fields["$fieldName"]->validation[0] == null){
				$this->isValidArray["$fieldName"] = true;
				return true; // no validation required for this field
			}

			$chain = $fields["$fieldName"]->validation[0];

			if($this->validatorName == null) {
				$this -> validatorName = "BaseFormValidator";
			}
			// As far as I can tell there's only the BaseFormValidator but I might be wrong.
            $this->validatorName = "Ozone\Framework\\".$this->validatorName;
			foreach($chain as $rule){
				$this->isValidArray["$fieldName"]=true;
				$ruleName = $rule['name'];
				$ruleValue = $rule['value'];
				// now perform the validation

				$validator = new $this->validatorName();
				$ruleMethod = $ruleName.'Rule';
				$validationResult_sub = $validator->$ruleMethod($this->getFieldValue($fieldName), $ruleValue);
				if($validationResult_sub == false){
					// and save the validation result!!!
					$this->isValidArray["$fieldName"] = false;
					// and set the error message!
					$this->errorMessages["$fieldName"] = "".xml_localized_text($rule->message);
					return;
				}
			}
		}
	}

	public  function isValid($fieldName = null){
		if($this->validated == false){
			return true;
		}

		if($fieldName == null){
			$this->updateValidAll();
			return $this->isValidAll;
		} else {
			return $this->isValidArray["$fieldName"];
		}

	}

	public function getErrorMessage($fieldName){
		return trim($this->errorMessages[$fieldName]);
	}

	public function getErrorMessages(){
		return $this->errorMessages;
	}

	public function declarations(){
		$out="";
		$out.='<input type="hidden" name="formname" value="'.$this->name  .'"/>';
		$out.='<input type="hidden" name="formkey" value="'.$this->formKey  .'"/>';
		$out.='<input type="hidden" name="use_formtool" value="yes"/>';
		return $out;
	}

	public function renderingString($name){
		$fields = FormXMLStorage::getFormFields($this->name);
		$attributes = $fields[$name]->rendering[0]->attributes();
		$out = "";
		$fieldType = $this->getFieldType($name);
		if($fieldType == 'text' || $fieldType == 'password'|| $fieldType == 'checkbox' || $fieldType=='hidden'){
			if($attributes !== null){
				foreach($attributes as $key => $value){
					$out.=' '.$key.'="'.$value.'" ';
				}
			}
		}

		if($fieldType=="select" || $fieldType=="textarea"){
			if($attributes !== null){
				foreach($attributes as $key => $value){
					if($key!=type){
						$out.=' '.$key.'="'.$value.'" ';
					}
				}
			}
		}

		if($fieldType == 'file'){
			if($attributes !== null){
				foreach($attributes as $key => $value){
					$out.=' '.$key.'="'.$value.'" ';
				}
			}
		}

		return $out;
	}

	public function populateFromParameterArray($parameterArray){
		$fieldNames = FormXMLStorage::getFormFieldNames($this->name);
		foreach ($fieldNames as  $key){
			$paramKey = $this->name.$this->formKey.$key;
			$fieldType = $this->getFieldType($key);

			if($fieldType == 'text' || $fieldType == 'password' || $fieldType=='textarea' || $fieldType=='select' || $fieldType=="hidden"){
				$tmp1 = $parameterArray["$paramKey"];
				if($tmp1 != null){
					$this->fieldValues["$key"] = trim($tmp1);
				} else {
					$this->fieldValues["$key"] = null;
				}
			}

			if($fieldType == 'checkbox'){
				if(isset($parameterArray["$paramKey"])){
					$this->fieldValues["$key"] = true;
				} else {
					$this->fieldValues["$key"] = false;
				}
			}

		}
	}

	private function updateValidAll(){
		$validAll = true;
		$fieldNames = FormXMLStorage::getFormFieldNames($this->name);
		foreach ( $fieldNames as $fname){
			if($this->isValidArray["$fname"] == false){
				$validAll = false;
			}
		}
		$this->isValidAll = $validAll;
	}

	public function getHelpText($fieldName){
		$fields = FormXMLStorage::getFormFields($this->name);
		$text = xml_localized_text($fields["$fieldName"]->help);
		return trim(''.$text);
	}

	public function getAllHelpTexts(){
		$fields = FormXMLStorage::getFormFields($this->name);
		$fieldNames = FormXMLStorage::getFormFieldNames($this->name);
		$helps = array();
		foreach ($fieldNames as $fieldName){
			$helps["$fieldName"] = 	trim(''.$fields["$fieldName"]->help[0]);
		}
		return $helps;
	}

	public function getFieldNames(){
		return FormXMLStorage::getFormFieldNames($this->name);
	}

	/**
	 * Return associated list name for a select element.
	 */
	public function getSelectValueListName($fieldName){
		$fields = FormXMLStorage::getFormFields($this->name);
		return $fields["$fieldName"]['valueList'];
	}

	/**
	 * Return associated table name for a select element.
	 */
	public function getSelectValueTableName($fieldName){
		$fields = FormXMLStorage::getFormFields($this->name);
		return $fields["$fieldName"]['valueTable'];
	}

	/**
	 * When uploading files - this determines the max size (in bytes) allowed to
	 * download. Specified in the -form.xml file in the validation rule chain.
	 */
	public function getUploadMaxSize($name){
		$fields = FormXMLStorage::getFormFields($this->name);
		$rules = $fields["$name"]->validation[0]->rule;
		$mrule = findNodeWithAttribute($rules, 'name', 'upload_maxsize');
		if ($mrule == null) {return null;}
		return $mrule['value'];
	}

	public function isUpload(){
		$formXML = FormXMLStorage::getFormXML($this->name);
		$ustring = $formXML['upload'];
		if($ustring == 'true'){
			return true;
		}else{
			return false;
		}
	}
}
