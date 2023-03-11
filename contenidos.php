<?php
require_once "config/Autoload.php";
Config\Autoload::run();
$template = new Clases\TemplateSite();
$f = new Clases\PublicFunction();
$contenidos = new Clases\Contenidos();
$categoria = new Clases\Categorias();


$filter = [];
$get = $f->antihackMulti($_GET);
foreach ($get as $key => $get_) {
    (isset($_GET[$key]) && $key != 'pagina') ?  $filter[] = "contenidos.$key = '" . $get_ . "'" : '';
}
$area = isset($get['area']) ? $get['area'] : '';

$pagina = isset($_GET['pagina']) ? $f->antihack_mysqli($_GET['pagina']) : 1;
$limite = 12;
$data = [
    "filter" => $filter,
    "images" => 'single',
    "category" => true,
    "subcategory" => true,
    "limit" => ($limite * ($pagina - 1)) . "," . $limite
];
#List de contenidos (al ser único el título, solo trae un resultado)
$contenidoData = $contenidos->list($data, $_SESSION["lang"], false);
#List de categorias
$categoriaList = $categoria->list(["filter" => "area = '$area'"], 'titulo ASC', '', "es", false,  false);

if (empty($contenidoData)) $f->headerMove(URL);
#Si se encontro el contenido se almacena y sino se redirecciona al inicio

$paginador = $contenidos->paginador(URL . '/c/' . $area, $filter, $limite, $pagina, 1);

// var_dump($paginador);
#Información de cabecera
$template->set("title", $contenidoData[array_key_first($contenidoData)]['area']["data"]['titulo'] . " | " . TITULO);
$template->set("description", "");
$template->set("keywords", "");
$template->set("imagen", LOGO);
$template->themeInit();
?>
<div class="gen-breadcrumb" style="background-image: url('images/background/asset-25.jpeg');">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-12">
                <nav aria-label="breadcrumb">
                    <div class="gen-breadcrumb-title">
                        <h1>
                            <?= $contenidoData[array_key_first($contenidoData)]['area']["data"]['titulo'] ?>
                        </h1>
                    </div>
                    <div class="gen-breadcrumb-container">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= URL ?>"><i class="fas fa-home mr-2"></i><?= $_SESSION["lang-txt"]["general"]["inicio"] ?></a></li>
                            <li class="breadcrumb-item active"><a href="<?= CANONICAL ?>"><?= $contenidoData[array_key_first($contenidoData)]['area']["data"]['titulo'] ?></a></li>
                        </ol>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</div>


<section class="gen-section-padding-3">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="button-group filter-btn-group mb-50 text-center">
                    <button class="btn btn-category " data-toggle="portfilter" data-target="all">
                        <?=$_SESSION['lang-txt']['novedades']['todos']?>
                    </button>
                    <?php
                    // if isset contenidosData categoria
                    foreach ($categoriaList as $item) { ?>
                        <button class="btn btn-category" data-toggle="portfilter" data-target="<?= $item['data']['cod'] ?>">
                            <?= $item['data']['titulo'] ?>
                        </button>
                    <?php } ?>
                </div>

                <div class="row">
                    <?php foreach ($contenidoData as $item) {
                        $link = URL . "/c/" . $item['data']['area'] . "/" . $f->normalizar_link($item['data']['titulo']) . "/" . $item['data']['cod'];
                        $date = date_create($item['data']['fecha']);
                        
                    ?>
                        <div class="col-xl-3 col-lg-4 col-md-6" data-tag="<?= $item['data']['categoria'] ?>">
                            <div class="gen-carousel-movies-style-1 movie-grid style-1">
                                <div class="gen-movie-contain">
                                    <div class="gen-movie-img">
                                        <img src="<?= $item["images"][0]['url'] ?>" alt="single-video-image">
                                        <div class="gen-movie-add">
                                            <div class="wpulike wpulike-heart">
                                                <div class="wp_ulike_general_class">
                                                    <a href="#" class="sl-button text-white"><i class="far fa-heart"></i></a>
                                                </div>
                                            </div>
                                            <ul class="menu bottomRight">
                                                <li class="share top">
                                                    <i class="fa fa-share-alt"></i>
                                                    <ul class="submenu">
                                                        <li><a href="https://www.facebook.com/" class="facebook"><i class="fab fa-facebook-f"></i></a>
                                                        </li>
                                                        <li><a href="https://www.instagram.com/" class="facebook"><i class="fab fa-instagram"></i></a>
                                                        </li>
                                                        <li><a href="https://www.twitter.com/" class="facebook"><i class="fab fa-twitter"></i></a></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                            <div class="video-actions--link_add-to-playlist dropdown">
                                                <a class="dropdown-toggle" href="#" data-toggle="dropdown"><i class="fa fa-plus"></i></a>
                                                <div class="dropdown-menu">
                                                    <a class="login-link" href="#"><?=$_SESSION['lang-txt']['usuarios']['regrastrate-para']?></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="gen-movie-action">
                                            <a class="gen-button" href="<?= $link ?>">
                                                <i class="fa fa-play"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="gen-info-contain">
                                        <div class="gen-movie-info">
                                            <h3><a data-rel="<?= $item["data"]["categoria"] ?>" href="<?= $link ?>"><?= $item["data"]['titulo'] ?></a></h3>
                                        </div>
                                        <div class="gen-movie-meta-holder">
                                            <ul>
                                                <li><?= date_format($date, 'd-m-Y '); ?></li>
                                                <li>
                                                    <a href=""><span class="fs-12"><?= $item["data"]['categoria_titulo'] ?></span></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="gen-pagination">
                            <?= $paginador ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<?php
$template->themeEnd();
?>