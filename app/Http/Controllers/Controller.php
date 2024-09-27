<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
     *
     *  @OA\Info(
     *      version="1.0.0",
     *      title="Laravel Product Api",
     *      description="A simple CRUD for manage products",
     *      @OA\Contact(email="casalaspro.alessandro@gmail.com")
     *  )
     *
     *  @OA\Server(url=L5_SWAGGER_CONST_HOST)
     *
     */

     


#[
    OA\Info(version: "1.0.0", description: "petshop api", title: "Petshop-api Documentation"),
    OA\Server(url: 'http://localhost:8088', description: "local server"),
    OA\Server(url: 'http://staging.example.com', description: "staging server"),
    OA\Server(url: 'http://example.com', description: "production server"),
    OA\SecurityScheme( securityScheme: 'bearerAuth', type: "http", name: "Authorization", in: "header", scheme: "bearer"),
]
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
