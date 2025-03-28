<?php

namespace App\Http\Controllers;

use App\RoleEnum;

class CustomerAuthController extends BaseAuthController
{
    public function __construct()
    {
        parent::__construct();
        $this->role = RoleEnum::CUSTOMER->value;
        $this->relations = ['country', 'currency'];
    }
}
