<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$plugin   = isset( $GLOBALS['srk_portfolio_gallery'] ) ? $GLOBALS['srk_portfolio_gallery'] : null;
$settings = $plugin ? $plugin->get_settings() : array(
    'banner_id'       => 0,
    'banner_height'   => 280,
    'overlay_opacity' => 0.62,
    'accent_color'    => '#ff8a2a',
);

wp_enqueue_style( 'tpg-frontend' );
wp_enqueue_script( 'tpg-frontend' );

while ( have_posts() ) :
    the_post();

    $post_id       = get_the_ID();
    $banner_id     = absint( get_post_meta( $post_id, '_tpg_banner_id', true ) );
    $banner_id     = $banner_id ? $banner_id : absint( $settings['banner_id'] );
    $banner_url    = $banner_id ? wp_get_attachment_image_url( $banner_id, 'full' ) : '';
    $gallery_ids   = get_post_meta( $post_id, '_tpg_gallery_ids', true );
    $gallery_ids   = is_array( $gallery_ids ) ? array_map( 'absint', $gallery_ids ) : array();
    $portfolio_url = $plugin ? $plugin->get_portfolio_page_url() : home_url( '/' );

    $style_vars = sprintf(
        '--tpg-accent:%s;--tpg-banner-height:%dpx;--tpg-banner-opacity:%s;%s',
        esc_attr( $settings['accent_color'] ),
        absint( $settings['banner_height'] ),
        esc_attr( $settings['overlay_opacity'] ),
        $banner_url ? '--tpg-banner-image:url(' . esc_url( $banner_url ) . ');' : ''
    );
    ?>
    <div class="tpg-single-wrap" style="<?php echo esc_attr( $style_vars ); ?>">
        <section class="tpg-single-hero">
            <div class="tpg-single-hero-overlay"></div>
            <div class="tpg-single-hero-inner">
                <h1><?php the_title(); ?></h1>
                <div class="tpg-single-breadcrumbs">
                    <a href="<?php echo esc_url( $portfolio_url ); ?>"><?php esc_html_e( 'Back to portfolio', 'srk-portfolio-gallery' ); ?></a>
                    <span>//</span>
                    <span><?php the_title(); ?></span>
                </div>
            </div>
        </section>

        <main class="tpg-single-content">
            <?php if ( '' !== trim( get_the_content() ) ) : ?>
                <div class="tpg-single-description">
                    <?php the_content(); ?>
                </div>
            <?php endif; ?>

            <section class="tpg-gallery-section">
                <h2><?php esc_html_e( 'Image Gallery', 'srk-portfolio-gallery' ); ?></h2>
                <div class="tpg-heading-line"></div>

                <?php if ( $gallery_ids ) : ?>
                    <div class="tpg-single-gallery">
                        <?php
                        foreach ( $gallery_ids as $index => $attachment_id ) :
                            $full = wp_get_attachment_image_url( $attachment_id, 'full' );
                            if ( ! $full ) {
                                continue;
                            }
                            $caption = wp_get_attachment_caption( $attachment_id );
                            ?>
                            <button
                                type="button"
                                class="tpg-gallery-item tpg-open-lightbox"
                                data-lightbox-src="<?php echo esc_url( $full ); ?>"
                                data-lightbox-title="<?php echo esc_attr( $caption ? $caption : get_the_title() ); ?>"
                                data-lightbox-group="portfolio-<?php echo esc_attr( $post_id ); ?>"
                                aria-label="<?php echo esc_attr( sprintf( __( 'Open image %d', 'srk-portfolio-gallery' ), $index + 1 ) ); ?>"
                            >
                                <?php echo wp_get_attachment_image( $attachment_id, 'large', false, array( 'loading' => 'lazy' ) ); ?>
                                <span class="tpg-gallery-hover">
                                    <span class="tpg-gallery-zoom">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9.5 4a5.5 5.5 0 1 0 3.47 9.77l4.13 4.13 1.4-1.4-4.13-4.13A5.5 5.5 0 0 0 9.5 4Zm0 2a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7Zm-.75 1.25v1.5h-1.5v1.5h1.5v1.5h1.5v-1.5h1.5v-1.5h-1.5v-1.5h-1.5Z"/></svg>
                                    </span>
                                </span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p class="tpg-empty"><?php esc_html_e( 'No gallery images have been added yet.', 'srk-portfolio-gallery' ); ?></p>
                <?php endif; ?>
            </section>
        </main>

        <?php echo $plugin ? $plugin->lightbox_markup() : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </div>
    <?php
endwhile;

get_footer();
