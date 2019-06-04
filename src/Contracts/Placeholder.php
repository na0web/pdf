<?php


namespace App\Contracts;


use Exception;

abstract class Placeholder
{

    /**
     * @var array
     */
    private $args;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $arguments_name;

    /**
     * @param array $args
     * @return bool
     */
    protected function validate(array $args) : bool
    {
        return true;
    }

    /**
     * @param array $args
     * @return string
     */
    abstract protected function handle(?array $args): string;

    /**
     * @return string
     */
    final public function execute() : string {
        return $this->handle($this->args);
    }

    /**
     * @return string
     */
    final public function getName() : string {
        return $this->name;
    }

    /**
     * @param string $field
     * @throws Exception
     */
    final public function parse(string $field) {
        $va_arg = explode('|', $field);

        if (sizeof($va_arg) > 1) {
            $args = array_slice($va_arg, 1);

            if (is_array($this->arguments_name) && sizeof($args) <= sizeof($this->arguments_name)) {
                $reducedArr = [];
                for ($i = 0; $i < sizeof($args); $i++) {
                    $reducedArr[$this->arguments_name[$i]] = $args[$i];
                }
                $args = array_merge($args, $reducedArr);
            }
            if (!$this->validate($args)) {
                throw new Exception(sprintf("Invalid '%s' arguments: %s", $va_arg[0], $field));
            }
            $this->args = $args;
        }
    }
}