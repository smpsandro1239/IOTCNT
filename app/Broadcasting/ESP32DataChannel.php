<?php

namespace Approadcasting;

use Appodelsser;

class ESP32DataChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  ppodelsser  $user
     * @return array|bool
     */
    public function join(User $user)
    {
        // Permitir acesso apenas a usuários autenticados
        return $user !== null;
    }
}
