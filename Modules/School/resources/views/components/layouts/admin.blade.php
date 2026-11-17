{{--
    Admin (school) dashboard shell. `group` names the sidebar section a page belongs to
    (e.g. "Users") so the breadcrumb reads the same path as the sidebar; leave it null for
    a page with no group, like the dashboard itself.

    The nav below is the full school-management IA. Most of it has no controller behind it
    yet — a child with no 'route' renders in the menu as a disabled "Soon" row rather than a
    route/page that doesn't exist. Wire it up by adding 'route' (+ 'active', + 'roleFilter'
    for a Users-style role-filtered link) once that entity is built. See CLAUDE.md.
--}}
@props(['title' => null, 'group' => null])

@php
    $school = \Modules\School\Support\SchoolTenancy::company();
@endphp

<x-school::layouts.dashboard
    :title="$title"
    section="Admin"
    :group="$group"
    :brand-title="$school?->name"
    :navigation="[
        ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'school.admin.dashboard', 'active' => 'school.admin.dashboard'],

        ['label' => 'Users', 'icon' => 'group', 'children' => [
            ['label' => 'All Users', 'route' => 'school.admin.users.index', 'active' => 'school.admin.users.*'],
            ['label' => 'Teachers', 'route' => 'school.admin.users.index', 'active' => 'school.admin.users.*', 'roleFilter' => 'teacher'],
            ['label' => 'Students', 'route' => 'school.admin.users.index', 'active' => 'school.admin.users.*', 'roleFilter' => 'student'],
            ['label' => 'Parents/Guardians'],
            ['label' => 'Staff'],
            ['label' => 'Admins', 'route' => 'school.admin.users.index', 'active' => 'school.admin.users.*', 'roleFilter' => 'admin'],
        ]],

        ['label' => 'Classes', 'icon' => 'meeting_room', 'children' => [
            ['label' => 'All Classes', 'route' => 'school.admin.classrooms.index', 'active' => 'school.admin.classrooms.*'],
            ['label' => 'Sections', 'route' => 'school.admin.sections.index', 'active' => 'school.admin.sections.*'],
            ['label' => 'Subjects', 'route' => 'school.admin.subjects.index', 'active' => 'school.admin.subjects.*'],
            ['label' => 'Class Teachers', 'route' => 'school.admin.class-teachers.index', 'active' => 'school.admin.class-teachers.*'],
        ]],

        ['label' => 'Academics', 'icon' => 'menu_book', 'children' => [
            ['label' => 'Subjects', 'route' => 'school.admin.subjects.index', 'active' => 'school.admin.subjects.*'],
            ['label' => 'Timetable'],
            ['label' => 'Assignments'],
            ['label' => 'Exams'],
            ['label' => 'Results'],
        ]],

        ['label' => 'Attendance', 'icon' => 'fact_check', 'children' => [
            ['label' => 'Student Attendance'],
            ['label' => 'Teacher Attendance'],
            ['label' => 'Attendance Reports'],
        ]],

        ['label' => 'Students', 'icon' => 'school', 'children' => [
            ['label' => 'Student List', 'route' => 'school.admin.users.index', 'active' => 'school.admin.users.*', 'roleFilter' => 'student'],
            ['label' => 'Student Admission'],
            ['label' => 'Student Promotion'],
            ['label' => 'Student Documents'],
        ]],

        ['label' => 'Fees', 'icon' => 'payments', 'children' => [
            ['label' => 'Fee Structure'],
            ['label' => 'Collect Fees'],
            ['label' => 'Payment History'],
            ['label' => 'Fee Reports'],
        ]],

        ['label' => 'Communication', 'icon' => 'forum', 'children' => [
            ['label' => 'Notices'],
            ['label' => 'Announcements'],
            ['label' => 'Messages'],
        ]],

        ['label' => 'Reports', 'icon' => 'insights', 'children' => [
            ['label' => 'Student Reports'],
            ['label' => 'Attendance Reports'],
            ['label' => 'Academic Reports'],
            ['label' => 'Fee Reports'],
        ]],

        ['label' => 'Settings', 'icon' => 'settings', 'children' => [
            ['label' => 'School Settings'],
            ['label' => 'Academic Year'],
            ['label' => 'Classes & Subjects'],
            ['label' => 'User Roles & Permissions'],
        ]],
    ]">
    {{ $slot }}
</x-school::layouts.dashboard>
