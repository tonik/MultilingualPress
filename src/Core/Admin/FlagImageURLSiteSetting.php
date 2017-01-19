<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultilingualPress\Core\Admin;

use Inpsyde\MultilingualPress\Common\Setting\Site\SiteSettingViewModel;

/**
 * MultilingualPress "Flag image URL" site setting.
 *
 * @package Inpsyde\MultilingualPress\Core\Admin
 * @since   3.0.0
 */
final class FlagImageURLSiteSetting implements SiteSettingViewModel {

	/**
	 * @var string
	 */
	private $id = 'mlp-flag-image-url';

	/**
	 * Returns the markup for the site setting.
	 *
	 * @since 3.0.0
	 *
	 * @param int $site_id Site ID.
	 *
	 * @return string The markup for the site setting.
	 */
	public function markup( $site_id ) {

		// TODO: Adapt to be used on Edit Site as well.
		return sprintf(
			'<input type="url" name="%3$s" value="%1$s" class="regular-text" id="%2$s" placeholder="https://example.com/flag.png">',
			esc_url( '' ),
			esc_attr( $this->id ),
			esc_attr( SiteSettingsRepository::NAME_FLAG_IMAGE_URL )
		);
	}

	/**
	 * Returns the title of the site setting.
	 *
	 * @since 3.0.0
	 *
	 * @return string The markup for the site setting.
	 */
	public function title() {

		return sprintf(
			'<label for="%2$s">%1$s</label>',
			esc_html__( 'Flag image URL', 'multilingual-press' ),
			esc_attr( $this->id )
		);
	}
}
