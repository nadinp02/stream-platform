<?php

namespace Clases;

use Exception;

class Contenidos extends Conexion
{

    //Atributos
    public $id;
    public $cod;
    public $titulo;
    public $titulo_on;
    public $subtitulo;
    public $subtitulo_on;
    public $contenido;
    public $categoria;
    public $subcategoria;
    public $keywords;
    public $description;
    public $link;
    public $link_on;
    public $area;
    public $fecha;
    public $idioma;
    public $destacado;

    public function set($atributo, $valor)
    {
        if (!empty($valor)) {
            $valor = "'" . $valor . "'";
        } else {
            $valor = "NULL";
        }
        $this->$atributo = $valor;
    }

    public function get($atributo)
    {
        return $this->$atributo;
    }

    /**
     *
     * Realizamos un list de varios ....
     *
     * @param    array  $array  todos los campos a actualizar con la key = campo sql
     *
     */

    public function add($array)
    {
        $attr = implode(",", array_keys($array));
        $values = ":" . str_replace(",", ",:", $attr);
        $sql = "INSERT INTO contenidos ($attr) VALUES ($values)";
        try {
            $stmt = parent::conPDO()->prepare($sql);
            $stmt->execute($array);
            $response = true;
        } catch (Exception $e) {
            $response["error"] = $e->getMessage();
        }
        return $response;
    }

    /**
     *
     * Realizamos un list de varios ....
     *
     * @param array  $array  todos los campos a actualizar con la key = campo sql
     * @param array  $params filtros para el where del update ["id = 1"]
     *
     */
    public function edit($array, $params)
    {
        $query = implode(", ", array_map(function ($v) {
            return "$v=:$v";
        }, array_keys($array)));
        $condition = implode(' AND ', $params);
        $sql = "UPDATE contenidos SET $query WHERE $condition";
        echo $sql;
        try {
            $stmt = parent::conPDO()->prepare($sql);
            $stmt->execute($array);
            $response = true;
        } catch (Exception $e) {
            $response["error"] = $e->getMessage();
        }
        return $response;
    }

    public function delete($array)
    {
        $sql = "DELETE FROM `contenidos` WHERE cod=:cod AND idioma=:idioma";
        try {
            $stmt = parent::conPDO()->prepare($sql);
            $stmt->execute($array);
            if (!empty(Imagenes::list($array, "", "", true))) {
                Imagenes::deleteAll($array);
            }
            $response = true;
        } catch (Exception $e) {
            $response["error"] = $e->getMessage();
        }
        return $response;
    }


