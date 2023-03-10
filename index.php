<?php
require_once "Config/Autoload.php";
Config\Autoload::run();

$template = new Clases\TemplateSite();
$f = new Clases\PublicFunction();
$productos = new Clases\Productos();
$contenidos = new Clases\Contenidos();

$data_banner = [
   "filter" => ["contenidos.area = 'novedades' && contenidos.destacado = 1"],
   "images" => 'all',
   "options" => true,
   "orden" => 5

];
$bannerInicio = $contenidos->list($data_banner, $_SESSION["lang"], false);

$data = [
   "filter" => ["contenidos.area = 'novedades' && contenidos.destacado = 1"],
   "images" => 'all',
   "options" => true,
   "category" => true,

];
$novedades = $contenidos->list($data, $_SESSION["lang"]);

$filter_recientes = [
   "filter" => ["contenidos.area = 'novedades' "],
   "images" => 'all',
   "category" => true,
   "limit" => 5,
   "order"  => "fecha DESC"
];

$novRecientes = $contenidos->list($filter_recientes, $_SESSION["lang"], false);

#Información de cabecera
$template->set("title", TITULO);
$template->set("description", "");
$template->set("keywords", "");
$template->themeInit();
?>

<!-- owl-carousel Banner Start -->
<section class="pt-0 pb-0">
   <div class="container-fluid px-0">
      <div class="row no-gutters">
         <div class="col-12">
            <div class="gen-banner-movies banner-style-2">
               <div class="owl-carousel owl-loaded owl-drag" data-dots="false" data-nav="true" data-desk_num="1" data-lap_num="1" data-tab_num="1" data-mob_num="1" data-mob_sm="1" data-autoplay="true" data-loop="true" data-margin="0">
                  <?php foreach ($bannerInicio as $item) { 
                     $link = URL . "/c/" . $item["data"]["area"] . "/" . $f->normalizar_link($item["data"]["titulo"]) . "/" . $item["data"]["cod"];
                     ?>
                     <div class="item" style="background: url('<?= $item['images'][0]['url'] ?>')">
                        <div class="gen-movie-contain-style-2 h-100">
                           <div class="container h-100">
                              <div class="row flex-row-reverse align-items-center h-100">
                                 <div class="col-xl-6">
                                    <div class="gen-front-image">
                                       <img src="<?= $item['images'][0]['url'] ?>" alt="<?= $item['data']['titulo'] ?>">
                                       <a href="https://www.youtube.com/watch?v=<?= $item['data']['link'] ?>" class="playBut popup-youtube popup-vimeo popup-gmaps">
                                          <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="213.7px" height="213.7px" viewBox="0 0 213.7 213.7" enable-background="new 0 0 213.7 213.7" xml:space="preserve">
                                             <polygon class="triangle" id="XMLID_17_" fill="none" stroke-width="7" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" points="
                                                            73.5,62.5 148.5,105.8 73.5,149.1 "></polygon>
                                             <circle class="circle" id="XMLID_18_" fill="none" stroke-width="7" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" cx="106.8" cy="106.8" r="103.3">
                                             </circle>
                                          </svg>
                                          <span><?= $_SESSION["lang-txt"]["general"]["ver_ahora"] ?></span>
                                       </a>
                                    </div>
                                 </div>
                                 <div class="col-xl-6">
                                    <div class="gen-tag-line"><span><?= $item['data']['subtitulo'] ?></span></div>
                                    <div class="gen-movie-info">
                                       <h3><?= $item['data']['titulo'] ?></h3>
                                    </div>
                                    <div class="gen-movie-meta-holder">
                                       <p><?= $item['data']['description'] ?></p>
                                          <div class="gen-meta-info">
                                             <ul class="gen-meta-after-excerpt">
                                             <?php foreach ($item['options'] as $option) { ?>
                                                <li>
                                                   <strong><?= $option['data']["titulo"] ?></strong>
                                                   <span>
                                                   <?= $option['data']["valor"] ?>
                                                   <span>
                                                </li>                                             
                                             <?php } ?>
                                             </ul>
                                          </div>
                                    </div>
                                    <div class="gen-movie-action">
                                       <div class="gen-btn-container">
                                          <a href="<?=$link?>" class="gen-button .gen-button-dark">
                                             <i aria-hidden="true" class="fas fa-play"></i> <span class="text"><?= $_SESSION["lang-txt"]["general"]["ver_mas"] ?></span>
                                          </a>
                                       </div>
                                    </div>
                                 </div>
                              </div>
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
<!-- owl-carousel Banner End -->

<!-- owl-carousel Videos Section-1 Start -->
<section class="gen-section-padding-2">
   <div class="container">
      <div class="row">
         <div class="col-xl-6 col-lg-6 col-md-6">
            <h4 class="gen-heading-title"><?= $_SESSION['lang-txt']['general']['destacados'] ?></h4>
         </div>
         <div class="col-xl-6 col-lg-6 col-md-6 d-none d-md-inline-block">
            <div class="gen-movie-action">
               <div class="gen-btn-container text-right">
                  <a href="<?= URL ?>/c/novedades" class="gen-button gen-button-flat">
                     <span class="text"><?= $_SESSION['lang-txt']['general']['ver_mas'] ?></span>
                  </a>
               </div>
            </div>
         </div>
      </div>
      <div class="row mt-3">
         <div class="col-12">
            <div class="gen-style-2">
               <div class="owl-carousel owl-loaded owl-drag" data-dots="false" data-nav="true" data-desk_num="4" data-lap_num="3" data-tab_num="2" data-mob_num="1" data-mob_sm="1" data-autoplay="false" data-loop="false" data-margin="30">
                  <?php foreach ($novedades as $item) {
                     $link = URL . "/c/" . $item["data"]["area"] . "/" . $f->normalizar_link($item["data"]["titulo"]) . "/" . $item["data"]["cod"];
                  ?>
                     <div class="item">
                        <div class="movie type-movie status-publish has-post-thumbnail hentry movie_genre-action movie_genre-adventure movie_genre-drama">
                           <div class="gen-carousel-movies-style-2 movie-grid style-2">
                              <div class="gen-movie-contain">
                                 <div class="gen-movie-img">
                                    <img src="<?= $item['images'][0]['url'] ?>" alt="<?= $item['data']['titulo'] ?>">
                                    <div class="gen-movie-add">
                                       <div class="wpulike wpulike-heart">
                                          <div class="wp_ulike_general_class wp_ulike_is_not_liked"><button type="button" class="wp_ulike_btn wp_ulike_put_image"></button></div>
                                       </div>
                                       <ul class="menu bottomRight">
                                          <li class="share top">
                                             <i class="fa fa-share-alt"></i>
                                             <ul class="submenu">
                                                <li><a href="#" class="facebook"><i class="fab fa-facebook-f"></i></a>
                                                </li>
                                                <li><a href="#" class="facebook"><i class="fab fa-instagram"></i></a>
                                                </li>
                                                <li><a href="#" class="facebook"><i class="fab fa-twitter"></i></a></li>
                                             </ul>
                                          </li>
                                       </ul>
                                       <div class="movie-actions--link_add-to-playlist dropdown">
                                          <a class="dropdown-toggle" href="#" data-toggle="dropdown"><i class="fa fa-plus"></i></a>
                                          <div class="dropdown-menu mCustomScrollbar">
                                             <div class="mCustomScrollBox">
                                                <div class="mCSB_container">
                                                   <a class="login-link" href="register"><?=$_SESION['lang-txt']['usuarios']['registrate-para']?></a>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="gen-movie-action">
                                       <a href="<?= $link ?>" class="gen-button">
                                          <i class="fa fa-play"></i>
                                       </a>
                                    </div>
                                 </div>
                                 <div class="gen-info-contain">
                                    <div class="gen-movie-info">
                                       <h3><a href="<?= $link ?>"><?= $item['data']['titulo'] ?></a>
                                       </h3>
                                    </div>
                                    <div class="gen-movie-meta-holder">

                                       <ul>
                                          <li>2hr 00mins</li>
                                          <li>
                                             <a href="action.html"><span><?= $item['data']['categoria_titulo'] ?></span></a>
                                          </li>
                                       </ul>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <!-- #post-## -->
                     </div>
                  <?php } ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
<!-- owl-carousel Videos Section-1 End -->


<!-- owl-carousel Videos Section-4 Start -->
<section class="pt-0 gen-section-padding-2">
   <div class="container">
      <div class="row">
         <div class="col-xl-6 col-lg-6 col-md-6">
            <h4 class="gen-heading-title"><?= $_SESSION["lang-txt"]["novedades"]["recientes"] ?></h4>
         </div>
         <div class="col-xl-6 col-lg-6 col-md-6 d-none d-md-inline-block">
            <div class="gen-movie-action">
               <div class="gen-btn-container text-right">
                  <a href="<?= URL ?>/c/novedades" class="gen-button gen-button-flat">
                     <span class="text"><?= $_SESSION['lang-txt']['general']['ver_mas'] ?></span>
                  </a>
               </div>
            </div>
         </div>
      </div>
      <div class="row mt-3">
         <div class="col-12">
            <div class="gen-style-2">
               <div class="owl-carousel owl-loaded owl-drag" data-dots="false" data-nav="true" data-desk_num="4" data-lap_num="3" data-tab_num="2" data-mob_num="1" data-mob_sm="1" data-autoplay="false" data-loop="false" data-margin="30">
                  <?php foreach ($novRecientes as $item) {
                     $link = URL . "/c/" . $item["data"]["area"] . "/" . $f->normalizar_link($item["data"]["titulo"]) . "/" . $item["data"]["cod"];
                  ?>

                     <div class="item">
                        <div class="movie type-movie status-publish has-post-thumbnail hentry movie_genre-action movie_genre-adventure movie_genre-drama">
                           <div class="gen-carousel-movies-style-2 movie-grid style-2">
                              <div class="gen-movie-contain">
                                 <div class="gen-movie-img">
                                    <img src="<?= $item["images"][0]["url"] ?>" alt="<?= $item["data"]["titulo"] ?>">
                                    <div class="gen-movie-add">
                                       <div class="wpulike wpulike-heart">
                                          <div class="wp_ulike_general_class wp_ulike_is_not_liked"><button type="button" class="wp_ulike_btn wp_ulike_put_image"></button></div>
                                       </div>
                                       <ul class="menu bottomRight">
                                          <li class="share top">
                                             <i class="fa fa-share-alt"></i>
                                             <ul class="submenu">
                                                <li><a href="#" class="facebook"><i class="fab fa-facebook-f"></i></a>
                                                </li>
                                                <li><a href="#" class="facebook"><i class="fab fa-instagram"></i></a>
                                                </li>
                                                <li><a href="#" class="facebook"><i class="fab fa-twitter"></i></a></li>
                                             </ul>
                                          </li>
                                       </ul>
                                       <div class="movie-actions--link_add-to-playlist dropdown">
                                          <a class="dropdown-toggle" href="#" data-toggle="dropdown"><i class="fa fa-plus"></i></a>
                                          <div class="dropdown-menu mCustomScrollbar">
                                             <div class="mCustomScrollBox">
                                                <div class="mCSB_container">
                                                   <a class="login-link" href="register.html"><?=$_SESION['lang-txt']['usuarios']['registrate-para']?></a>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="gen-movie-action">
                                       <a href="<?= $link ?>" class="gen-button">
                                          <i class="fa fa-play"></i>
                                       </a>
                                    </div>
                                 </div>
                                 <div class="gen-info-contain">
                                    <div class="gen-movie-info">
                                       <h3><a href="<?= $link ?>"><?= $item["data"]["titulo"] ?></a>
                                       </h3>
                                    </div>
                                    <div class="gen-movie-meta-holder">
                                       <ul>
                                          <li>1hr 24mins</li>
                                          <li>
                                             <a href="action.html"><span><?= $item['data']['categoria_titulo'] ?></span></a>
                                          </li>
                                       </ul>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <!-- #post-## -->
                     </div>
                  <?php } ?>

               </div>
            </div>
         </div>
      </div>
   </div>
</section>
<!-- owl-carousel Videos Section-4 End -->

<?php $template->themeEnd() ?>