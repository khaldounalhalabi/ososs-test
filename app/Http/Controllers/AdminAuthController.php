<?php

namespace App\Http\Controllers;

use App\RoleEnum;

class AdminAuthController extends BaseAuthController
{
    public function __construct()
    {
        parent::__construct();
        $this->role = RoleEnum::ADMIN->value;
        $this->relations = [];
    }
}