    /**
     *
     * Pasar un array con "clave" => "valor"
     * $data = [
     * "filter" => $filter,
     * "images" => true,
     *];
     * @param    array  $filter se agrega un filtro
     * @param    string  $idioma se pasa el codigo de la $_SESSION['lang'] o el código de un idioma en especifico
     * @param    bool  $subcategory si desea traer esa informacion extra sobre el contenido true/false
     * @param    bool  $category si desea traer esa informacion extra sobre el contenido true/false
     * @param    bool  $options si desea traer esa informacion extra sobre el contenido true/false
     * @param    array  $optionsFilter array multidimensional y multiple ["cod_opcion" => ["value","value"]]
     * @param    bool  $images si desea traer esa informacion extra sobre el contenido single/all/false
     * @param    bool  $gallery si desea traer esa informacion extra sobre el contenido true/false
     * @param    int  $order agregar un orden Ej: id DESC
     * @param    int  $limit agregar un limite de contenidos a retornar
     * @return   array retorna un array con los contenidos
     *
     */
    public static function list($data, $idioma, $single = false)
    {
        $filter = !empty($data['filter']) ? $data['filter'] :  [];
        $idioma = !empty($idioma) ? $idioma :  $_SESSION['lang'];
        $category = !empty($data['category']) ? $data['category'] :  false;
        $subcategory = !empty($data['subcategory']) ? $data['subcategory'] :  false;
        $images = !empty($data['images']) ? $data['images'] :  false;
        $order = !empty($data['order']) ? $data['order'] :  '';
        $limit = !empty($data['limit']) ? $data['limit'] :  '';
        $options = !empty($data['options']) ? $data['options'] :  '';
        $optionsFilter = !empty($data["optionsFilter"]) ? $data["optionsFilter"] : false;
        $optionsSQLFilter = '';

        $filter[] = "contenidos.idioma = '" . $idioma . "'";


        if ($optionsFilter) {
            foreach ($optionsFilter as $key => $option) {
                $key = str_replace("'", "", $key);
                $optionsSQLFilter .= " INNER JOIN `opciones_valor` AS `" . $key . "` ON `" . $key . "`.`opcion_cod` = '" . $key . "' AND `" . $key . "`.`idioma` = '" . $_SESSION['lang'] . "' AND ( `" . $key . "`.`relacion_cod` = `contenidos`.`cod` ";
                foreach ($option as $key2 => $valor) {
                    $optionsSQLFilter .= ($key2 == 0) ? " AND (`" . $key . "`.`valor` = '" . $valor . "'" : " OR  `" . $key . "`.`valor`  = '" . $valor . "'";
                }
                $optionsSQLFilter .= ")) ";
            }
        }

        $filterSql = (is_array($filter)) ? 'WHERE ' . implode(' AND ', $filter) : '';

        if ($category) {
            $arrayAttr[] = "`categorias`.`titulo` as 'categoria_titulo',`categorias`.`area` as 'categoria_area', `categorias`.`descripcion` as 'categoria_descripcion'";
            $arrayLeft[] = "LEFT JOIN `categorias` ON `categorias`.`cod` = `contenidos`.`categoria`  AND `categorias`.`idioma` = '$idioma'";
        }
        if ($subcategory) {
            $arrayAttr[] = "`subcategorias`.`titulo`as 'subcategoria_titulo'";
            $arrayLeft[] = "LEFT JOIN `subcategorias` ON `subcategorias`.`cod` = `contenidos`.`subcategoria`  AND `subcategorias`.`idioma` = '$idioma'";
        }
        if ($optionsSQLFilter) {
            $arrayLeft[] = $optionsSQLFilter;
        }

        $attr = isset($arrayAttr) ? " , " . implode(" , ", $arrayAttr) . " " : '';
        $left = isset($arrayLeft) ? implode(" ", $arrayLeft) : '';
        $orderSql = ($order != '') ? $order  : "`contenidos`.`idioma`,`contenidos`.`id` DESC";
        $limitSql = ($limit != '') ? "LIMIT " . $limit : '';
        $sql = "SELECT `contenidos`.* $attr FROM `contenidos` $left $filterSql GROUP BY `contenidos`.`id` ORDER BY $orderSql $limitSql";
        $contenido = parent::sqlReturn($sql);
        if ($contenido) {
            $array = array();
            $array_ = [];
            while ($row = mysqli_fetch_assoc($contenido)) {
                $array_['data'] = $row;
                $row['fecha'] = $row['idioma'] == 'es' ? date_format(date_create($row["fecha"]), "d/m/Y") : $row['fecha'];
                $row["link"] = URL . "/c/" . $row["area"] . "/" . PublicFunction::normalizar_link($row["titulo"]) . "/" . $row["cod"];
                $array_["options"] = ($options) ? OpcionesValor::list($_SESSION["lang"], ["relacion_cod = '" . $row['cod'] . "'"], true) : [];
                if ($images != false) {
                    $imagesData =  Imagenes::list(["cod" => $row["cod"], "idioma" => $idioma], "", "", ($images === 'all') ? false : true);
                    $array_["images"] = self::createArrayImages($imagesData, $images);
                }
                $array_["area"] = Area::list(["cod = '" . $row["area"] . "'"], '', '', $idioma, true);
                $array[$row["cod"]] = $array_;
            }
            return ($single) ? $array_ : $array;
        } else {
            return false;
        }
    }

