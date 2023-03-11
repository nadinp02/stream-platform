<?php
require_once "config/Autoload.php";
Config\Autoload::run();

$template = new Clases\TemplateSite();
$f = new Clases\PublicFunction();
$contenidos = new Clases\Contenidos();

$filter = [];

isset($_GET["area"]) ?  $filter[] = "contenidos.area = '" . $f->antihack_mysqli($_GET["area"]) . "'" : '';
isset($_GET["cod"]) ?  $filter[] = "contenidos.cod = '" . $f->antihack_mysqli($_GET["cod"]) . "'" : '';


$data = [
    "filter" => $filter,
    "images" => 'all',
    "category" => true,
    "subcategory"  => true,
    "gallery" => true,
];

#List de contenidos (al ser único el título, solo trae un resultado)
$contenidoData = $contenidos->list($data, $_SESSION["lang"], true);
$novedadesRelacionadas = $contenidos->list(["filter" => ["contenidos.area = '" . $f->antihack_mysqli($_GET["area"]) . "'", "contenidos.cod != '" . $f->antihack_mysqli($_GET["cod"]) . "'"], "images" => 'single', "limit" => 4], $_SESSION["lang"]);
$date = date_create($contenidoData['data']['fecha']);

#Si se encontro el contenido se almacena y sino se redirecciona al inicio
if (empty($contenidoData)) $f->headerMove(URL);
#Información de cabecera
$template->set("title", $contenidoData['data']['titulo'] . " | " . TITULO);
$template->set("description", $contenidoData['data']['description']);
$template->set("keywords", $contenidoData['data']['keywords']);
$template->set("imagen", isset($contenidoData['data']['images'][0]['url']) ? $contenidoData['data']['images'][0]['url'] : LOGO);
$template->themeInit();
?>
<section class="gen-section-padding-3 gen-single-video">
    <div class="container">
        <div class="row no-gutters">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="gen-video-holder">
                            <iframe width="100%" height="550px" src="https://www.youtube.com/embed/<?= $contenidoData['data']['link'] ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="single-video">
                            <div class="gen-single-video-info">
                                <h2 class="gen-title"><?= $contenidoData['data']['titulo'] ?></h2>
                                <div class="gen-single-meta-holder">
                                    <ul>
                                        <li><?= date_format($date, 'd-m-y')?></li>
                                        <li>
                                            <a href="<?= URL ?>/c/<?= $contenidoData['area']['data']['titulo'] ?>"><span><?= $contenidoData['area']['data']['titulo'] ?></span></a>
                                        </li>
                                        <li>
                                        <span><?= $contenidoData['data']['categoria_titulo']?></span>
                                        </li>
                                    </ul>
                                </div>
                                <p><?= $contenidoData['data']['description'] ?>
                                </p>
                                <div class="gen-socail-share mt-0">
                                    <h4 class="align-self-center"><?= $_SESSION['lang-txt']['general']['compartir'] ?></h4>
                                    <ul class="social-inner">
                                        <li><a href="https://www.facebook.com/" class="facebook"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a href="https://www.instagram.com/" class="facebook"><i class="fab fa-instagram"></i></a></li>
                                        <li><a href="https://www.twitter.com/" class="facebook"><i class="fab fa-twitter"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <?php
                        if (isset($novedadesRelacionadas) && count($novedadesRelacionadas) >= 1) { ?>
                            <div class="pm-inner">
                                <div class="gen-more-like">
                                    <h5 class="gen-more-title"><?=$_SESSION["lang-txt"]["novedades"]["relacionadas"]?></h5>
                                    <div class="row post-loadmore-wrapper">
                                        <?php foreach ($novedadesRelacionadas as $item) {
                                            $link = URL . "/c/" . $item['data']['area'] . "/" . $f->normalizar_link($item['data']['titulo']) . "/" . $item['data']['cod']; 
                                            $date = date_create($contenidoData['data']['fecha'])?>
                                            <div class="col-xl-3 col-lg-4 col-md-6">
                                                <div class="gen-carousel-movies-style-3 movie-grid style-3">
                                                    <div class="gen-movie-contain">
                                                        <div class="gen-movie-img">
                                                            <img src="<?= $item['images'][0]["url"] ?>" alt="single-video-image">
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
                                                                        <a class="login-link" href="#"><?=$_SESSION["lang-txt"]['usuarios']['registrate-para']?></a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="gen-movie-action">
                                                                <a href="<?=$link?>" class="gen-button">
                                                                    <i class="fa fa-play"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="gen-info-contain">
                                                            <div class="gen-movie-info">
                                                                <h3><a href="<?=$link?>"><?= $item['data']['titulo']?></a></h3>
                                                            </div>
                                                            <div class="gen-movie-meta-holder">
                                                                <ul>
                                                                    <li><?= date_format($date,"d-m-y") ?></li>
                                                                    <li>
                                                                        <a><span><?= $contenidoData['data']['categoria_titulo'] ?></span></a> | <a><span><?= $contenidoData['data']['subcategoria_titulo'] ?></span></a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<?php
$template->themeEnd();
?>