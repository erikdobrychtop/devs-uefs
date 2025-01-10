<?php

namespace App\Docs;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Blog API",
 *     description="Documentação da API para gerenciamento de Usuários, Posts e Tags.",
 *     @OA\Contact(
 *         email="suporte@exemplo.com",
 *         name="Equipe de Suporte"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Servidor Local"
 * )
 *
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     properties={
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="John Doe"),
 *         @OA\Property(property="email", type="string", example="johndoe@example.com"),
 *         @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *         @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-02T00:00:00Z")
 *     }
 * )
 */
class SwaggerDefinitions
{
    // Definições globais para o Swagger
}