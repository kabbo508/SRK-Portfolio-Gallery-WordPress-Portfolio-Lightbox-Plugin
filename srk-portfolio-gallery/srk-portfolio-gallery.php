<?php
/**
 * Plugin Name: SRK Portfolio Gallery
 * Description: Responsive filterable portfolio gallery with category controls, hover actions, custom single portfolio pages, admin-managed banners, galleries, and lightbox navigation.
 * Version: 1.1.2
 * Author: srkpics
 * Author URI: https://sumonrahmankabbo.com/
 * Text Domain: srk-portfolio-gallery
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class SRK_Portfolio_Gallery {

    const VERSION      = '1.1.2';
    const POST_TYPE    = 'tpg_portfolio';
    const TAXONOMY     = 'tpg_portfolio_cat';
    const OPTION_GROUP = 'tpg_portfolio_settings';
    const OPTION_NAME  = 'tpg_settings';
    const NONCE_ACTION = 'tpg_save_portfolio_meta';

    public function __construct() {
        add_action( 'init', array( $this, 'register_content_types' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post_' . self::POST_TYPE, array( $this, 'save_portfolio_meta' ) );

        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );

        add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend_assets' ) );
        add_filter( 'single_template', array( $this, 'single_template' ) );
        add_filter( 'body_class', array( $this, 'body_class' ) );

        add_shortcode( 'srk_portfolio', array( $this, 'portfolio_shortcode' ) );

        add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( $this, 'portfolio_columns' ) );
        add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( $this, 'portfolio_column_content' ), 10, 2 );
    }

    public function register_content_types() {
        register_post_type(
            self::POST_TYPE,
            array(
                'labels' => array(
                    'name'               => __( 'Portfolio', 'srk-portfolio-gallery' ),
                    'singular_name'      => __( 'Portfolio Item', 'srk-portfolio-gallery' ),
                    'menu_name'          => __( 'Portfolio', 'srk-portfolio-gallery' ),
                    'add_new'            => __( 'Add New', 'srk-portfolio-gallery' ),
                    'add_new_item'       => __( 'Add New Portfolio Item', 'srk-portfolio-gallery' ),
                    'edit_item'          => __( 'Edit Portfolio Item', 'srk-portfolio-gallery' ),
                    'new_item'           => __( 'New Portfolio Item', 'srk-portfolio-gallery' ),
                    'view_item'          => __( 'View Portfolio Item', 'srk-portfolio-gallery' ),
                    'search_items'       => __( 'Search Portfolio', 'srk-portfolio-gallery' ),
                    'not_found'          => __( 'No portfolio items found.', 'srk-portfolio-gallery' ),
                    'not_found_in_trash' => __( 'No portfolio items found in Trash.', 'srk-portfolio-gallery' ),
                ),
                'public'              => true,
                'publicly_queryable'  => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'show_in_rest'        => true,
                'menu_icon'           => 'dashicons-format-gallery',
                'has_archive'         => false,
                'rewrite'             => array( 'slug' => 'portfolio' ),
                'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
                'exclude_from_search' => false,
            )
        );

        register_taxonomy(
            self::TAXONOMY,
            self::POST_TYPE,
            array(
                'labels' => array(
                    'name'          => __( 'Portfolio Categories', 'srk-portfolio-gallery' ),
                    'singular_name' => __( 'Portfolio Category', 'srk-portfolio-gallery' ),
                    'search_items'  => __( 'Search Categories', 'srk-portfolio-gallery' ),
                    'all_items'     => __( 'All Categories', 'srk-portfolio-gallery' ),
                    'edit_item'     => __( 'Edit Category', 'srk-portfolio-gallery' ),
                    'update_item'   => __( 'Update Category', 'srk-portfolio-gallery' ),
                    'add_new_item'  => __( 'Add New Category', 'srk-portfolio-gallery' ),
                    'new_item_name' => __( 'New Category Name', 'srk-portfolio-gallery' ),
                    'menu_name'     => __( 'Categories', 'srk-portfolio-gallery' ),
                ),
                'public'            => true,
                'show_ui'           => true,
                'show_admin_column' => true,
                'show_in_rest'      => true,
                'hierarchical'      => true,
                'rewrite'           => array( 'slug' => 'portfolio-category' ),
            )
        );
    }

    public function add_meta_boxes() {
        add_meta_box(
            'tpg_gallery_meta',
            __( 'Portfolio Gallery', 'srk-portfolio-gallery' ),
            array( $this, 'gallery_meta_box' ),
            self::POST_TYPE,
            'normal',
            'high'
        );

        add_meta_box(
            'tpg_banner_override',
            __( 'Banner Override', 'srk-portfolio-gallery' ),
            array( $this, 'banner_meta_box' ),
            self::POST_TYPE,
            'side',
            'default'
        );
    }

    public function gallery_meta_box( $post ) {
        wp_nonce_field( self::NONCE_ACTION, 'tpg_nonce' );

        $gallery_ids = get_post_meta( $post->ID, '_tpg_gallery_ids', true );
        $gallery_ids = is_array( $gallery_ids ) ? array_map( 'absint', $gallery_ids ) : array();

        echo '<div class="tpg-admin-gallery-wrap">';
        echo '<p>' . esc_html__( 'Upload or select multiple images. Drag thumbnails to control the exact frontend gallery order.', 'srk-portfolio-gallery' ) . '</p>';
        echo '<input type="hidden" id="tpg_gallery_ids" name="tpg_gallery_ids" value="' . esc_attr( implode( ',', $gallery_ids ) ) . '">';
        echo '<div id="tpg-gallery-preview" class="tpg-gallery-preview">';

        foreach ( $gallery_ids as $attachment_id ) {
            $thumb = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );
            if ( ! $thumb ) {
                continue;
            }
            echo '<div class="tpg-gallery-thumb" data-id="' . esc_attr( $attachment_id ) . '">';
            echo '<img src="' . esc_url( $thumb ) . '" alt="">';
            echo '<button type="button" class="tpg-remove-gallery-image" aria-label="' . esc_attr__( 'Remove image', 'srk-portfolio-gallery' ) . '">&times;</button>';
            echo '<span class="dashicons dashicons-move tpg-sort-handle"></span>';
            echo '</div>';
        }

        echo '</div>';
        echo '<p><button type="button" class="button button-primary" id="tpg-select-gallery">' . esc_html__( 'Select / Add Gallery Images', 'srk-portfolio-gallery' ) . '</button></p>';
        echo '<p class="description">' . esc_html__( 'Featured Image is used for the portfolio grid card. This gallery is used on the single portfolio page.', 'srk-portfolio-gallery' ) . '</p>';
        echo '</div>';
    }

    public function banner_meta_box( $post ) {
        $banner_id = absint( get_post_meta( $post->ID, '_tpg_banner_id', true ) );
        $banner    = $banner_id ? wp_get_attachment_image_url( $banner_id, 'medium' ) : '';

        echo '<div class="tpg-banner-picker">';
        echo '<input type="hidden" id="tpg_banner_id" name="tpg_banner_id" value="' . esc_attr( $banner_id ) . '">';
        echo '<div id="tpg-banner-preview">';
        if ( $banner ) {
            echo '<img src="' . esc_url( $banner ) . '" alt="">';
        }
        echo '</div>';
        echo '<p><button type="button" class="button" id="tpg-select-banner">' . esc_html__( 'Choose Banner', 'srk-portfolio-gallery' ) . '</button></p>';
        echo '<p><button type="button" class="button-link-delete" id="tpg-remove-banner">' . esc_html__( 'Remove Override', 'srk-portfolio-gallery' ) . '</button></p>';
        echo '<p class="description">' . esc_html__( 'Leave empty to use the global banner configured under Portfolio → Settings.', 'srk-portfolio-gallery' ) . '</p>';
        echo '</div>';
    }

    public function save_portfolio_meta( $post_id ) {
        if ( ! isset( $_POST['tpg_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tpg_nonce'] ) ), self::NONCE_ACTION ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $gallery_ids = array();
        if ( isset( $_POST['tpg_gallery_ids'] ) ) {
            $raw = sanitize_text_field( wp_unslash( $_POST['tpg_gallery_ids'] ) );
            foreach ( explode( ',', $raw ) as $id ) {
                $id = absint( $id );
                if ( $id && 'attachment' === get_post_type( $id ) ) {
                    $gallery_ids[] = $id;
                }
            }
        }
        update_post_meta( $post_id, '_tpg_gallery_ids', array_values( array_unique( $gallery_ids ) ) );

        $banner_id = isset( $_POST['tpg_banner_id'] ) ? absint( $_POST['tpg_banner_id'] ) : 0;
        if ( $banner_id && 'attachment' !== get_post_type( $banner_id ) ) {
            $banner_id = 0;
        }
        update_post_meta( $post_id, '_tpg_banner_id', $banner_id );
    }

    public function add_settings_page() {
        add_submenu_page(
            'edit.php?post_type=' . self::POST_TYPE,
            __( 'Portfolio Settings', 'srk-portfolio-gallery' ),
            __( 'Settings', 'srk-portfolio-gallery' ),
            'manage_options',
            'tpg-settings',
            array( $this, 'settings_page' )
        );
    }

    public function register_settings() {
        register_setting(
            self::OPTION_GROUP,
            self::OPTION_NAME,
            array(
                'type'              => 'array',
                'sanitize_callback' => array( $this, 'sanitize_settings' ),
                'default'           => array(),
            )
        );
    }

    public function sanitize_settings( $input ) {
        $output = array();

        $output['banner_id']        = isset( $input['banner_id'] ) ? absint( $input['banner_id'] ) : 0;
        $output['banner_height']    = isset( $input['banner_height'] ) ? min( 700, max( 180, absint( $input['banner_height'] ) ) ) : 280;
        $output['overlay_opacity']  = isset( $input['overlay_opacity'] ) ? max( 0, min( 0.95, floatval( $input['overlay_opacity'] ) ) ) : 0.62;
        $output['accent_color']     = isset( $input['accent_color'] ) ? sanitize_hex_color( $input['accent_color'] ) : '#ff8a2a';
        $output['grid_columns']     = isset( $input['grid_columns'] ) ? absint( $input['grid_columns'] ) : 3;
        $output['grid_gap']         = isset( $input['grid_gap'] ) ? min( 80, max( 0, absint( $input['grid_gap'] ) ) ) : 20;
        $output['portfolio_page']   = isset( $input['portfolio_page'] ) ? absint( $input['portfolio_page'] ) : 0;
        $output['filter_order']     = array();

        if ( isset( $input['filter_order'] ) ) {
            $raw_filter_order = sanitize_text_field( wp_unslash( $input['filter_order'] ) );

            foreach ( explode( ',', $raw_filter_order ) as $filter_key ) {
                $filter_key = trim( $filter_key );

                if ( 'all' === $filter_key ) {
                    $output['filter_order'][] = 'all';
                    continue;
                }

                if ( preg_match( '/^term:(\d+)$/', $filter_key, $matches ) ) {
                    $term_id = absint( $matches[1] );
                    $term    = get_term( $term_id, self::TAXONOMY );

                    if ( $term && ! is_wp_error( $term ) ) {
                        $output['filter_order'][] = 'term:' . $term_id;
                    }
                }
            }

            $output['filter_order'] = array_values( array_unique( $output['filter_order'] ) );
        }

        if ( ! in_array( $output['grid_columns'], array( 2, 3, 4 ), true ) ) {
            $output['grid_columns'] = 3;
        }

        if ( ! $output['accent_color'] ) {
            $output['accent_color'] = '#ff8a2a';
        }

        return $output;
    }

    public function settings_page() {
        $settings          = $this->get_settings();
        $banner            = ! empty( $settings['banner_id'] ) ? wp_get_attachment_image_url( $settings['banner_id'], 'large' ) : '';
        $all_filter_items  = $this->get_all_filter_items();
        $filter_order      = $this->get_filter_order( $settings );
        $selected_filters  = array();
        $available_filters = $all_filter_items;

        foreach ( $filter_order as $filter_key ) {
            if ( isset( $all_filter_items[ $filter_key ] ) ) {
                $selected_filters[ $filter_key ] = $all_filter_items[ $filter_key ];
                unset( $available_filters[ $filter_key ] );
            }
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Portfolio Settings', 'srk-portfolio-gallery' ); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields( self::OPTION_GROUP ); ?>

                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Global Single Page Banner', 'srk-portfolio-gallery' ); ?></th>
                        <td>
                            <input type="hidden" id="tpg_global_banner_id" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[banner_id]" value="<?php echo esc_attr( $settings['banner_id'] ); ?>">
                            <div id="tpg-global-banner-preview" class="tpg-settings-banner-preview">
                                <?php if ( $banner ) : ?>
                                    <img src="<?php echo esc_url( $banner ); ?>" alt="">
                                <?php endif; ?>
                            </div>
                            <p>
                                <button type="button" class="button" id="tpg-select-global-banner"><?php esc_html_e( 'Choose Banner Image', 'srk-portfolio-gallery' ); ?></button>
                                <button type="button" class="button-link-delete" id="tpg-remove-global-banner"><?php esc_html_e( 'Remove', 'srk-portfolio-gallery' ); ?></button>
                            </p>
                            <p class="description"><?php esc_html_e( 'Used on every single portfolio page unless an individual portfolio item has its own Banner Override.', 'srk-portfolio-gallery' ); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="tpg_banner_height"><?php esc_html_e( 'Banner Height', 'srk-portfolio-gallery' ); ?></label></th>
                        <td><input type="number" id="tpg_banner_height" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[banner_height]" value="<?php echo esc_attr( $settings['banner_height'] ); ?>" min="180" max="700"> px</td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="tpg_overlay_opacity"><?php esc_html_e( 'Banner Overlay Opacity', 'srk-portfolio-gallery' ); ?></label></th>
                        <td><input type="number" id="tpg_overlay_opacity" step="0.05" min="0" max="0.95" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[overlay_opacity]" value="<?php echo esc_attr( $settings['overlay_opacity'] ); ?>"></td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="tpg_accent_color"><?php esc_html_e( 'Accent Color', 'srk-portfolio-gallery' ); ?></label></th>
                        <td><input type="color" id="tpg_accent_color" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[accent_color]" value="<?php echo esc_attr( $settings['accent_color'] ); ?>"></td>
                    </tr>

                    <tr>
                        <th scope="row"><?php esc_html_e( 'Frontend Category Filters', 'srk-portfolio-gallery' ); ?></th>
                        <td>
                            <div class="tpg-filter-manager">
                                <div class="tpg-filter-manager-column">
                                    <h3><?php esc_html_e( 'Available Filters', 'srk-portfolio-gallery' ); ?></h3>
                                    <p class="description"><?php esc_html_e( 'Drag a category from here to the Frontend Filters list to show it.', 'srk-portfolio-gallery' ); ?></p>

                                    <ul id="tpg-available-filters" class="tpg-filter-sortable">
                                        <?php foreach ( $available_filters as $filter_key => $filter_item ) : ?>
                                            <li class="tpg-filter-sort-item" data-filter-key="<?php echo esc_attr( $filter_key ); ?>">
                                                <span class="dashicons dashicons-menu tpg-filter-drag" aria-hidden="true"></span>
                                                <span class="tpg-filter-sort-label"><?php echo esc_html( $filter_item['label'] ); ?></span>
                                                <span class="tpg-filter-sort-type"><?php echo esc_html( $filter_item['type'] ); ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>

                                <div class="tpg-filter-manager-column tpg-filter-manager-selected">
                                    <h3><?php esc_html_e( 'Frontend Filters — Drag to Set Exact Order', 'srk-portfolio-gallery' ); ?></h3>
                                    <p class="description"><?php esc_html_e( 'Only filters in this list appear on the frontend. Their top-to-bottom order is used exactly.', 'srk-portfolio-gallery' ); ?></p>

                                    <ul id="tpg-selected-filters" class="tpg-filter-sortable">
                                        <?php foreach ( $selected_filters as $filter_key => $filter_item ) : ?>
                                            <li class="tpg-filter-sort-item" data-filter-key="<?php echo esc_attr( $filter_key ); ?>">
                                                <span class="dashicons dashicons-menu tpg-filter-drag" aria-hidden="true"></span>
                                                <span class="tpg-filter-sort-label"><?php echo esc_html( $filter_item['label'] ); ?></span>
                                                <span class="tpg-filter-sort-type"><?php echo esc_html( $filter_item['type'] ); ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>

                            <input type="hidden"
                                   id="tpg_filter_order"
                                   name="<?php echo esc_attr( self::OPTION_NAME ); ?>[filter_order]"
                                   value="<?php echo esc_attr( implode( ',', $filter_order ) ); ?>">

                            <p class="description tpg-filter-manager-help">
                                <?php esc_html_e( 'ALL is also draggable and can be hidden. If ALL is hidden, the first visible category becomes the default active filter.', 'srk-portfolio-gallery' ); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="tpg_grid_columns"><?php esc_html_e( 'Default Grid Columns', 'srk-portfolio-gallery' ); ?></label></th>
                        <td>
                            <select id="tpg_grid_columns" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[grid_columns]">
                                <option value="2" <?php selected( $settings['grid_columns'], 2 ); ?>>2</option>
                                <option value="3" <?php selected( $settings['grid_columns'], 3 ); ?>>3</option>
                                <option value="4" <?php selected( $settings['grid_columns'], 4 ); ?>>4</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="tpg_grid_gap"><?php esc_html_e( 'Grid Gap', 'srk-portfolio-gallery' ); ?></label></th>
                        <td><input type="number" id="tpg_grid_gap" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[grid_gap]" value="<?php echo esc_attr( $settings['grid_gap'] ); ?>" min="0" max="80"> px</td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="tpg_portfolio_page"><?php esc_html_e( 'Portfolio Page', 'srk-portfolio-gallery' ); ?></label></th>
                        <td>
                            <?php
                            wp_dropdown_pages(
                                array(
                                    'name'              => self::OPTION_NAME . '[portfolio_page]',
                                    'id'                => 'tpg_portfolio_page',
                                    'selected'          => $settings['portfolio_page'],
                                    'show_option_none'  => __( '— Select page —', 'srk-portfolio-gallery' ),
                                    'option_none_value' => 0,
                                )
                            );
                            ?>
                            <p class="description"><?php esc_html_e( 'This page should contain the [srk_portfolio] shortcode. It is used for the Back to portfolio link on single pages.', 'srk-portfolio-gallery' ); ?></p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>

            <hr>
            <h2><?php esc_html_e( 'Portfolio Grid Shortcode', 'srk-portfolio-gallery' ); ?></h2>
            <p><code>[srk_portfolio]</code></p>
            <p><code>[srk_portfolio columns="3"]</code> &nbsp; <code>[srk_portfolio columns="4" gap="24"]</code></p>
        </div>
        <?php
    }

    public function admin_assets() {
        $screen = get_current_screen();
        if ( ! $screen ) {
            return;
        }

        $is_portfolio_editor = self::POST_TYPE === $screen->post_type;
        $is_settings         = 'tpg_portfolio_page_tpg-settings' === $screen->id;

        if ( ! $is_portfolio_editor && ! $is_settings ) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_script( 'jquery-ui-sortable' );

        wp_enqueue_style(
            'tpg-admin',
            plugin_dir_url( __FILE__ ) . 'assets/css/admin.css',
            array(),
            null
        );

        wp_enqueue_script(
            'tpg-admin',
            plugin_dir_url( __FILE__ ) . 'assets/js/admin.js',
            array( 'jquery', 'jquery-ui-sortable' ),
            self::VERSION,
            true
        );
    }

    public function register_frontend_assets() {
        wp_register_style(
            'tpg-frontend',
            plugin_dir_url( __FILE__ ) . 'assets/css/frontend.css',
            array(),
            null
        );

        wp_register_script(
            'tpg-frontend',
            plugin_dir_url( __FILE__ ) . 'assets/js/frontend.js',
            array(),
            self::VERSION,
            true
        );
    }

    public function get_settings() {
        $defaults = array(
            'banner_id'       => 0,
            'banner_height'   => 280,
            'overlay_opacity' => 0.62,
            'accent_color'    => '#ff8a2a',
            'grid_columns'    => 3,
            'grid_gap'        => 20,
            'portfolio_page'  => 0,
        );

        $saved    = get_option( self::OPTION_NAME, array() );
        $saved    = is_array( $saved ) ? $saved : array();
        $settings = wp_parse_args( $saved, $defaults );

        // Backward compatibility for v1.0.0.
        if ( ! array_key_exists( 'filter_order', $saved ) ) {
            $settings['filter_order'] = $this->get_default_filter_order();
        } elseif ( ! is_array( $settings['filter_order'] ) ) {
            $settings['filter_order'] = array();
        }

        return $settings;
    }

    public function get_all_filter_items() {
        $items = array(
            'all' => array(
                'label' => __( 'All', 'srk-portfolio-gallery' ),
                'type'  => __( 'Special Filter', 'srk-portfolio-gallery' ),
            ),
        );

        $terms = get_terms(
            array(
                'taxonomy'   => self::TAXONOMY,
                'hide_empty' => false,
                'orderby'    => 'name',
                'order'      => 'ASC',
            )
        );

        if ( is_wp_error( $terms ) ) {
            return $items;
        }

        foreach ( $terms as $term ) {
            $items[ 'term:' . $term->term_id ] = array(
                'label' => $term->name,
                'type'  => __( 'Category', 'srk-portfolio-gallery' ),
                'term'  => $term,
            );
        }

        return $items;
    }

    public function get_default_filter_order() {
        return array_keys( $this->get_all_filter_items() );
    }

    public function get_filter_order( $settings = null ) {
        if ( null === $settings ) {
            $settings = $this->get_settings();
        }

        $order = isset( $settings['filter_order'] ) && is_array( $settings['filter_order'] )
            ? $settings['filter_order']
            : array();

        $available = $this->get_all_filter_items();
        $clean     = array();

        foreach ( $order as $filter_key ) {
            $filter_key = sanitize_text_field( (string) $filter_key );

            if ( isset( $available[ $filter_key ] ) && ! in_array( $filter_key, $clean, true ) ) {
                $clean[] = $filter_key;
            }
        }

        return $clean;
    }

    public function get_frontend_filters( $settings = null ) {
        if ( null === $settings ) {
            $settings = $this->get_settings();
        }

        $available = $this->get_all_filter_items();
        $order     = $this->get_filter_order( $settings );
        $filters   = array();

        foreach ( $order as $filter_key ) {
            if ( ! isset( $available[ $filter_key ] ) ) {
                continue;
            }

            if ( 'all' === $filter_key ) {
                $filters[] = array(
                    'slug'  => 'all',
                    'label' => $available[ $filter_key ]['label'],
                );
                continue;
            }

            if ( empty( $available[ $filter_key ]['term'] ) ) {
                continue;
            }

            $term = $available[ $filter_key ]['term'];

            $filters[] = array(
                'slug'    => $term->slug,
                'label'   => $term->name,
                'term_id' => $term->term_id,
            );
        }

        return $filters;
    }

    public function portfolio_shortcode( $atts ) {
        $settings = $this->get_settings();

        $atts = shortcode_atts(
            array(
                'columns' => $settings['grid_columns'],
                'gap'     => $settings['grid_gap'],
                'limit'   => -1,
            ),
            $atts,
            'srk_portfolio'
        );

        $columns = absint( $atts['columns'] );
        $gap     = absint( $atts['gap'] );
        $limit   = intval( $atts['limit'] );

        if ( ! in_array( $columns, array( 2, 3, 4 ), true ) ) {
            $columns = 3;
        }
        $gap = min( 80, max( 0, $gap ) );

        $query = new WP_Query(
            array(
                'post_type'           => self::POST_TYPE,
                'post_status'         => 'publish',
                'posts_per_page'      => $limit,
                'orderby'             => array(
                    'menu_order' => 'ASC',
                    'date'       => 'DESC',
                ),
                'ignore_sticky_posts' => true,
            )
        );

        if ( ! $query->have_posts() ) {
            return '<p class="tpg-empty">' . esc_html__( 'No portfolio items found.', 'srk-portfolio-gallery' ) . '</p>';
        }

        wp_enqueue_style( 'tpg-frontend' );
        wp_enqueue_script( 'tpg-frontend' );

        $frontend_filters = $this->get_frontend_filters( $settings );

        ob_start();
        ?>
        <div class="tpg-portfolio-wrap" style="<?php echo esc_attr( '--tpg-columns:' . $columns . ';--tpg-gap:' . $gap . 'px;--tpg-accent:' . $settings['accent_color'] . ';' ); ?>">
            <?php if ( ! empty( $frontend_filters ) ) : ?>
                <div class="tpg-filter-bar" role="group" aria-label="<?php esc_attr_e( 'Portfolio filters', 'srk-portfolio-gallery' ); ?>">
                    <?php foreach ( $frontend_filters as $filter_index => $filter_item ) : ?>
                        <button
                            type="button"
                            class="tpg-filter<?php echo 0 === $filter_index ? ' is-active' : ''; ?>"
                            data-filter="<?php echo esc_attr( $filter_item['slug'] ); ?>"
                        ><?php echo esc_html( $filter_item['label'] ); ?></button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="tpg-grid">
                <?php
                while ( $query->have_posts() ) :
                    $query->the_post();

                    $post_id    = get_the_ID();
                    $term_slugs = wp_get_post_terms( $post_id, self::TAXONOMY, array( 'fields' => 'slugs' ) );
                    $term_slugs = is_wp_error( $term_slugs ) ? array() : $term_slugs;

                    $thumb_id = get_post_thumbnail_id( $post_id );

                    // If no Featured Image is set, use the first image from the saved Portfolio Gallery order.
                    if ( ! $thumb_id ) {
                        $gallery_ids = get_post_meta( $post_id, '_tpg_gallery_ids', true );
                        $gallery_ids = is_array( $gallery_ids ) ? array_values( array_filter( array_map( 'absint', $gallery_ids ) ) ) : array();

                        if ( ! empty( $gallery_ids ) ) {
                            $thumb_id = $gallery_ids[0];
                        }
                    }

                    $full_image = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'full' ) : '';
                    ?>
                    <article class="tpg-card" data-categories="<?php echo esc_attr( implode( ' ', $term_slugs ) ); ?>">
                        <div class="tpg-card-media">
                            <?php if ( $thumb_id ) : ?>
                                <?php echo wp_get_attachment_image( $thumb_id, 'large', false, array( 'class' => 'tpg-card-image', 'loading' => 'lazy' ) ); ?>
                            <?php else : ?>
                                <div class="tpg-card-placeholder"></div>
                            <?php endif; ?>

                            <div class="tpg-card-overlay">
                                <div class="tpg-card-actions">
                                    <?php if ( $full_image ) : ?>
                                        <button
                                            type="button"
                                            class="tpg-icon-button tpg-open-lightbox"
                                            data-lightbox-src="<?php echo esc_url( $full_image ); ?>"
                                            data-lightbox-title="<?php echo esc_attr( get_the_title() ); ?>"
                                            aria-label="<?php esc_attr_e( 'Zoom image', 'srk-portfolio-gallery' ); ?>"
                                        >
                                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9.5 4a5.5 5.5 0 1 0 3.47 9.77l4.13 4.13 1.4-1.4-4.13-4.13A5.5 5.5 0 0 0 9.5 4Zm0 2a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7Zm-.75 1.25v1.5h-1.5v1.5h1.5v1.5h1.5v-1.5h1.5v-1.5h-1.5v-1.5h-1.5Z"/></svg>
                                        </button>
                                    <?php endif; ?>

                                    <a class="tpg-icon-button" href="<?php the_permalink(); ?>" aria-label="<?php esc_attr_e( 'Open portfolio item', 'srk-portfolio-gallery' ); ?>">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10.59 13.41a1.98 1.98 0 0 0 2.82 0l3.59-3.59a2 2 0 1 0-2.83-2.83l-1.54 1.54-1.42-1.42 1.55-1.54a4 4 0 1 1 5.65 5.65l-3.59 3.6a3.99 3.99 0 0 1-5.65 0l-.17-.18 1.41-1.41.18.18Zm2.82-2.82a1.98 1.98 0 0 0-2.82 0L7 14.18a2 2 0 1 0 2.83 2.83l1.54-1.54 1.42 1.42-1.55 1.54a4 4 0 1 1-5.65-5.65l3.59-3.6a3.99 3.99 0 0 1 5.65 0l.17.18-1.41 1.41-.18-.18Z"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <h3 class="tpg-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    </article>
                <?php endwhile; ?>
            </div>
        </div>
        <?php
        wp_reset_postdata();

        echo $this->lightbox_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        return ob_get_clean();
    }

    public function lightbox_markup() {
        ob_start();
        ?>
        <div class="tpg-lightbox" aria-hidden="true">
            <button type="button" class="tpg-lightbox-close" aria-label="<?php esc_attr_e( 'Close lightbox', 'srk-portfolio-gallery' ); ?>">&times;</button>
            <button type="button" class="tpg-lightbox-prev" aria-label="<?php esc_attr_e( 'Previous image', 'srk-portfolio-gallery' ); ?>">&#10094;</button>

            <div class="tpg-lightbox-stage">
                <img class="tpg-lightbox-image" src="" alt="">
                <div class="tpg-lightbox-caption"></div>
            </div>

            <button type="button" class="tpg-lightbox-next" aria-label="<?php esc_attr_e( 'Next image', 'srk-portfolio-gallery' ); ?>">&#10095;</button>
        </div>
        <?php
        return ob_get_clean();
    }

    public function single_template( $single ) {
        if ( is_singular( self::POST_TYPE ) ) {
            $template = plugin_dir_path( __FILE__ ) . 'templates/single-tpg_portfolio.php';
            if ( file_exists( $template ) ) {
                return $template;
            }
        }

        return $single;
    }

    public function body_class( $classes ) {
        if ( is_singular( self::POST_TYPE ) ) {
            $classes[] = 'tpg-single-portfolio';
        }
        return $classes;
    }

    public function portfolio_columns( $columns ) {
        $new = array();
        foreach ( $columns as $key => $label ) {
            $new[ $key ] = $label;
            if ( 'title' === $key ) {
                $new['tpg_thumb'] = __( 'Image', 'srk-portfolio-gallery' );
            }
        }
        return $new;
    }

    public function portfolio_column_content( $column, $post_id ) {
        if ( 'tpg_thumb' !== $column ) {
            return;
        }
        if ( has_post_thumbnail( $post_id ) ) {
            echo get_the_post_thumbnail( $post_id, array( 80, 60 ), array( 'style' => 'width:80px;height:60px;object-fit:cover;' ) );
        } else {
            echo '&mdash;';
        }
    }

    public function get_portfolio_page_url() {
        $settings = $this->get_settings();
        if ( ! empty( $settings['portfolio_page'] ) ) {
            $url = get_permalink( $settings['portfolio_page'] );
            if ( $url ) {
                return $url;
            }
        }
        return home_url( '/' );
    }
}

$GLOBALS['srk_portfolio_gallery'] = new SRK_Portfolio_Gallery();

register_activation_hook(
    __FILE__,
    function() {
        $plugin = isset( $GLOBALS['srk_portfolio_gallery'] ) ? $GLOBALS['srk_portfolio_gallery'] : null;
        if ( $plugin && method_exists( $plugin, 'register_content_types' ) ) {
            $plugin->register_content_types();
        }

        if ( ! term_exists( 'Commercial', SRK_Portfolio_Gallery::TAXONOMY ) ) {
            wp_insert_term( 'Commercial', SRK_Portfolio_Gallery::TAXONOMY );
        }
        if ( ! term_exists( 'Residential', SRK_Portfolio_Gallery::TAXONOMY ) ) {
            wp_insert_term( 'Residential', SRK_Portfolio_Gallery::TAXONOMY );
        }
        if ( ! term_exists( 'Industrial', SRK_Portfolio_Gallery::TAXONOMY ) ) {
            wp_insert_term( 'Industrial', SRK_Portfolio_Gallery::TAXONOMY );
        }

        flush_rewrite_rules();
    }
);

register_deactivation_hook(
    __FILE__,
    function() {
        flush_rewrite_rules();
    }
);
