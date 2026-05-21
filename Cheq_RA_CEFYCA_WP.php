<?php
/*
Plugin Name: Cheq_RA_CEFYCA_WP_LOMLOE_LFP
Plugin URI: https://cefyca.es
Description: Plugin para CHEQUEAR ficheros de la enseñanza concertada a partir del curso 2022-23
Version: 5.71
Author: Javier Soriano
Author URI: https://cefyca.es
License: GPL2
*/


//Por seguridad para que no se pueda ejecutar desde linea de navegador
defined('ABSPATH') or die("Bye bye");

include_once(ABSPATH . 'wp-includes/pluggable.php');
//Comprobamos el nivel de seguridad del usuario
/*if (! current_user_can ('manage_options')){
    wp_die ('No tienes suficientes permisos para acceder a esta página. Logeate por favor.');
}*/

//Asignación de constantes
//define('CEFY_RUTA',plugin_dir_path(__FILE__));

/*
*   Generamos los shortcodes
*/

//Añadimos los estilos

function cefy_chequeo_RA_LOMLOE_LFP_enqueue_scripts() {
 global $post;

 if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'cefy_chequeo_RA_LOMLOE_LFP') ) {
     
     wp_register_style('cefy_chequeo_RA_LOMLOE_LFP-stylesheet', plugins_url('public/css/main.css', __FILE__));
     wp_enqueue_style( 'cefy_chequeo_RA_LOMLOE_LFP-stylesheet' );
     wp_register_script('cefy_chequeo_RA_LOMLOE_LFP-script-variables', plugins_url('public/js/variables.js', __FILE__));
     wp_enqueue_script( 'cefy_chequeo_RA_LOMLOE_LFP-script-variables' );
     wp_register_script('cefy_chequeo_RA_LOMLOE_LFP-script', plugins_url('public/js/main.js', __FILE__));
     wp_enqueue_script( 'cefy_chequeo_RA_LOMLOE_LFP-script' );
 }
 if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'cefy_chequeo_RA_LOMLOE_LFP_HELP') ) {
     
    wp_register_style('cefy_chequeo_RA_LOMLOE_LFP-stylesheet', plugins_url('public/css/help.css', __FILE__));
    wp_enqueue_style( 'cefy_chequeo_RA_LOMLOE_LFP-stylesheet' );
    wp_register_style('bootstrap5_css','https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css');
    wp_enqueue_style('bootstrap5_css');
    wp_register_script('bootstrap5_js','https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js');
    wp_enqueue_script('bootstrap5_js');
    wp_register_script('cefy_chequeo_RA_LOMLOE_LFP-script-variables', plugins_url('public/js/variables.js', __FILE__));
    wp_enqueue_script( 'cefy_chequeo_RA_LOMLOE_LFP-script-variables' );
    wp_register_script('cefy_chequeo_RA_LOMLOE_LFP-script-help', plugins_url('public/js/help.js', __FILE__));
    wp_enqueue_script( 'cefy_chequeo_RA_LOMLOE_LFP-script-help' );
}
}
add_action( 'wp_enqueue_scripts', 'cefy_chequeo_RA_LOMLOE_LFP_enqueue_scripts');

//en $atts van los atributos que pasamos al shortcode y en $content lo que se mete entre las etiquetas del mismo
//ejemplo: [cefy_grafico idgrafico="343"]Texto interior[/cefy_grafico]
function cefy_chequeo_RA_LOMLOE_LFP_shortcode($atts = '', $content='') {

    $version = '6.07';
    
    $atributos = shortcode_atts([ 'curso' => '0'], $atts);
    $curso = $atributos['curso'];
    $codigo = "";
    $codigo .= '<div id="informeExpErroresFlot" class="cefy_ocultar_resultados"></div>';
    
    $codigo .= '<div class="contenedor" id="contenedor_cefy_chequeo_RA_LOMLOE_LFP">';
    $codigo .= '<h2>Chequeo curso: ' . $curso . ' - ' . ($curso + 1) . '</h2>';
    $codigo .= 'V PHP: <span id="version_cefy_cheq_RA">' . $version . '</span>';
    $codigo .= '<span id="version_cefy_cheq_RA_js"> </span><br>';
    $codigo .= '<p><input id="fileupload" type="file" name="fileupload" accept=".txt" required/>';
    $codigo .= '<p><button id="upload-button" onclick="cefy_chequeo_RA_uploadFile()"> Cargar archivo </button>';
    $codigo .= '<button id="ref-button" onclick="window.location.reload(true);">Refrescar página</button>';

    $codigo .= ''; //cierre form

    $codigo .= '<p id="informeResultadoFinal" style="margin: 0.5em; color: red; font-weight: bold;"></p>';
    
    $codigo .= '<h3>Informe de estructura:</h3>';
    $codigo .= '<div id="informeEstructuraExt" class="marcoInforme"><div id="informeEstructura" class="interiorInforme">';
    $codigo .= '</div></div>';

    $codigo .= '<h3>Informe de congruencia de evaluación:</h3>';
    $codigo .= '<div id="informeCongruenciaExt" class="marcoInforme"><div id="informeCongruencia" class="interiorInforme">';
    $codigo .= '</div></div>';

    //$codigo .= '<h3>Explicación de errores:</h3>';
    //$codigo .= '<div id="informeExpErroresExt" class="marcoInforme"><div id="informeExpErrores" class="interiorInforme">';
    //$codigo .= '</div></div>';

    
    
    $codigo .= '<h3>Resumen de asignaturas:</h3>';
    $codigo .= '<div id="informeResumenExt" class="marcoInforme"><div id="informeResumen" class="interiorInforme">';
    $codigo .= '</div></div>';


    $codigo .= '<h3>Análisis línea a línea:</h3>';
    $codigo .= '<div id="informeFallos"></div>';

    

    $codigo .= '</div>'; //cierre contenedor

    return $codigo;
        
    }

function cefy_chequeo_RA_LOMLOE_LFP_HELP_shortcode($atts = '', $content='') {
    $codigo = '<div id="Cheq_RA_CEFYCA_WP_HELP">Cargando ayudas</div>';

    return $codigo;
}

add_shortcode('cefy_chequeo_RA_LOMLOE_LFP', 'cefy_chequeo_RA_LOMLOE_LFP_shortcode'); 
add_shortcode('cefy_chequeo_RA_LOMLOE_LFP_HELP', 'cefy_chequeo_RA_LOMLOE_LFP_HELP_shortcode');


?>