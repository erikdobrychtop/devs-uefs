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
 * @OA\Schema(
 *     schema="Tag",
 *     type="object",
 *     title="Tag",
 *     description="Representa uma tag associada a posts",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Laravel"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-02T15:00:00Z")
 * )
 * @OA\Schema(
 *     schema="Post",
 *     type="object",
 *     title="Post",
 *     description="Representa um post criado pelo usuário",
 *     @OA\Property(property="id", type="integer", example=1, description="ID do post"),
 *     @OA\Property(property="title", type="string", example="Meu Primeiro Post", description="Título do post"),
 *     @OA\Property(property="content", type="string", example="Este é o conteúdo do post.", description="Conteúdo do post"),
 *     @OA\Property(property="user_id", type="integer", example=1, description="ID do usuário que criou o post"),
 *     @OA\Property(
 *         property="tags",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Tag"),
 *         description="Lista de tags associadas ao post"
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T12:00:00Z", description="Data de criação do post"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-02T15:00:00Z", description="Data da última atualização do post")
 * )
 */

class SwaggerDefinitions
{
    // Definições globais para o Swagger
}