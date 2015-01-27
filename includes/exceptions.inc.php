<?php

class ExceptionParameter extends Exception {}
	class ExceptionConstructor extends ExceptionParameter {}
		class ExceptionLookup extends ExceptionConstructor {}
		class ExceptionPath extends ExceptionConstructor {}

class ExceptionMySQL extends Exception {}

class ExceptionObject extends Exception {}
	class ExceptionContainer extends ExceptionObject {}
	class ExceptionImage extends ExceptionObject {}

?>