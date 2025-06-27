<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="REST API приложения для тестового задания",
 *     description="API-документация для CRUD организаций, зданий, видов деятельности и аутентификации.",
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API сервер"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Token",
 *     description="Авторизация по токену Laravel Sanctum"
 * )
 */
abstract class Controller
{
    //
}
