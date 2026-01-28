<?php
/**
 * Comments Template
 *
 * @package TugasinWP
 * @since 1.0.0
 */

// Prevent direct access
if ( post_password_required() ) {
    return;
}
?>

<div id="comments" class="comments-area" style="margin-top: 60px; padding-top: 40px; border-top: 1px solid #e5e7eb;">

    <?php if ( have_comments() ) : ?>
        <h2 class="comments-title" style="font-size: 1.5rem; margin-bottom: 32px;">
            <?php
            $comment_count = get_comments_number();
            printf(
                esc_html( _nx( '%1$s Komentar', '%1$s Komentar', $comment_count, 'comments title', 'tugasin' ) ),
                number_format_i18n( $comment_count )
            );
            ?>
        </h2>

        <ol class="comment-list" style="list-style: none; padding: 0;">
            <?php
            wp_list_comments( array(
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 48,
            ) );
            ?>
        </ol>

        <?php
        the_comments_navigation();

        if ( ! comments_open() ) :
            ?>
            <p class="no-comments" style="color: var(--text-secondary); font-style: italic;">
                <?php esc_html_e( 'Komentar ditutup.', 'tugasin' ); ?>
            </p>
            <?php
        endif;

    endif;

    comment_form( array(
        'title_reply'        => esc_html__( 'Tinggalkan Komentar', 'tugasin' ),
        'class_submit'       => 'btn btn-primary',
        'label_submit'       => esc_html__( 'Kirim Komentar', 'tugasin' ),
    ) );
    ?>

</div>
