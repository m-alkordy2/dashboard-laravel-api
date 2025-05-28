<?php

namespace App\Swagger;

/**
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="API Token",
 *     description="استخدم توكن Bearer الصادر من تسجيل الدخول"
 * )
 */

class SwaggerSecurity
{
    // هذا الكلاس فارغ فقط لتعريف الـ SecurityScheme
}
