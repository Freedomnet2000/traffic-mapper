<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Show the Admin Dashboard overview.
     */
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'userCount' => User::count(),
        ]);
    }

    /**
     * List all users for management.
     */
    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(20);

        return Inertia::render('Admin/UsersList', [
            'users' => $users,
        ]);
    }
}
