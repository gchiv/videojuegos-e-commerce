<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;
use OpenApi\Attributes\Info;

#[OA\Info(
    title: 'E-commerce API',
     version: '1.0'
)]

#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer"
)]

abstract class Controller
{
    //
}
