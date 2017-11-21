<?php

use Kirby\Cms\UserBlueprint;
use Kirby\Cms\Form;

return [
    'auth'    => true,
    'pattern' => 'users/(.*?)/blueprint',
    'action'  => function ($email) {

        if ($user = $this->users()->find($email)) {
            return (new UserBlueprint($user->role(), $user))->toArray();
        }

    }
];