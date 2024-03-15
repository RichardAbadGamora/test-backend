<?php

use App\Enums\Access;
use App\Enums\PageType;

return [
    /**
     * grid_x:
     * Horizontal position within the Grid.
     * e.g. x = 0 means on the left-most part of the Grid.
     *
     * grid_y:
     * Vertical position within the Grid.
     * e.g. y = 0 means on the top-most part of the Grid.
     *
     * grid_width: col-span on the Grid.
     * grid_height: row-span on the Grid.
     */
    'defaults' => [
        [
            'name' => 'Overview',
            'type' => PageType::OVERVIEW,
            'access' => Access::SHARED,
            'singleton' => false,
            'deletable' => false,
            'grid_x' => '0',
            'grid_y' => '0',
            'grid_width' => '1',
            'grid_height' => '15',
        ],
        [
            'name' => 'Shared Files',
            'type' => PageType::FILES,
            'access' => Access::SHARED,
            'singleton' => false,
            'deletable' => false,
            'grid_x' => '1',
            'grid_y' => '0',
            'grid_width' => '1',
            'grid_height' => '15',
        ],
        [
            'name' => 'Private Files',
            'type' => PageType::FILES,
            'access' => Access::PRIVATE,
            'singleton' => false,
            'deletable' => false,
            'grid_x' => '2',
            'grid_y' => '0',
            'grid_width' => '1',
            'grid_height' => '15',
        ],
        [
            'name' => 'Messaging',
            'type' => PageType::MESSAGING,
            'access' => Access::SHARED,
            'singleton' => false,
            'deletable' => false,
            'grid_x' => '3',
            'grid_y' => '0',
            'grid_width' => '1',
            'grid_height' => '15',
        ],
        [
            'name' => 'Shared Tasks',
            'type' => PageType::TASKS,
            'access' => Access::SHARED,
            'singleton' => false,
            'deletable' => false,
            'grid_x' => '0',
            'grid_y' => '15',
            'grid_width' => '1',
            'grid_height' => '15',
        ],
        [
            'name' => 'Private Tasks',
            'type' => PageType::TASKS,
            'access' => Access::PRIVATE,
            'singleton' => false,
            'deletable' => false,
            'grid_x' => '1',
            'grid_y' => '15',
            'grid_width' => '1',
            'grid_height' => '15',
        ],
        [
            'name' => 'All Paths',
            'type' => PageType::ALL_PATHS,
            'access' => Access::SHARED,
            'singleton' => true,
            'deletable' => false,
            'grid_x' => '2',
            'grid_y' => '15',
            'grid_width' => '1',
            'grid_height' => '15',
        ],
        [
            'name' => 'Path Settings',
            'type' => PageType::PATH_SETTINGS,
            'access' => Access::SHARED,
            'singleton' => true,
            'deletable' => false,
            'grid_x' => '3',
            'grid_y' => '15',
            'grid_width' => '1',
            'grid_height' => '15',
        ],
    ]
];
