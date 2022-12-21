<?php

namespace Jsl\Config;

use Jsl\Config\Contracts\ConfigInterface;
use Jsl\Config\Exceptions\FileNotFoundException;
use Jsl\Config\Exceptions\InvalidFormatException;

class Config implements ConfigInterface
{
    /**
     * Throw exception if config file not found
     */
    const THROW_ON_FILE_NOT_FOUND = 1 << 10;

    /**
     * Ignore duplicates instead of replacing them
     */
    const IGNORE_DUPLICATES = 1 << 2;

    /**
     * @var array
     */
    protected array $data = [];

    /**
     * @var string
     */
    protected string $separator = '.';

    /**
     * @var int
     */
    protected int $flags = 0;


    /**
     * @param string|array $files
     * @param ?string $separator If null, dot (.) will be used
     * @param int $flags
     */
    public function __construct(string|array $file = [], ?string $separator = null, int $flags = 0)
    {
        $this->flags = $flags;
        $this->separator = $separator ?? '.';
        $this->load($file);
    }


    /**
     * @inheritDoc
     */
    public function get(string $path, mixed $fallback = null, ?string $separator = null): mixed
    {
        return $this->find($path, $fallback, $separator);
    }


    /**
     * @inheritDoc
     */
    public function has(string $path, ?string $separator = null): bool
    {
        return $this->find($path, false, $separator, false);
    }


    /**
     * Find a value
     *
     * @param string $path
     * @param mixed $fallback
     * @param ?string $separator
     * @param bool $returnValue
     *
     * @return mixed
     */
    protected function find(string $path, mixed $fallback, ?string $separator, bool $returnValue = true): mixed
    {
        $separator = $separator ?? $this->separator;

        $data = &$this->data;
        $keys = explode($separator, trim($path, '.'));

        foreach ($keys as $key) {
            if (!is_array($data) || !key_exists($key, $data)) {
                return $returnValue ? $fallback : false;
            }

            $data = &$data[$key];
        }

        return $returnValue ? $data : true;
    }


    /**
     * @inheritDoc
     */
    public function add(array $data): self
    {
        $this->data = $this->hasFlag(self::IGNORE_DUPLICATES)
            ? array_replace_recursive($data, $this->data)
            : array_replace_recursive($this->data, $data);

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function hasFlag(int $flag): bool
    {
        return ($this->flags & $flag) === $flag;
    }


    /**
     * @inheritDoc
     */
    public function load(string|array $path): self
    {
        foreach ((array)$path as $file) {
            if (is_file($file)) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                $data = null;

                if ($ext === 'json') {
                    $data = json_decode(file_get_contents($file), true);
                } else if ($ext === 'php') {
                    $data = require $file;
                }

                if (is_array($data) === false) {
                    throw new InvalidFormatException("The contents of {$file} must either contain or resolve into an array");
                }

                $this->add($data);
            } else if ($this->hasFlag(self::THROW_ON_FILE_NOT_FOUND)) {
                throw new FileNotFoundException("The file {$file} was not found");
            }
        }

        return $this;
    }
}
