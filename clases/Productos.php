<?php

namespace Clases;

use Exception;

class Productos extends Conexion
{
    //Atributos
    public $id;
    public $cod;
    public $titulo;
    public $precio;
    public $precio_descuento;
    public $precio_mayorista;
    public $peso;
    public $stock;
    public $desarrollo;
    public $categoria;
    public $subcategoria;
    public $keywords;
    public $description;
    public $destacado;
    public $envio_gratis;
    public $mostrar_web;
    public $fecha;
    public $meli;
    public $tercercategoria;
    public $cod_producto;
    public $img;
    public $url;
    public $idioma;

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

    public function getAttrWithTitle()
    {
        $data = [];
        $data["cod"] = "Código Interno";
        $data["cod_producto"] = "Código Empresa";
        $data["titulo"] = "Titulo";
        $data["desarrollo"] = "Desarrollo";
        $data["stock"] = "Stock";
        $data["peso"] = "Peso";
        $data["precio"] = "Precio";
        $data["precio_descuento"] = "Precio con Descuento";
        $data["precio_mayorista"] = "Precio Mayorista";
        $data["categoria"] = "Categoria";
        $data["subcategoria"] = "Subcategoria";
        $data["tercercategoria"] = "Tercer Categoria";
        $data["keywords"] = "Palabras Claves";
        $data["description"] = "Descripcion Breve";
        $data["mostrar_web"] = "Mostrar en web";
        $data["idioma"] = "Idioma";
        $data["fecha"] = "Fecha";
        return $data;
    }

    public function add($array)
    {
        $attr = implode(",", array_keys($array));
        $values = ":" . str_replace(",", ",:", $attr);
        $sql = "INSERT INTO productos ($attr) VALUES ($values)";
        $stmt = parent::conPDO()->prepare($sql);
        $stmt->execute($array);
    }

    public function edit($array, $cod)
    {
        $query = implode(", ", array_map(function ($v) {
            return "$v=:$v";
        }, array_keys($array)));
        $condition = implode(' AND ', $cod);
        $sql = "UPDATE productos SET $query WHERE $condition";
        $stmt = parent::conPDO()->prepare($sql);
        $stmt->execute($array);
    }


