<?php

namespace Liquidedge\ExternalStarter\install\makers;

class MakeInstance {
	//--------------------------------------------------------------------------------
	// fields
	//--------------------------------------------------------------------------------
	protected string $instance_id = "";
	protected string $instance_name = "";
	protected string $instance_code = "";
	protected string $instance_url = "";
	protected string $classname = "";
	protected string $extends = "";
	protected string $namespace = "";
	protected string $name = "";

	protected array $method_arr = [];

	protected array $var_arr = [];
	//--------------------------------------------------------------------------------
	// magic
	//--------------------------------------------------------------------------------
	public function __construct($options = []) {
		// init
		$this->name = "Instance";
		$this->namespace = 'acc\core\instance';
		$this->extends = '\acc\core\instance\intf\instance';
	}
	//--------------------------------------------------------------------------------
	// methods
	//--------------------------------------------------------------------------------
	public function set_instance_id(string $instance_id): MakeInstance {
		$this->instance_id = $instance_id;
		return $this;
	}

	//--------------------------------------------------------------------------------

	public function set_instance_name(string $instance_name): MakeInstance {
		$this->instance_name = $instance_name;
		return $this;
	}

	//--------------------------------------------------------------------------------

	public function set_instance_code(string $instance_code): MakeInstance {
		$this->instance_code = $instance_code;
		return $this;
	}

	//--------------------------------------------------------------------------------

	public function set_instance_url(string $instance_url): MakeInstance {
		$this->instance_url = $instance_url;
		return $this;
	}

	//--------------------------------------------------------------------------------

	public function set_extends(string $extends): MakeInstance {
		$this->extends = $extends;
		return $this;
	}

	//--------------------------------------------------------------------------------

	public function set_namespace(string $namespace): MakeInstance {
		$this->namespace = $namespace;
		return $this;
	}

	//--------------------------------------------------------------------------------

	public function set_classname(string $classname): MakeInstance {
		$this->classname = $classname;
		return $this;
	}

	//--------------------------------------------------------------------------------

	public function set_name(string $name): MakeInstance {
		$this->name = $name;
		return $this;
	}

	//--------------------------------------------------------------------------------
	public function set_vars($var_arr) {
		$this->var_arr = $var_arr;
	}

	//--------------------------------------------------------------------------------
	public function build(): string {
		// fn: constructor
		$this->add_method("__construct", [
			'// init',
			'$this->id = "' . $this->instance_id . '";',
			'$this->name = "' . $this->instance_name . '";',
			'$this->code = "' . $this->instance_code . '";',
			'$this->url = "' . $this->instance_url . '";',
		]);

		// fn: apply_options
		$code_arr = [];
		$code_arr[] = '// init';
		$code_arr[] = '$this->apply_development_options();';
		$code_arr[] = '';
		foreach ($this->var_arr as $var_index => $var_item) {
			if (is_bool($var_item)) $value = ($var_item ? "true" : "false");
			else $value = "\"{$var_item}\"";

			$code_arr[] = "\$this->{$var_index} = {$value};";
		}
		$this->add_method("apply_options", $code_arr);

		// done
		return $this::__build();
	}

	//--------------------------------------------------------------------------------
	public function __build(): string {
		// functions
		$functions_arr = [];
		foreach ($this->method_arr as $method_index => $method_item) {
			$visibility = ($method_index == "__construct" ? "protected" : "public");
			$functions_arr[] = "	{$visibility} function {$method_index}() {";
			foreach ($method_item as $method_item_item) {
				$functions_arr[] = "		{$method_item_item}";
			}
			$functions_arr[] = "	}";
			$functions_arr[] = "	//--------------------------------------------------------------------------------";
		}

		// done
		return implode("\n", [
			'<?php',
			'',
			"namespace {$this->namespace};",
			'',
			'/**',
			' * Class.',
			' *',
			' * @author Liquid Edge Solutions',
			' * @copyright Copyright Liquid Edge Solutions. All rights reserved.',
			' */',
			"class {$this->classname} extends {$this->extends} {",
			'	//--------------------------------------------------------------------------------',
			'	// functions',
			'	//--------------------------------------------------------------------------------',
			implode("\n", $functions_arr),
			'}',
		]);
	}

	//--------------------------------------------------------------------------------
	public function add_method($name, $code): void {
		$this->method_arr[$name] = $code;
	}
	//--------------------------------------------------------------------------------
	// internal
	//--------------------------------------------------------------------------------
	protected function build_methods() {

	}
	//--------------------------------------------------------------------------------
}