    public function countContents($filter = [], $idioma = "es")
    {
        $filter[] = "contenidos.idioma = '" . $idioma . "'";
        $filterSql = (is_array($filter)) ? 'WHERE ' . implode(' AND ', $filter) : '';
        $sql = "SELECT COUNT(*) as cantidad FROM `contenidos` $filterSql ";
        $query = parent::sqlReturn($sql);
        $row = mysqli_fetch_assoc($query);
        return $row["cantidad"];
    }

    public static function createArrayImages($images, $type)
    {
        $images_array = [];
        if (!empty($images) && $type === 'all') {
            foreach ($images as $img) {
                $images_array[] = ["id" => $img["id"], "orden" => $img["orden"], "url" => URL . "/" . $img["ruta"], "idioma" => $img['idioma']];
            }
        }
        if (!empty($images) && $type != 'all') $images_array[] = ["id" => $images["id"], "orden" => $images["orden"], "url" => URL . "/" . $images["ruta"], "idioma" => $images['idioma']];
        $images = (count($images_array)) ? $images_array : [["id" => 000000, "orden" => 0, "url" => URL . "/assets/archivos/sin_imagen.jpg"]];
        return $images;
    }

    /**
     *
     * Realizamos un list de varios ....
     *
     * @param    array  $filter array con todos los códigos que quiero traer
     * @return    array retorna un array con todos los array internos, con a key de código de contenidos
     *
     */

    public function listMany($filter, $images = true)
    {
        $filter = implode(" OR ", $filter);
        $sql = "SELECT * FROM `contenidos` WHERE  $filter  ORDER BY area ASC";
        $contenido = parent::sqlReturn($sql);
        $area = '';
        while ($row = mysqli_fetch_assoc($contenido)) {
            $area = ($row["area"] == $area) ? $area : $row["area"];
            $img = ($images) ? Imagenes::list(array("cod = '" . $row['cod'] . "'"), "orden ASC", "") : [];
            $array[$area][$row['cod']] = array("data" => $row,  "images" => $img);
        }
        return $array;
    }

    public function editSingle($atribute, $value)
    {
        $sql = "UPDATE `contenidos` SET $atribute = $value WHERE `id`={$this->id} OR `cod`={$this->cod}";
        $query = parent::sql($sql);

        return (!empty($query)) ? true : false;
    }

    public function doSlideOrGallery($contenido)
    {
        $contenido = $this->createGallery($contenido);
        $contenido = $this->createSlider($contenido);
        return $contenido;
    }

    public function createGallery($contenido)
    {
        $galeryCheck = strpos($contenido['data']['contenido'], "[gallery");
        if ($galeryCheck !== false) {
            preg_match("/\[[^\]]*\]/", $contenido['data']['contenido'], $check);
            $string = str_replace(["[", "]"], "", $check[0]);
            $stringExplode = explode("|", $string);
            $col = isset($stringExplode[1]) ? $stringExplode[1] : 12;
            $galeria = "<div class='galleryDev'><div class='row'>";
            $count = 0;
            foreach ($contenido["data"]["imagenes_rutas"] as $img) {
                $count++;
                $galeria .= '<div class="col-md-' . $col  . '"><a href="' . URL . "/" . $img . '" class="thumbnail" data-toggle="modal" data-target="#lightbox' . $count . '"><img src="' . URL . "/" . $img . '" alt="' . $contenido["data"]["titulo"] . '"></a></div>';
                $galeria .= "<div id='lightbox" . $count . "' class='modal fade' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
                                <div class='modal-dialog'>
                                    <button type='button' class='close hidden' data-dismiss='modal' aria-hidden='true'>×</button>
                                <div class='modal-content'>
                                <div class='modal-body'>
                                    <img src='" . URL . "/" . $img . "' alt='' />
                                </div>
                            </div>
                        </div>
                    </div>";
            }
            $galeria .= "</div></div>";
            $contenido["data"]["contenido"] = str_replace($check[0], $galeria, $contenido["data"]["contenido"]);
        }
        return $contenido;
    }

