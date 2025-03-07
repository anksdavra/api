<?php
/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="Croissant API",
 *         version="3.0.0",
 *         description="API documentation for the Croissant API"
 *     ),
 *     @OA\Server(
 *         url="{WP_HOME}",
 *         description="API Server",
 *         @OA\ServerVariable(
 *             serverVariable="WP_HOME",
 *             default="https://cms.local"
 *         )
 *     )
 * )
 */
class DummyGlobalAnnotation {}
