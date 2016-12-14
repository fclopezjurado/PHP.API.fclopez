<?php // apiResultsDoctrine - src/swagger_def.php

use Swagger\Annotations as SWG;

/**
 * Global api definion
 *
 * @SWG\Swagger(
 *   schemes       = { "http" },
 *   host          = "localhost:8000",
 *   consumes      = { "application/json" },
 *   produces      = { "application/json" },
 *   basePath      = "/",
 *   @SWG\Info(
 *     title       = "MiW16 Results REST api",
 *     version     = "0.1.0",
 *     description = "[UPM] MiW16 Results REST api operations",
 *     license     = {
 *              "name": "MIT",
 *              "url": "./LICENSE.txt"
 *          }
 *   )
 * )
 */

/**
 * Message definition
 *
 * @SWG\Definition(
 *     definition="Message",
 *     required = { "code", "message" },
 *     example = {
 *          "code"    = "HTTP code",
 *          "message" = "Response Message"
 *     },
 *     @SWG\Property(
 *          property    = "code",
 *          description = "Response code",
 *          type        = "integer",
 *          format      = "int32"
 *     ),
 *     @SWG\Property(
 *          property    = "message",
 *          description = "Response message",
 *          type        = "string"
 *      )
 * )
 */
