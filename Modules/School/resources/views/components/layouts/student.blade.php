{{--
    Only "Dashboard" is wired up — the resource routes for students (create/show/edit/...)
    are pre-existing scaffolding with no view behind them yet. See CLAUDE.md for how to
    add a "Soon" sidebar entry once a real one exists.
--}}
@props(['title' => null])

@php
    $school = \Modules\School\Support\SchoolTenancy::company();
@endphp

<x-school::layouts.dashboard
    :title="$title"
    section="Student"
    :brand-title="$school?->name"
    :navigation="[
        ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'school.student.dashboard', 'active' => 'school.student.dashboard'],
    ]">
    {{ $slot }}
</x-school::layouts.dashboard>
