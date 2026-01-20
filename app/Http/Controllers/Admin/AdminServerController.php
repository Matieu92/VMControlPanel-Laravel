<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;

class AdminServerController extends Controller
{
    public function index()
    {
        $servers = Server::with(['user', 'node', 'subscription.plan', 'operatingSystem'])->latest()->get();
        
        return view('admin.servers.index', compact('servers'));
    }
}