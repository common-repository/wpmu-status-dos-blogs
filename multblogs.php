<?php

/*
  Plugin Name: Status dos Blogs (Multisite)
  Description: Retorna a lista de blogs criados, no Wordpress 3 Multisite
  Version: 1.0
  Author: Claudney Santana <claudsan@gmail.com>
  Author URI: http://www.cucadigital.com.br/
 */

/**
 * LISTA OS BLOGS CRIADOS EXCLUIDO O BLOG MASTER
 * @global object $wpdb
 * @param array $args
 */
function listaBlogs($args) {
    global $wpdb;

    if (defined('MULTISITE')) {
        if (MULTISITE) {
            // Recuperando os posts
            $opcoes = get_option('listaQtdBlog');

            $qtd = (int) $opcoes['quantidade'];
            $totalBlogs = $wpdb->get_results("SELECT * FROM {$wpdb->blogs} where blog_id > 1 order by registered desc limit $qtd");

            $blogs = array();
            //$totalBlogs = get_blog_count();
            for ($i = 0; $i < count($totalBlogs); $i++) {
                $blogs[$i] = get_blog_details($totalBlogs[$i]->blog_id);
            }

            // Usando o modelo de widgets do tema
            echo $args['before_widget'];
            echo $args['before_title'] . $opcoes['titulo'] . $args['after_title'];

            echo "<ul>";

            // Listando os posts mais quentes
            for ($i = 0; $i < count($blogs); $i++) {
                echo "<li><a href='" . $blogs[$i]->siteurl . "'>{$blogs[$i]->blogname} ({$blogs[$i]->post_count})</a></li>";
            }

            echo "</ul>";
            echo $args['after_widget'];
        }
    }
}

/**
 * PAINEL DE CONFIGURACAO DO WIDGET
 */
function configurarListaBlog() {
    $msgNao = "<p>MULTISITE NÃO CONFIGURADO</p>";
    if (defined('MULTISITE')) {
        if (MULTISITE) {
            //INICIALIZA AS VARIÁVEIS NECESSÁRIAS
            $opcoes = array();

            //sALVANDO AS OPÇÕES
            if ($_POST['salvarConfigurarListaBlog']) {
                $opcoes['titulo'] = $_POST['tituloListaBlog'];
                $opcoes['quantidade'] = (int) $_POST['listaQtdBlog'];

                // VALOR PADRÃO, CASO NADA TENHA SIDO INFORMADO
                if (empty($opcoes['quantidade']))
                    $opcoes['quantidade'] = 5;

                update_option('listaQtdBlog', $opcoes);
            }

            //CARREGAR AS OPÇÕES DESSE WIDGET
            $opcoes = get_option('listaQtdBlog');

            //VALOR PADRÃO, CASO NADA TENHA SIDO INFORMADO
            if (empty($opcoes['quantidade']))
                $opcoes['quantidade'] = "5";

            //VALOR PADRÃO, CASO NADA TENHA SIDO INFORMADO
            if (empty($opcoes['titulo']))
                $opcoes['titulo'] = "Últimos Blogs";

            $titulo = $opcoes['titulo'];
            $qtdBlogs = $opcoes['quantidade'];

            //FORMULÁRIO
            echo <<<FORM
                    <input type="hidden" name="salvarConfigurarListaBlog" value="1" />

                    <p>
                      <label for="tituloListaBlog">Título:</label>
                      <input type="text" name="tituloListaBlog" maxlength="26" value="$titulo" class="widefat" />
                      <label for="listaQtdBlog">Quantidade:</label>
                      <input type="text" name="listaQtdBlog" maxlength="2" value="$qtdBlogs" class="widefat" />
                    </p>
FORM;
        }else {
            echo $msgNao;
        }
    } else {
        echo $msgNao;
    }
}

/**
 * CHAMADA DO WIDGET
 */
function listaBlogWidgets() {
    wp_register_sidebar_widget('wlista_blogs','Lista de Blogs', 'listaBlogs');
}

// ADICIONAR O CONTROLE AO WIDGET
wp_register_widget_control('lista_blogs','Lista de Blogs', 'configurarListaBlog');

// CARREGAR O WIDGET
add_action('widgets_init', 'listaBlogWidgets');

?>