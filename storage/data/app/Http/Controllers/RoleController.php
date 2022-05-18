<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    public function  __construct() {
        $this->middleware(['auth:api']);
    }

    public function getRoles(Request $request) {
        return Role::all();
    }
}
