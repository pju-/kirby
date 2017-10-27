<?php

return [
    'pattern' => 'session',
    'method'  => 'DELETE',
    'action'  => function () {
        $user = $this->user();
        $user->update(['token' => null]);
        return $user;
    }
];