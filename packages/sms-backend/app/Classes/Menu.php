<?php


namespace App\Classes;


use Illuminate\Support\Collection;

class Menu
{

    public Collection $submenus;

    /**
     * Submenu constructor.
     * @param string $permission
     * @param string $icon
     * @param string $title
     * @param Submenu ...$submenus
     */
    public function __construct(
        public string $permission,
        public string $icon,
        public string $title,
        Submenu ...$submenus
    )
    {
        $this->submenus = collect($submenus ?? []);
    }

    public function getAllSubmenuRoutes(): array
    {
        $paths = $this->submenus->map(function (Submenu $submenu) {
            return $submenu->path . '*';
        });

        return $paths->all();
    }
}