    public function createSlider($contenido)
    {
        $sliderCheck = strpos($contenido['data']['contenido'], "[slider]");
        if ($sliderCheck !== false) {
            $i = 0;
            $galeria = '<div id="carousel" class="carousel slide" data-ride="carousel"><div class="carousel-inner">';
            foreach ($contenido["data"]["imagenes_rutas"] as $img) {
                $check = ($i === 0) ? "active" : "";
                $galeria .= '<div class="carousel-item ' . $check . '"><img class="d-block w-100"   src="' . URL . "/" . $img . '" alt="' . $contenido["data"]["titulo"] . '" /></div>';
                $i++;
            }
            $galeria .= '</div><a class="carousel-control-prev" href="#carousel" role="button" data-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="sr-only">Previous</span>  </a>  <a class="carousel-control-next" href="#carousel" role="button" data-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="sr-only">Next</span></a></div>';

            $contenido["data"]["contenido"] = str_replace("[slider]", $galeria, $contenido["data"]["contenido"]);
        }
        return $contenido;
    }




    public function duplicateByLanguage($lang, $langTo)
    {
        foreach ($this->list([], $lang) as $contenido_) {
            unset($contenido_["data"]["id"]);
            $contenido_["data"]["idioma"] = $langTo;
            $this->add($contenido_["data"]);
        }
    }

    /**
     *
     * Creacion del paginador segun la cantidad de contenidos a mostrar ....
     *
     * @param    string  $url url de la pagina a paginar
     * @param    array  $filter array con la informacion de contenidos a traer
     * @param    int  $limit numero de contenidos por pagina
     * @param    int  $page pagina en la que se encuentra
     * @param    int  $range cantidad de paginas a mostrar en el paginador entre las flechas  < x x 3 x x > 
     * @param    bool  $friendly paginador para url amigable o no
     * @return   html retorna un HTML con el paginador
     *
     */

    public function paginador($url, $filter, $limit, $page = 1, $range = 1, $friendly = true)
    {

        $separator = ($friendly) ? '/p/' : '&pagina=';
        $count = $this->countContents($filter, $_SESSION["lang"]);
        $total = ceil($count / $limit);
        $pre = $page - 1;
        $next = $page + 1;
        $html = "";
        if ($total > 1) {
            $html .= "<nav class='Page navigation'>";
            $html .=  "<ul class='page-numbers'>";
            if ($pre > 0) {
                $html .= "<li>";
                $html .= "<a class='page-numbers' href='" . $url . $separator . "1'><i class='fa fa-angle-double-left'></i></a>";
                $html .=  "</li>";
                $html .=  "<li>";
                $html .=  "<a class='page-numbers' href='" . $url . $separator . $pre . "'><i class='fa fa-angle-left'></i></a>";
                $html .=  "</li>";
            }
            foreach (range($page - $range, $page + $range) as $i) {
                if ($i > 0 && $i <= $total) {
                    $active = ($i == $page) ? 'current' : '';
                    $html .=  "<li>";
                    $html .=  "<a class='page-numbers $active' href='" . $url . $separator . $i . "'>$i</a>";
                    $html .=  "</li>";
                }
            }
            if ($next <= $total) {
                $html .= "<li>";
                $html .= "<a class='next page-numbers' href='" . $url . $separator . $next . "'><i class='fa fa-angle-right'></i></a>";
                $html .=  "</li>";
                $html .= "<li>";
                $html .= "<a class='page-numbers' href='" . $url . $separator . $total . "'><i class='fa fa-angle-double-right'></i></a>";
                $html .=  "</li>";
            }
            $html .=  "</ul>";
            $html .=  "</nav>";
        }
        return $html;
    }
}
