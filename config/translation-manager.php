<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Routes group config
    |--------------------------------------------------------------------------
    |
    | The default group settings for the elFinder routes.
    |
    */
    'route' => [
        'prefix' => 'translations',
        'middleware' => [
            'web',
            'auth',
        ],
    ],

	/**
	 * Enable deletion of translations
	 *
	 * @type boolean
	 */
	'delete_enabled' => false,

	/**
	 * Exclude specific groups from Laravel Translation Manager. 
	 * This is useful if, for example, you want to avoid editing the official Laravel language files.
	 *
	 * @type array
	 *
	 * 	array(
	 *		'pagination',
	 *		'reminders',
	 *		'validation',
	 *	)
	 */
	'exclude_groups' => array(),

    /**
     * Exclude specific directories from Laravel Translation Manager.
     *
     * @type array
     *
     * 	array(
     *		'pagination',
     *		'reminders',
     *		'validation',
     *	)
     */
    'exclude_dir' => array(
        //'vendor',
    ),

	/**
	 * Export translations with keys output alphabetically.
	 */
	'sort_keys' => true,

);
