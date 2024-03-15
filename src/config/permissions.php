<?php

use App\Enums\Role;

$authorizedUserRoles = [
    'phases:read-all',
    'phases:read-one',

    'phase-items:read-all',
    'phase-items:read-one',

    'shared-folders:read-all',
    'shared-folders:read-one',
    'shared-folders:upload',
    'shared-folders:create',
    'shared-folders:update',

    'private-folders:read-all',
    'private-folders:read-one',
    'private-folders:upload',
    'private-folders:create',
    'private-folders:update',
    'private-folders:delete',

    'shared-files:read-all',
    'shared-files:read-one',
    'shared-files:upload',
    'shared-files:create',
    'shared-files:update',

    'private-files:read-all',
    'private-files:read-one',
    'private-files:upload',
    'private-files:create',
    'private-files:update',
    'private-files:delete',

    'shared-tasks:read-all',
    'shared-tasks:read-one',
    'shared-tasks:create',
    'shared-tasks:update',

    'private-tasks:read-all',
    'private-tasks:read-one',
    'private-tasks:create',
    'private-tasks:update',
    'private-tasks:delete',

    'paths:read-all',
    'paths:read-one',
    'paths:create',
    'paths:update',
    'paths:delete',
    'paths:toggle-pin',
    'paths:reorder-pin',

    'path-settings:accounts:link',

    'channels:read-all',
    'channels:read-one',
    'channels:upload',
    'channels:create',
    'channels:update',
    'channels:create-sub-channel',
];

$pathCollaboratorRoles = array_merge($authorizedUserRoles, [
    'path-settings:read-all',
    'path-settings:read-one',
    'phases:update',
    'phase-items:update',
]);

$pathAdminRoles = array_merge($pathCollaboratorRoles, [
    'shared-files:delete',
    'shared-tasks:delete',
    'shared-folders:delete',
    'roles:reassign',
    'roles:authorized-users:invite',
    'paths:archive',
    'paths:unarchive',
    'phases:create',
    'phases:delete',
    'phase-items:create',
    'phase-items:delete',
]);

$pathCreatorRoles = array_merge($pathAdminRoles, [
    'paths:delete',
    'path-settings:update',
    'path-settings:delete',
    'path-settings:cancel-invitation',
    'path-settings:update-path-background',
    'path-settings:update-page-background',
    'path-settings:update-general-info',
    'user-settings:update-path-background',
    'user-settings:update-page-background',
    'pages:read-all',
    'pages:read-one',
    'pages:create',
    'pages:update',
    'pages:delete',
    'pages:reposition',
    'pages:float-reposition',
    'shared-folders:restore',
    'private-folders:restore',
    'shared-files:restore',
    'private-files:restore',
    'phases:restore',
    'phase-items:restore',
    'roles:authorized-users:remove',
]);

return [
    Role::PATH_CREATOR => $pathCreatorRoles,

    Role::PATH_ADMIN => $pathAdminRoles,

    Role::PATH_COLLABORATOR => $pathCollaboratorRoles,

    Role::AUTHORIZED_USER => $authorizedUserRoles,
];
