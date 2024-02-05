<?php
namespace Jet_Engine_Listing_As_Filter;

class Elementor_Integration {

	public function __construct() {
		
		add_action(
			'elementor/element/jet-listing-grid/section_general/after_section_end',
			[ $this, 'register_controls' ]
		);

		add_action( 'elementor/element/after_add_attributes', [ $this, 'render_data_attributes' ] );

	}

	public function render_data_attributes( $widget ) {

		$settings = $widget->get_settings_for_display();

		if ( empty( $settings['jelaf_enabled'] ) ) {
			return;
		}

		if ( empty( $settings['jelaf_filter_id'] ) ) {
			return;
		}

		$filter_id = $settings['jelaf_filter_id'];
		$filter_id = is_array( $filter_id ) ? $filter_id[0] : $filter_id;
		$filter_type = ! empty( $settings['jelaf_filter_type'] ) ? $settings['jelaf_filter_type'] : 'select';
		$provider = ! empty( $settings['jelaf_provider'] ) ? $settings['jelaf_provider'] : 'jet-engine';
		$apply_type = ! empty( $settings['jelaf_apply_type'] ) ? $settings['jelaf_apply_type'] : 'ajax';
		$apply_on = ! empty( $settings['jelaf_apply_on'] ) ? $settings['jelaf_apply_on'] : 'value';
		$query_id = ! empty( $settings['jelaf_query_id'] ) ? $settings['jelaf_query_id'] : '';

		if ( 'submit' === $settings['jelaf_apply_on'] 
			&& in_array( $settings['jelaf_apply_type'], [ 'ajax', 'mixed' ] ) ) {
			$apply_type = $settings['jelaf_apply_type'] . '-reload';
		} else {
			$apply_type = $settings['jelaf_apply_type'];
		}

		$source     = get_post_meta( $filter_id, '_data_source', true );
		$query_type = false;
		$query_var  = '';

		if ( 'taxonomies' === $source ) {
			
			$tax        = get_post_meta( $filter_id, '_source_taxonomy', true );
			$query_type = 'tax_query';
			$query_var  = $tax;

			$custom_query_var = false;

			if ( filter_var( get_post_meta( $filter_id, '_is_custom_query_var', true ), FILTER_VALIDATE_BOOLEAN ) ) {
				$custom_query_var = get_post_meta( $filter_id, '_custom_query_var', true );
			}

			if ( $custom_query_var ) {
				$query_type = 'meta_query';
				$query_var  = $custom_query_var;
			}

		} else {
			$query_type = 'meta_query';
			$query_var  = get_post_meta( $filter_id, '_query_var', true );
		}

		$widget->add_render_attribute( '_wrapper', 'data-filter-id', $filter_id );
		$widget->add_render_attribute( '_wrapper', 'data-smart-filter', $filter_type );
		$widget->add_render_attribute( '_wrapper', 'data-content-provider', $provider );
		$widget->add_render_attribute( '_wrapper', 'data-apply-type', $apply_type );
		$widget->add_render_attribute( '_wrapper', 'data-apply-on', $apply_on );
		$widget->add_render_attribute( '_wrapper', 'data-query-id', $query_id );
		$widget->add_render_attribute( '_wrapper', 'data-query-type', $query_type );
		$widget->add_render_attribute( '_wrapper', 'data-query-var', $query_var );

		$widget->add_render_attribute( '_wrapper', 'class', 'jet-smart-filters-custom-listing' );

		$this->enqueue_assets();

	}

	public function enqueue_assets() {
		
		jet_smart_filters()->set_filters_used();
		
		if ( did_action( 'wp_head' ) ) {
			add_action( 'wp_footer', array( $this, 'print_assets' ), 999 );
		} else {
			$this->print_assets();
		}
	}

