<?php

namespace App\Enums\ViewPaths\Admin;

enum AccessControl
{
    const DEPARTMENT_INDEX = [
        'URI' => '/',
        'VIEW' => 'admin-views.access-control.department.list'
    ];

    const DEPARTMENT_ADD = [
        'URI' => 'store',
        'VIEW' => 'admin-views.access-control.department.create'
    ];

    const DEPARTMENT_UPDATE = [
        'URI' => 'edit',
        'VIEW' => 'admin-views.access-control.department.edit'
    ];

    const DEPARTMENT_DELETE = [
        'URI' => 'delete',
        'VIEW' => ''
    ];

    const DEPARTMENT_UNIT_INDEX = [
        'URI' => '/',
        'VIEW' => 'admin-views.access-control.department-unit.list'
    ];

    const DEPARTMENT_UNIT_ADD = [
        'URI' => 'store',
        'VIEW' => 'admin-views.access-control.department-unit.create'
    ];

    const DEPARTMENT_UNIT_UPDATE = [
        'URI' => 'edit',
        'VIEW' => 'admin-views.access-control.department-unit.edit'
    ];

    const GEOGRAPHY_INDEX = [
        'URI' => '/',
        'VIEW' => 'admin-views.access-control.geography.list'
    ];

    const GEOGRAPHY_ADD = [
        'URI' => 'store',
        'VIEW' => 'admin-views.access-control.geography.create'
    ];

    const GEOGRAPHY_UPDATE = [
        'URI' => 'edit',
        'VIEW' => 'admin-views.access-control.geography.edit'
    ];

    const USER_ASSIGNMENT_INDEX = [
        'URI' => '/',
        'VIEW' => 'admin-views.access-control.user-assignment.list'
    ];

    const USER_ASSIGNMENT_ADD = [
        'URI' => 'store',
        'VIEW' => 'admin-views.access-control.user-assignment.create'
    ];

    const USER_ASSIGNMENT_UPDATE = [
        'URI' => 'edit',
        'VIEW' => 'admin-views.access-control.user-assignment.edit'
    ];
}

