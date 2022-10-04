<?php


namespace App\Classes;


class Submenu
{

    /**
     * Submenu constructor.
     * @param string $permission
     * @param string $url
     * @param string $path
     * @param string $icon
     * @param string $title
     */
    public function __construct(
        public string $permission,
        public string $url,
        public string $path,
        public string $icon,
        public string $title,
        public bool $disabled = false,
    )
    {
    }

}