	public function print_assets() {
		?>
		<script>
		( function( $ ) {

			"use strict";

			const initListingFilter = function() {


				window.JetSmartFilters.filtersList.JetEngineCustomListingFilter = 'jet-smart-filters-custom-listing';
				window.JetSmartFilters.filters.JetEngineCustomListingFilter = class JetEngineCustomListingFilter extends window.JetSmartFilters.filters.Select {

					constructor( $container ) {

						const $filterListing = $container.find( '.jet-listing-grid' );
					
						$filterListing.addClass( 'jet-select' );
												
						$filterListing.data( 'filter-id', $container.data( 'filter-id' ) );
						$filterListing.attr( 'data-filter-id', $container.data( 'filter-id' ) );
						$filterListing.data( 'smart-filter', $container.data( 'smart-filter' ) );
						$filterListing.attr( 'data-smart-filter', $container.data( 'smart-filter' ) );
						$filterListing.data( 'content-provider', $container.data( 'content-provider' ) );
						$filterListing.attr( 'data-content-provider', $container.data( 'content-provider' ) );
						$filterListing.data( 'apply-type', $container.data( 'apply-type' ) );
						$filterListing.attr( 'data-apply-type', $container.data( 'apply-type' ) );
						$filterListing.data( 'apply-on', $container.data( 'apply-on' ) );
						$filterListing.attr( 'data-apply-on', $container.data( 'apply-on' ) );
						$filterListing.data( 'query-id', $container.data( 'query-id' ) );
						$filterListing.attr( 'data-query-id', $container.data( 'query-id' ) );
						$filterListing.data( 'query-type', $container.data( 'query-type' ) );
						$filterListing.attr( 'data-query-type', $container.data( 'query-type' ) );
						$filterListing.data( 'query-var', $container.data( 'query-var' ) );
						$filterListing.attr( 'data-query-var', $container.data( 'query-var' ) );

						super( $container );
						
						this.$select = $filterListing.find( '.jet-listing-grid__item' );
						this.isSelect = false;
						this.canDeselect = true;

						if ( 'checkboxes' === this.$filter.data( 'smart-filter' ) ) {
							this.isMultiple = true;
						} else {
							this.isMultiple = false;
						}
						
						this.$select.each( ( index, el ) => {
							el.setAttribute( 'value', el.dataset.postId );
						} );
						
						this.processData();
						this.initEvent();
						
						window.JetSmartFilters.events.subscribe( 'ajaxFilters/updated', ( provider, queryId, response ) => {
							if ( 'jet-engine' === provider && queryId === this.$container.attr( 'id' ) ) {
								
								const $filterListing = this.$container.find( '.jet-listing-grid' );
					
								$filterListing.addClass( 'jet-select' );

								$filterListing.data( 'filter-id', $container.data( 'filter-id' ) );
								$filterListing.attr( 'data-filter-id', $container.data( 'filter-id' ) );
								$filterListing.data( 'smart-filter', $container.data( 'smart-filter' ) );
								$filterListing.attr( 'data-smart-filter', $container.data( 'smart-filter' ) );
								$filterListing.data( 'content-provider', $container.data( 'content-provider' ) );
								$filterListing.attr( 'data-content-provider', $container.data( 'content-provider' ) );
								$filterListing.data( 'apply-type', $container.data( 'apply-type' ) );
								$filterListing.attr( 'data-apply-type', $container.data( 'apply-type' ) );
								$filterListing.data( 'apply-on', $container.data( 'apply-on' ) );
								$filterListing.attr( 'data-apply-on', $container.data( 'apply-on' ) );
								$filterListing.data( 'query-id', $container.data( 'query-id' ) );
								$filterListing.attr( 'data-query-id', $container.data( 'query-id' ) );
								$filterListing.data( 'query-type', $container.data( 'query-type' ) );
								$filterListing.attr( 'data-query-type', $container.data( 'query-type' ) );
								$filterListing.data( 'query-var', $container.data( 'query-var' ) );
								$filterListing.attr( 'data-query-var', $container.data( 'query-var' ) );
								
								this.$filter = $filterListing;
								
								this.$select = this.$container.find( '.jet-listing-grid__item' );
								
								this.$select.each( ( index, el ) => {
									el.setAttribute( 'value', el.dataset.postId );
								} );
								
								this.addFilterChangeEvent();
							}
						} );
					}

					addFilterChangeEvent() {
												
						this.$select.on( 'click', evt => {

							const $radioItem = jQuery( evt.target ).closest( '.jet-listing-grid__item' );
							
							if ( ! this.isMultiple ) {
								this.$select.filter('[is-checked="1"]').attr( 'is-checked', null );
							}
														
							if ( this.dataValue != $radioItem.attr( 'value' ) ) {
								$radioItem.attr( 'is-checked', 1 );
							} else {
								$radioItem.attr( 'is-checked', null );
							}

							this.processData();
							this.wasСhanged();

						});
					}

					removeChangeEvent() {
						this.$select.off();
					}

					processData() {

						this.dataValue = this.$selected.attr( 'value' );
						
						if (!this.dataValue)
							this.checkAllOption();

						if (this.additionalFilterSettings)
							this.additionalFilterSettings.dataUpdated();
					}

					setData(newData) {
						this.reset();

						if (!newData)
							return;

						const $item = this.getItemByValue(newData);

						if ( $item ) {
							$item.attr( 'is-checked', 1 );
						}

						this.processData();
					}

					reset() {
						this.$select.attr( 'is-checked', null );
						this.processData();
					}

					get activeValue() {
						const $item = this.getItemByValue( this.data );

						if ( $item ) {
							if ( $item.find( '.is-label' ).length ) {
								return $item.find( '.is-label' ).text();
							} else {
								return $item.attr( 'value' );
							}
						}
					}

					get $selected() {
						return this.$select.filter('[is-checked="1"]');
					}

					// Additional methods
					getItemByValue(value) {

						let $item = false;

						$item = this.$select.filter('[value="' + value + '"]');

						return $item;
					}

				};
			}

			document.addEventListener( 'jet-smart-filters/before-init', ( e ) => {
				initListingFilter();
			});

		}( jQuery ) );
		</script>
		<?php
	}

