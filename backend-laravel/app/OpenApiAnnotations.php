<?php
// app/OpenApiAnnotations.php

/**
 * @OA\Info(
 *     title="School Management System API",
 *     version="1.0.0",
 *     description="API endpoints for school management including buildings, rooms, tracks, departments, sections, and student assignments",
 *     @OA\Contact(
 *         email="admin@example.com",
 *         name="API Support"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="/api",
 *     description="API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

/**
 * This is just a placeholder file for annotations - it doesn't need class or namespace declarations
 * This file won't be loaded by your application but will be scanned for annotations
 */