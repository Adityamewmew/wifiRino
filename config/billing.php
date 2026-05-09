<?php

return [
    /*
    | Samakan dengan token JWT lama (jika ada) selama secret sama.
    */
    'jwt_secret' => env('JWT_SECRET', 'SS-billing-super-secret-key-2026'),
];