	public function register_controls( $widget ) {

		$widget->start_controls_section(
			'jelaf_section',
			array(
				'label' => __( 'Listing as Filter', 'jet-engine' ),
			)
		);

		$widget->add_control(
			'jelaf_enabled',
			array(
				'type'           => \Elementor\Controls_Manager::SWITCHER,
				'label'          => __( 'Enable', 'jet-engine' ),
				'render_type'    => 'template',
				'style_transfer' => false,
			)
		);

		$widget->add_control(
			'jelaf_filter_id',
			array(
				'label'       => __( 'Select filter', 'jet-smart-filters' ),
				'label_block' => true,
				'type'        => 'jet-query',
				'multiple'    => false,
				'default'     => '',
				'query_type'  => 'post',
				'query'       => array(
					'post_type'      => jet_smart_filters()->post_type->slug(),
					'posts_per_page' => -1,
					'meta_query'     => array(
						array(
							'key'     => '_filter_type',
							'value'   => [ 'select', 'radio', 'checkboxes' ],
							'compare' => 'IN',
						),
					)
				),
				'condition' => array(
					'jelaf_enabled' => 'yes',
				),
			)
		);

		$widget->add_control(
			'jelaf_filter_type',
			array(
				'label'   => __( 'Filter type', 'jet-smart-filters' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'select',
				'options' => array(
					'select'     => 'Single select',
					'checkboxes' => 'Multi select',
				),
				'condition' => array(
					'jelaf_enabled' => 'yes',
				),
			)
		);

		$widget->add_control(
			'jelaf_provider',
			array(
				'label'   => __( 'This filter for', 'jet-smart-filters' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => jet_smart_filters()->data->content_providers(),
				'condition' => array(
					'jelaf_enabled' => 'yes',
				),
			)
		);

		$widget->add_control(
			'jelaf_epro_posts_notice',
			array(
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw'  => __( 'Please set <b>jet-smart-filters</b> into Query ID option of Posts widget you want to filter', 'jet-smart-filters' ),
				'condition' => array(
					'jelaf_enabled' => 'yes',
					'jelaf_content_provider' => array( 'epro-posts', 'epro-portfolio' ),
				),
			)
		);

		$widget->add_control(
			'jelaf_apply_type',
			array(
				'label'   => __( 'Apply type', 'jet-smart-filters' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'ajax',
				'options' => array(
					'ajax'   => __( 'AJAX', 'jet-smart-filters' ),
					'reload' => __( 'Page reload', 'jet-smart-filters' ),
					'mixed'  => __( 'Mixed', 'jet-smart-filters' ),
				),
				'condition' => array(
					'jelaf_enabled' => 'yes',
				),
			)
		);

		$widget->add_control(
			'jelaf_apply_on',
			array(
				'label'     => __( 'Apply on', 'jet-smart-filters' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'value',
				'options'   => array(
					'value'  => __( 'Value change', 'jet-smart-filters' ),
					'submit' => __( 'Click on apply button', 'jet-smart-filters' ),
				),
				'condition' => array(
					'jelaf_enabled' => 'yes',
					'jelaf_apply_type' => array( 'ajax', 'mixed' ),
				),
			)
		);

		$widget->add_control(
			'jelaf_query_id',
			array(
				'label'       => esc_html__( 'Query ID', 'jet-smart-filters' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'description' => __( 'Set unique query ID if you use multiple widgets of same provider on the page. Same ID you need to set for filtered widget.', 'jet-smart-filters' ),
				'condition' => array(
					'jelaf_enabled' => 'yes',
				),
			)
		);

		$widget->end_controls_section();

	}

}
