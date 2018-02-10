<?php

namespace Kirby\Form\Mixins;

use Exception;
use Kirby\Data\Handler\Yaml;
use Kirby\Form\Exceptions\DisabledFieldException;
use Kirby\Form\Exceptions\ValidationException;
use Kirby\Util\Str;

trait Value
{

    protected $default;
    protected $empty;
    protected $error;
    protected $value;

    public function default()
    {
        return $this->default;
    }

    protected function defaultDefault()
    {
        return null;
    }

    protected function defaultValue()
    {
        return $this->default();
    }

    protected function emptyValues(): array
    {
        return [null, '', []];
    }

    public function error()
    {
        try {
            $this->validate($this->value());
            return false;
        } catch (Exception $e) {
            return [
                'type'    => method_exists($e, 'getType') ? $e->getType() : null,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function isEmpty(): bool
    {
        $args  = func_get_args();
        $value = count($args) > 0 ? $args[0] : $this->value();

        return in_array($value, $this->emptyValues(), true) === true;
    }

    /**
     * Checks if the current field value is valid
     *
     * @return boolean
     */
    public function isValid(): bool
    {
        return $this->validate($this->value());
    }

    /**
     * @param mixed $default
     * @return self
     */
    protected function setDefault($default = null): self
    {
        $this->default = $this->valueFromInput($default);
        return $this;
    }

    /**
     * @param mixed $value
     * @return self
     */
    protected function setValue($value = null): self
    {
        // set the default if the value is empty
        $value = $this->isEmpty($value) === true ? $this->default() : $value;

        // convert the given value to a data value
        $this->value = $this->valueFromInput($value);

        return $this;
    }

    public function stringValue(): string
    {
        return $this->valueToString($this->value());
    }

    protected function validate($value): bool
    {
        return true;
    }

    public function value()
    {
        return $this->value;
    }

    protected function valueFromInput($input)
    {
        return $input;
    }

    protected function valueFromList($value, $separator = ',')
    {
        if (is_array($value) === true) {
            return $value;
        }

        return Str::split($value, $separator);
    }

    protected function valueFromYaml($value)
    {
        if (is_array($value) === true) {
            return $value;
        }

        return Yaml::decode($value);
    }

    protected function valueToList($value, $separator = ', ')
    {
        return implode($separator, $value);
    }

    protected function valueToYaml($value)
    {
        return Yaml::encode($value);
    }

    protected function valueToString($value): string
    {
        return (string)$value;
    }

}