    public function editSingle($atributo, $valor, $idioma)
    {
        $sql = "UPDATE `productos` SET `$atributo` = {$valor} WHERE `cod`={$this->cod} AND `idioma`= '{$idioma}'";
        if (parent::sqlReturn($sql)) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($array)
    {
        $sql = "DELETE FROM `productos` WHERE cod=:cod AND idioma=:idioma";
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


    public function truncate()
    {
        $sql = "TRUNCATE `productos`";
        parent::sql($sql);
    }

    public function updateStockAvilableCero()
    {
        $sql = "UPDATE `productos` SET `mostrar_web` = 0 AND `stock` = 0 ";
        return (parent::sqlReturn($sql)) ? true : false;
    }

    public function viewSimple($cod, $idioma, $attr)
    {
        if (is_array($cod)) {
            foreach ($cod as $codigo) {
                $sql_filter[] = "`productos`.`$attr` = '" . $codigo . "' ";
            }
            $filter = implode(" OR ", $sql_filter);
        } else {
            $filter = "`productos`.`$attr` = '" . $cod . "' ";
        }
        $array = [];
        $sql = "SELECT cod, cod_proucto , titulo FROM `productos` WHERE  ($filter) AND idioma = '$idioma'";
        $productos = parent::sqlReturn($sql);
        if ($productos) {
            while ($row = mysqli_fetch_assoc($productos)) {
                $array[] = ["data" => $row];
            }
        }
        return $array;
    }

    public function listSearch($search, $limit, $idioma)
    {
        $search = trim($search);
        $search_array = explode(' ', $search);
        $searchSql = '';
        foreach ($search_array as $key => $searchData) {
            if ($key == 0) {
                $searchSql .= "UPPER(`productos`.`cod_producto`) LIKE UPPER('%$searchData%') OR UPPER(`productos`.`titulo`) LIKE UPPER('%$searchData%')";
            } else {
                $searchSql .= " AND `productos`.`titulo` LIKE '%$searchData%'";
            }
        }
        $sql = "SELECT `productos`.`titulo`, `productos`.`cod`  
                FROM `productos` 
                WHERE mostrar_web = '1' AND idioma = '$idioma' AND ($searchSql) 
                CASE WHEN `productos`.`titulo` LIKE '%$search%'  THEN `productos`.`titulo` END DESC
                LIMIT $limit";
        $contenido = parent::sqlReturn($sql);

        if ($contenido) {
            while ($row = mysqli_fetch_assoc($contenido)) {
                $link = URL . '/producto/' . PublicFunction::normalizar_link($row['titulo']) . '/' . $row['cod'];
                $array[] = ["value" => $row['titulo'], "label" => $row['titulo'], "link" => $link];
            }
            $array[] = ["value" => 'VER RESULTADOS DE ' . mb_strtoupper($search), "label" => 'VER RESULTADOS DE ' . mb_strtoupper($search), "link" => URL . "/productos/b/titulo/" . PublicFunction::normalizar_link($search)];
            return $array;
        }
    }

    public function maxPrice()
    {
        $sql = "SELECT MAX(precio) as precio FROM productos WHERE productos.mostrar_web = 1 AND (productos.cod_producto NOT LIKE '%|%' OR productos.cod_producto IS NULL)";
        $productos = parent::sqlReturn($sql);
        $row = mysqli_fetch_assoc($productos);
        return $row;
    }


    /**
     *
     * Traer más de un array 
     *
     * @param    array  $data array con todos los filtros de lo que se desea listar
     * @param    string  $idioma si se entra desde el admin
     * @param    bool  $single si solo se desea traer 1 producto (view)
     * @return   array retorna un array con todos los array internos de productos
     *
     */


    public    static  function list($data, $idioma, $single = false)
    {
        $filter = !empty($data['filter']) ? $data['filter'] :  [];
        $options = (isset($data['options']) && $data['options'] != false) ? $data['options'] :  false;
        $category = !empty($data['category']) ? $data['category'] :  false;
        $subcategory = !empty($data['subcategory']) ? $data['subcategory'] :  false;
        $tercercategory = !empty($data['tercercategory']) ? $data['tercercategory'] :  false;
        $images = !empty($data['images']) ? $data['images'] :  false;
        $admin = isset($data['admin']) ? $data['admin'] :  false;
        $promos = isset($data['promos']) ? $data['promos'] :  false;
        $order = !empty($data['order']) ? $data['order'] :  'productos.id DESC';
        $limit = !empty($data['limit']) ? $data['limit'] :  '';
        $idioma = !empty($idioma) ? $idioma :  $_SESSION['lang'];
        $favorite = !empty($data['favorite']) ? $data['favorite'] :  false;

        $opciones = '';
        $array_ = '';
        $array = array();
        is_array($filter) ? $filter[] = "`productos`.`idioma` = '" . $idioma . "' " : $filter = "`productos`.`idioma` = '" . $idioma . "'";
        $filterSql = (is_array($filter)) ? 'WHERE ' . implode(' AND ', $filter) : '';
        if (!$admin && isset($_SESSION["perfil-ecommerce"]["data"]) && $_SESSION["perfil-ecommerce"]["data"]["mostrar_sin_stock"] == 0) {
            $filterSql .= ($filterSql != '') ? " AND " : " WHERE ";
            $filterSql .= "`productos`.`stock` > 0";
        }
        if ($category) {
            $arrayAttr[] = "`categorias`.`titulo` as 'categoria_titulo',`categorias`.`area` as 'categoria_area', `categorias`.`descripcion` as 'categoria_descripcion', `categorias`.`free_shipping` as 'categoria_free_shipping'";
            $arrayLeft[] = "LEFT JOIN `categorias` ON `categorias`.`cod` = `productos`.`categoria` AND `categorias`.`idioma` = '$idioma'";
        }
        if ($subcategory) {
            $arrayAttr[] = "`subcategorias`.`titulo`as 'subcategoria_titulo', `subcategorias`.`free_shipping` as 'subcategoria_free_shipping'";
            $arrayLeft[] = "LEFT JOIN `subcategorias` ON `subcategorias`.`cod` = `productos`.`subcategoria` AND `subcategorias`.`idioma` = '$idioma'";
        }
        if ($tercercategory) {
            $arrayAttr[] = "`tercercategorias`.`titulo`as 'tercercategoria_titulo', `tercercategorias`.`free_shipping` as 'tercercategoria_free_shipping'";
            $arrayLeft[] = "LEFT JOIN `tercercategorias` ON `tercercategorias`.`cod` = `productos`.`tercercategoria` AND `tercercategorias`.`idioma` = '$idioma'";
        }
        if ($promos) {
            $arrayAttr[] = "`promos`.`lleva` as 'promoLleva', `promos`.`paga` as 'promoPaga'";
            $arrayLeft[] = "LEFT JOIN `promos` ON `promos`.`producto` = `productos`.`cod` AND `promos`.`idioma` = '$idioma'";
        }
        if ($options) {
            $arrayLeft[] = $options;
        }
        if ($favorite && isset($_SESSION['usuarios']['cod'])) {
            $arrayLeft[] = "INNER JOIN favoritos ON favoritos.producto = productos.cod AND favoritos.usuario = '" . $_SESSION['usuarios']['cod'] . "'";
        }

        $attr = isset($arrayAttr) ? " , " . implode(" , ", $arrayAttr) . " " : '';
        $left = isset($arrayLeft) ? implode(" ", $arrayLeft) : '';
        $orderSql = ($order != '') ? $order : " ";
        $limitSql = ($limit != '') ? "LIMIT " . $limit : '';
        $sql = "SELECT `productos`.* $attr FROM `productos` $left $filterSql  GROUP BY `productos`.`id` ORDER BY $orderSql $limitSql";
        $producto = parent::sqlReturn($sql);
        if ($producto) {
            while ($row = mysqli_fetch_assoc($producto)) {
                $productFecha = $row["fecha"];
                $productFecha = date("Y-m-d", strtotime($productFecha . "+ 7 days"));
                $fecha = ($productFecha >= date("Y-m-d")) ? $_SESSION['lang-txt']['productos']['nuevo'] : '';
                if (!$admin) {
                    $row =   self::checkPriceByUser($row);
                    $row =   self::checkFreeShipping($row);
                    $row['stock'] = (isset($_SESSION["perfil-ecommerce"]["data"]) && $_SESSION["perfil-ecommerce"]["data"]["usar_stock"] == 0) ? 99999 : $row['stock'];
                    $row['habilitado'] = (isset($_SESSION["perfil-ecommerce"]["data"]) && $_SESSION["perfil-ecommerce"]["data"]["saltar_checkout"] != 'all') ? 1 : 0;
                }
                if ($options) {
                    $opciones = OpcionesValor::list($row["idioma"], ["relacion_cod = '" . $row["cod"] . "'"], true);
                }
                $fav =  (isset($_SESSION['usuarios']['cod'])) ? Favoritos::view($_SESSION['usuarios']['cod'], $row['cod'], $row['idioma']) : '';
                $link = URL . '/producto/' .  PublicFunction::normalizar_link(($row['titulo'])) . '/' . $row['cod'];
                $array_ = ["data" => $row, "options" => $opciones, "nuevo" => $fecha, "link" => $link, "favorite" => $fav];
                if ($images != false) {
                    $imagesData =  Imagenes::list(["cod" => $row["cod"], "idioma" => $idioma], "", "", ($images === 'all') ? false : true);
                    $array_["images"] = self::createArrayImages($row['cod_producto'], $imagesData, $images);
                }
                $array[] = $array_;
            }
            return ($single) ? $array_ : $array;
        } else {
            return false;
        }
    }
    public function getAllCods($idioma)
    {
        $array = [];
        $sql = "SELECT `productos`.`cod_producto` FROM `productos` WHERE `cod_producto` != '' AND idioma = '$idioma'";
        $producto = parent::sqlReturn($sql);
        if ($producto) {
            while ($row = mysqli_fetch_assoc($producto)) {
                $array[] = $row["cod_producto"];
            }
        }
        return $array;
    }

    public static function createArrayImages($cod, $images, $type)
    {
        $images_array = [];

        if (!empty($images) && $type == 'all') {
            foreach ($images as $img) {
                $urlImg = URL . "/" . $img["ruta"];
                $imgNombre = str_replace(".webp", '_thumb.webp', $img["ruta"]);
                $thumb = $urlImg;
                if (file_exists($imgNombre)) {
                    $thumb = $imgNombre;
                }
                $images_array[] = ["id" => $img["id"], "orden" => $img["orden"], "url" => $urlImg, "thumb" => $thumb, "idioma" => $img["idioma"]];
            }
        }
        if (!empty($images) && $type != 'all') {
            $img = URL . "/" . $images["ruta"];
            $imagesNombre = str_replace(".webp", '_thumb.webp', $images["ruta"]);
            $thumb = $img;
            if (file_exists($imagesNombre)) {
                $thumb = $imagesNombre;
            }
            $images_array[] = ["id" => $images["id"], "orden" => $images["orden"], "url" => $img, "thumb" => $thumb, "idioma" => $images["idioma"]];
        };
        $imagesByCod = Imagenes::checkForProduct($cod, "_");
        $images_array = array_merge($images_array, $imagesByCod);
        $imagesReturn = (count($images_array)) ? $images_array : [["id" => 000000, "url" => URL . "/assets/archivos/sin_imagen.jpg", "thumb" => URL . "/assets/archivos/sin_imagen.jpg"]];
        return $imagesReturn;
    }

    public function viewByCod($cod_producto)
    {
        $array = [];
        $sql = "SELECT * FROM `productos` WHERE  cod_producto = '$cod_producto' LIMIT 1";
        $productos = parent::sqlReturn($sql);
        if ($productos) {
            $row = mysqli_fetch_assoc($productos);
            $row = !empty($row) ? $this->checkPriceByUser($row) : '';
            $array = ["data" => $row];
        }
        return $array;
    }

    public function listVariable($variable)
    {
        $array = [];
        $sql = "SELECT DISTINCT $variable FROM `productos` ORDER BY $variable";
        $var = parent::sqlReturn($sql);
        if ($var) {
            while ($row = mysqli_fetch_assoc($var)) {
                $array[] = array("data" => $row);
            }
        }
        return $array;
    }


    public function listMeli($filter, $order, $limit)
    {
        $array = array();
        $filterSql = (is_array($filter)) ? "WHERE " . implode(" AND ", $filter) : '';

        if ($order != '') {
            $orderSql = $order;
        } else {
            $orderSql = "id DESC";
        }

        $limitSql = ($limit != '') ? "LIMIT " . $limit : "";


        $sql = "SELECT cod FROM `productos` $filterSql ORDER BY $orderSql $limitSql";
        $producto = parent::sqlReturn($sql);
        if ($producto) {
            while ($row = mysqli_fetch_assoc($producto)) {
                $array[] = array("data" => $row);
            }
            return $array;
        }
    }

    //Especiales

    public function reduceStock($cod, $stock, $tipo)
    {
        $idioma = $_SESSION['lang'];
        $query = '';
        if ($tipo == "pr") {
            $sql = "UPDATE `productos` SET `stock`= `stock` - $stock WHERE `cod` = '$cod' AND `idioma` = '$idioma'";
            $query = parent::sqlReturn($sql);
        }
        return !empty($query) ? true : false;
    }

    public static function checkPriceByUser($product)
    {
        $remarcado = (isset($_SESSION["perfil-ecommerce"]["data"]) && $_SESSION["perfil-ecommerce"]["data"]["remarcado_productos"] != null) ? $_SESSION["perfil-ecommerce"]["data"]["remarcado_productos"] : 0;
        $mostrar_precios = (isset($_SESSION["perfil-ecommerce"]["data"]) && $_SESSION["perfil-ecommerce"]["data"]["mostrar_precios"] != null) ? $_SESSION["perfil-ecommerce"]["data"]["mostrar_precios"] : 0;
        $product["precio"] += ((($remarcado / 100) * $product["precio"]));
        $product["precio_descuento"] += (($remarcado / 100) * $product["precio_descuento"]);
        $product["precio_mayorista"] += (($remarcado / 100) * $product["precio_mayorista"]);

        $user = new Usuarios();
        $userSession = (isset($_SESSION["usuarios"]["cod"])) ? $user->refreshSession($_SESSION["usuarios"]["cod"]) : '';
        if ((isset($userSession["minorista"]) && $userSession["minorista"] == 0 && $product['precio_mayorista'] > 0)) {
            $product["precio"]  = $product['precio_mayorista'];
        }
        if (!empty($userSession)) {
            $product["precio"] = is_null($userSession["descuento"]) ? $product['precio'] : $product['precio'] - (($product['precio'] * $userSession['descuento']) / 100);
            $product["precio_final"] = !empty($product['precio_descuento']) && $product['precio_descuento'] > 0 ? (!is_null($userSession["descuento"]) ?  $product['precio_descuento'] - (($product['precio_descuento'] * $userSession['descuento']) / 100)  :  $product["precio_descuento"]) : $product["precio"];
            if ($userSession["minorista"] == 0) {
                if (!empty($product["precio_mayorista"])) {
                    $product["precio_final"] = $product['precio_mayorista'] != '' ? $product['precio_mayorista'] - (($product['precio_mayorista'] * $userSession['descuento']) / 100) : '';
                } else {
                    $product["precio_final"] = empty($userSession["descuento"]) ? $product['precio'] : $product['precio'] - (($product['precio'] * $userSession['descuento']) / 100);
                }
            }
        } else {
            $product["precio_final"] = !empty($product["precio_descuento"]) ? $product["precio_descuento"] : $product["precio"];
        }
        $product["precio_final"] = ($mostrar_precios) ? number_format($product["precio_final"], 2, '.', '') : 0;
        $product["precio"] = ($mostrar_precios) ? $product["precio"] : 0;
        $product["precio_descuento"] = ($mostrar_precios) ? $product["precio_descuento"] : 0;
        $product["precio_mayorista"] = ($mostrar_precios) ? $product["precio_mayorista"] : 0;
        return $product;
    }

    public static function checkFreeShipping($p)
    {

        if ($p["envio_gratis"] == "1" || (isset($p["categoria_free_shipping"]) && $p["categoria_free_shipping"] == "1") ||  (isset($p["subcategoria_free_shipping"]) && $p["subcategoria_free_shipping"] == "1")   || (isset($p["tercercategoria_free_shipping"]) && $p["tercercategoria_free_shipping"] == "1")) {
            $p["envio_gratis"] = 1;
            $p["peso"] = 0;
        }
        return $p;
    }



    public function listCodProduct()
    {
        $sql = "SELECT cod_producto FROM `productos`";
        $producto = parent::sqlReturn($sql);
        if ($producto) {
            while ($row = mysqli_fetch_assoc($producto)) {
                $array[] = $row['cod_producto'];
            }
            return $array;
        }
    }

    public function paginador($filter, $cantidad)
    {
        $filterSql = $this->doAFilter($filter);
        $sql = "SELECT * FROM `productos` $filterSql";
        $contar = parent::sqlReturn($sql);
        $total = mysqli_num_rows($contar);
        $totalPaginas = $total / $cantidad;
        return ceil($totalPaginas);
    }

    public function doAFilter($filters)
    {
        $filter = [];
        if (!empty($filters)) {
            $filterSql = "WHERE ";
            foreach ($filters as $key => $value) {
                switch ($key) {
                    case 'categoria':
                        $categoria = Categorias::list(["filter" => ["cod = $value"]], "", "", $_SESSION['lang'], true);
                        (!empty($categoria)) ? $filter[] = " (categoria='" . $categoria['data']['cod'] . "') " : false;
                        break;
                    case 'subcategoria':
                        $subcategoria = Subcategorias::list(["filter" => ["cod = $value"]], "", "", $_SESSION['lang'], true);
                        (!empty($subcategoria)) ? $filter[] = " (subcategoria='" . $subcategoria['data']['cod'] . "') " : false;
                        break;
                    case 'titulo':
                        $filter[] = " (titulo LIKE '%" . $value . "%')";
                        break;
                }
            }
            $filterSql .= implode(" AND ", $filter);
            return $filterSql;
        } else {
            return '';
        }
    }
    public function viewProductMeliImport($cod)
    {
        $sql = "SELECT * FROM `mercadolibre` WHERE  product = '$cod' ";
        $productos = parent::sqlReturn($sql);
        $row = mysqli_fetch_assoc($productos);
        $array = array("data" => $row);
        return $array;
    }
}
