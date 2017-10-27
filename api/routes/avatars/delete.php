<?php

return [
    'pattern' => 'users/(.*?)/avatar',
    'method'  => 'DELETE',
    'action'  => function ($id) {
        if ($user = $this->users()->find($id)) {
            return $user->avatar()->delete();
        }
    }
];