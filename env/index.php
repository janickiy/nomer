<?php
/**
 * The manifest of files that are local to specific environment.
 * This file returns a list of environments that the application
 * may be installed under. The returned data must be in the following
 * format:
 *
 * ```php
 * return [
 *     'environment name' => [
 *         'path' => 'directory storing the local files',
 *         'writable' => [
 *             // list of directories that should be set writable
 *         ],
 *     ],
 * ];
 * ```
 */
return [
    'alexander' => [
        'path' => 'alexander',
        'writable' => [
            'runtime',
            'assets',
        ],
        'executable' => [
            'yii',
        ],
    ],
    'vladimir' => [
        'path' => 'vladimir',
        'writable' => [
            'runtime',
            'assets',
        ],
        'executable' => [
            'yii',
        ],
    ],
    'production' => [
        'path' => 'production',
        'writable' => [
            'runtime',
            'assets',
        ],
        'executable' => [
            'yii',
        ],
    ],
];