<?php
/**
 * Mlp_Term_Connector
 *
 * @version 2014.09.18
 * @author  Inpsyde GmbH, toscho
 * @license GPL
 */
class Mlp_Term_Connector {

	/**
	 * @type Inpsyde_Nonce_Validator_Interface
	 */
	private $nonce;

	/**
	 * @type string
	 */
	private $taxonomies;

	/**
	 * @type Mlp_Content_Relations_Interface
	 */
	private $content_relations;

	/**
	 * @type int
	 */
	private $current_site_id;

	/**
	 * @type array
	 */
	private $post_data;

	/**
	 * @param Mlp_Content_Relations_Interface   $content_relations
	 * @param Inpsyde_Nonce_Validator_Interface $nonce
	 * @param array                             $taxonomies
	 * @param Array                             $post_data
	 */
	public function __construct(
		Mlp_Content_Relations_Interface   $content_relations,
		Inpsyde_Nonce_Validator_Interface $nonce,
		Array                             $taxonomies,
		Array                             $post_data
	) {

		$this->nonce             = $nonce;
		$this->taxonomies        = $taxonomies;
		$this->content_relations = $content_relations;
		$this->current_site_id   = get_current_blog_id();
		$this->post_data         = $post_data;
	}

	/**
	 * Handle term changes.
	 *
	 * @wp-hook create_term
	 * @wp-hook delete_term
	 * @wp-hook edit_term
	 * @param   int    $term_id  Term ID. Not used.
	 * @param   int    $term_taxonomy_id  Term taxonomy ID.
	 * @param   string $taxonomy Taxonomy slug.
	 * @return  bool
	 */
	public function change_term_relationships(
		/** @noinspection PhpUnusedParameterInspection */
		$term_id, $term_taxonomy_id, $taxonomy
	) {

		if ( ! $this->is_valid_request( $taxonomy ) )
			return FALSE;

		$success = FALSE;
		$current_filter  = current_filter();

		if ( is_callable( array ( $this, $current_filter ) ) ) {

			/**
			 * Runs before the terms are changed.
			 *
			 * @param int    $term_taxonomy_id Term taxonomy ID.
			 * @param string $taxonomy         Taxonomy name.
			 * @param string $current_filter   Current filter.
			 */
			do_action(
				'mlp_before_term_synchronization',
				$term_taxonomy_id,
				$taxonomy,
				$current_filter
			);

			$success = call_user_func( array ( $this, $current_filter ), $term_taxonomy_id );

			/**
			 * Runs after the terms have been changed.
			 *
			 * @param int    $term_taxonomy_id Term taxonomy ID.
			 * @param string $taxonomy         Taxonomy name.
			 * @param string $current_filter   Current filter.
			 * @param bool   $success          Denotes whether or not the database was changed.
			 */
			do_action(
				'mlp_after_term_synchronization',
				$term_taxonomy_id,
				$taxonomy,
				$current_filter,
				$success
			);
		}

		return $success;
	}

	/**
	 * Handle term creations.
	 *
	 * @param   int    $term_taxonomy_id    Term taxonomy ID.
	 * @return  bool
	 */
	public function create_term( $term_taxonomy_id ) {

		$success = FALSE;

		foreach ( $this->post_data as $target_site_id => $target_term_id ) {

			if ( empty ( $target_term_id ) )
				continue;

			if ( $this->content_relations->set_relation(
				$this->current_site_id,
				$target_site_id,
				$term_taxonomy_id,
				$target_term_id,
				'term'
				)
			)
				$success = TRUE;
		}

		return $success;
	}

	/**
	 * Handle term deletions.
	 *
	 * @param   int     $term_taxonomy_id Term taxonomy ID.
	 * @return bool
	 */
	public function delete_term( $term_taxonomy_id ) {

		$result = $this->content_relations->delete_relation(
			 $this->current_site_id,
			 0,
			 $term_taxonomy_id,
			 0,
			 'term'
		);

		return $result;
	}

	/**
	 * Handle term edits.
	 *
	 * @param   int    $term_taxonomy_id    Term taxonomy ID.
	 * @return  bool
	 */
	public function edit_term( $term_taxonomy_id ) {

		$success  = FALSE;
		$existing = $this->content_relations->get_relations(
			$this->current_site_id,
			$term_taxonomy_id,
			'term'
		);

		foreach ( $this->post_data as $target_site_id => $target_term_id ) {

			$update = $this->update_terms(
				$existing,
				$term_taxonomy_id,
				$target_site_id,
				(int) $target_term_id
			);

			if ( $update )
				$success = TRUE;
		}

		return $success;
	}

	/**
	 * @param  array $existing
	 * @param  int   $source_term_id
	 * @param  int   $target_site_id
	 * @param  int   $target_term_id
	 * @return bool  TRUE when something has been changed
	 */
	private function update_terms(
		Array $existing,
		$source_term_id,
		$target_site_id,
		$target_term_id
	) {

		if ( isset ( $existing[ $target_site_id ] )
			&& $existing[ $target_site_id ] === $target_term_id
		)
			return TRUE;

		if ( 0 !== $target_term_id ) {

			return $this->content_relations->set_relation(
				$this->current_site_id,
				$target_site_id,
				$source_term_id,
				$target_term_id,
				'term'
			);
		}

		$translation_ids = $this->content_relations->get_translation_ids(
			$this->current_site_id,
			$target_site_id,
			$source_term_id,
			$target_term_id,
			'term'
		);

		return $this->content_relations->delete_relation(
			$translation_ids[ 'ml_source_blogid' ],
			$target_site_id,
			$translation_ids[ 'ml_source_elementid' ],
			0,
			'term'
		);
	}

	/**
	 * @param string $taxonomy
	 * @return bool
	 */
	private function is_valid_request( $taxonomy ) {

		if ( ! $this->nonce->is_valid() )
			return FALSE;

		if ( ! in_array( $taxonomy, $this->taxonomies ) )
			return FALSE;

		return TRUE;
	}

}
