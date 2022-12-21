<?php

namespace Jsl\Config\Contracts;

interface ConfigInterface
{
    /**
     * Get a value
     *
     * @param string $path
     * @param mixed $fallback
     * @param ?string $separator Path separator
     *
     * @return mixed
     */
    public function get(string $path, mixed $fallback = null, ?string $separator = null): mixed;


    /**
     * Check if a value exists
     *
     * @param string $path
     * @param ?string $separator Path separator
     *
     * @return bool
     */
    public function has(string $path, ?string $separator = null): bool;


    /**
     * Add values (replace if already exists)
     *
     * @param array $data
     *
     * @return self
     */
    public function add(array $data): self;


    /**
     * Check if a flag is set
     *
     * @param int $flag
     *
     * @return bool
     */
    public function hasFlag(int $flag): bool;


    /**
     * Load a config file
     *
     * @param string|array $file Single file path or array of paths
     *
     * @return self
     */
    public function load(string|array $file): self;
}
