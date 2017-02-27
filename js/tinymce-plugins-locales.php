<?php
/**
 * Locales for our TinyMCE plugins.
 *
 * @author  Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-downloads_plugin
 * @link https://www.gavick.com/blog/wordpress-tinymce-custom-buttons
 */
 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
 
if ( ! class_exists( '_WP_Editors' ) ) {
    require( ABSPATH . WPINC . '/class-wp-editor.php' );
}
 
function odwpdp_tinymce_plugins_locales() {
    $strings = array(
        'btn_tooltip' => __( 'Soubory ke stažení', ODWPDP_SLUG ),
        'dlg_title'   => __( 'Vložte soubory ke stažení', ODWPDP_SLUG ),
        'input1_lbl'  => __( 'Název', ODWPDP_SLUG ),
        'input1_val'  => __( 'Soubory ke stažení', ODWPDP_SLUG ),
        'input2_lbl'  => __( 'Počet položek', ODWPDP_SLUG ),
        'input3_lbl'  => __( 'Řadit dle', ODWPDP_SLUG ),
        'input3_opt1' => __( 'Názvu', ODWPDP_SLUG ),
        'input3_opt2' => __( 'Data vyvěšení', ODWPDP_SLUG ),
        'input3_opt3' => __( 'Data sejmutí', ODWPDP_SLUG ),
        'input3_opt4' => __( 'Počtu stažení', ODWPDP_SLUG ),
        'input4_lbl'  => __( 'Řadit', ODWPDP_SLUG ),
        'input4_opt1' => __( 'Sestupně', ODWPDP_SLUG ),
        'input4_opt2' => __( 'Vzestupně', ODWPDP_SLUG ),
        'input5_lbl'  => __( 'Zobrazit název?', ODWPDP_SLUG ),
        'input6_lbl'  => __( 'Umožnit ruční řazení?', ODWPDP_SLUG ),
        'input7_lbl'  => __( 'Zobrazit stránkování?', ODWPDP_SLUG ),
        'input8_lbl'  => __( 'Identifikátor', ODWPDP_SLUG ),
    );

    $locale = _WP_Editors::$mce_locale;
    $translated = 'tinyMCE.addI18n("' . $locale . '.odwpdp", ' . json_encode( $strings ) . ");\n";

    return $translated;
}
 
$strings = odwpdp_tinymce_plugins_locales();