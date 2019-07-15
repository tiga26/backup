<?php

namespace App\Http\Controllers;

use App\Jobs\BackupDatabase;

class BackupController extends Controller
{
    /**
     * Where to redirect users after backup.
     *
     * @var string
     */
    protected $redirectTo = '/';

    public function index()
    {
        BackupDatabase::dispatchNow();
    }
}
