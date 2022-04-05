<?php
/**
 * Server-side rendering of the `core/comment-date` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/comment-date` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Return the post comment's date.
 */
function render_block_core_comment_date( $attributes, $content, $block ) {
	if ( ! isset( $block->context['commentId'] ) ) {
		return '';
	}

	// Comment Date Template.
	if ( 0 === $block->context['commentId'] ) {
		// Block attributes are known at server-render time, so we can hard-wire them into the template.
		$attrs = '{ ';
		if ( isset( $attributes['format'] ) ) {
			// TODO: Translate format to JS-style.
			$attrs .= 'format: "' . $attributes['format'] . '" ';
		}
		$attrs .= '}';
		return "\${ wpCommentDate( context, $attrs ) }";
	}

	$comment = get_comment( $block->context['commentId'] );
	if ( empty( $comment ) ) {
		return '';
	}

	$wrapper_attributes = get_block_wrapper_attributes();
	$formatted_date     = get_comment_date(
		isset( $attributes['format'] ) ? $attributes['format'] : '',
		$comment
	);
	$link               = get_comment_link( $comment );

	if ( ! empty( $attributes['isLink'] ) ) {
		$formatted_date = sprintf( '<a href="%1s">%2s</a>', esc_url( $link ), $formatted_date );
	}

	return sprintf(
		'<div %1$s><time datetime="%2$s">%3$s</time></div>',
		$wrapper_attributes,
		esc_attr( get_comment_date( 'c', $comment ) ),
		$formatted_date
	);
}

/**
 * Registers the `core/comment-date` block on the server.
 */
function register_block_core_comment_date() {
	register_block_type_from_metadata(
		__DIR__ . '/comment-date',
		array(
			'render_callback' => 'render_block_core_comment_date',
		)
	);
}
add_action( 'init', 'register_block_core_comment_date' );

function define_comment_date_custom_element() {
    ?>
		<template id="wp-comment-date">
			<div><time><slot></slot></time></div>
		</template>
		<script>
		customElements.define( 'wp-comment-date',
			class extends HTMLElement {
				constructor() {
					super();
					const template = document.getElementById( 'wp-comment-date' );
					const templateContent = template.content;

					const shadowRoot = this.attachShadow( { mode: 'open' } );
					shadowRoot.appendChild( templateContent.cloneNode( true ) );
					const slotDate = shadowRoot.querySelector( 'slot' ).assignedNodes()[ 0 ];
					const datetime = new Date( slotDate.textContent );
					const timeElement = shadowRoot.querySelector( 'time' );

					if ( datetime ) {
						timeElement.setAttribute ( 'datetime', datetime.toISOString() );
					}
				}
			}
		);

		function wpCommentDate( { timestamp }, { format } ) {
			const dateOptions = { // TODO: Format datetime according to `format` arg.
					year: 'numeric', month: 'long', day: 'numeric',
					hour: 'numeric', minute: 'numeric'
			};
			const datetime = timestamp.toLocaleString( 'en', dateOptions );

			return `<div><time datetime="${ timestamp.toISOString() }">${ datetime }</time></div>`;
		}
		</script>
    <?php
}
add_action('wp_head', 'define_comment_date_custom_element');