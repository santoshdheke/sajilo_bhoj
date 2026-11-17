<?php

use App\Support\Workspace;
use Illuminate\Support\Facades\Route;

// A host with no company yet: "/" shows the 410 "create your workspace" page (RequireWorkspace),
// linking to the modules' sign-up pages. Hosts with a company get "/" from their module instead.
if (Workspace::isUnclaimedHost()) {
    Route::get('/', fn () => abort(410, request()->getHost()))->name('workspace.missing');
}
