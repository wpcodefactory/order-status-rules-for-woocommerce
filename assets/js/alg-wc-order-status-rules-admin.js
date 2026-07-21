/**
 * Order Status Rules for WooCommerce - Admin JS
 *
 * @version 3.9.3
 * @since   3.9.3
 *
 * @author  WPFactory
 *
 * @todo    (v3.9.3) check indentation: "If the method changes the context, an extra level of indentation must be used"
 */

jQuery( document ).ready( function () {
	jQuery( '.alg-wc-osr-select-all' ).click( function ( event ) {
		event.preventDefault();
		jQuery( this )
			.closest( 'td' )
			.find( 'select.chosen_select' )
			.select2( 'destroy' )
			.find( 'option' )
			.prop( 'selected', 'selected' )
			.end()
			.select2();
		return false;
	} );
	jQuery( '.alg-wc-osr-deselect-all' ).click( function ( event ) {
		event.preventDefault();
		jQuery( this )
			.closest( 'td' )
			.find( 'select.chosen_select' )
			.val( '' )
			.change();
		return false;
	} );
} );
