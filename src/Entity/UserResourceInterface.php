<?php

namespace App\Entity;

interface UserResourceInterface
{
    public function denyUnlessOwner(User $user);
}
