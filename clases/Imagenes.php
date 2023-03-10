<?php

namespace Clases;

use Exception;
use Verot\Upload\Upload;

class Imagenes extends Conexion
{

    //Atributos
    public $id;
    public $link;
    public $ruta;
    public $orden;
    public $cod;
    public $idioma;

    //Metodos
    public function __construct()
    {
        $this->f = new PublicFunction();
    }

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

    public function add()
    {
        $sql = "INSERT INTO `imagenes`(`ruta`, `cod`, `orden`, `idioma`) VALUES ({$this->ruta}, {$this->cod},0,{$this->idioma})";
        $query = parent::sql($sql);
        return (!empty($query)) ? true : false;
    }

    public function edit()
    {
        $sql = "UPDATE `imagenes` SET ruta = {$this->ruta}, cod = {$this->cod} WHERE `id`={$this->id}";
        $query = parent::sql($sql);

        return (!empty($query)) ? true : false;
    }

    public function editAllCod($cod)
    {
        $sql = "UPDATE `imagenes` SET cod = {$this->cod} WHERE `cod`='$cod'";
        $query = parent::sql($sql);
        return (!empty($query)) ? true : false;
    }

    public function delete($array)
    {
        $sql = "SELECT * FROM `imagenes` WHERE id=:id AND idioma=:idioma";
        $stmt = parent::conPDO()->prepare($sql);
        $stmt->execute($array);
        while ($row = $stmt->fetch()) {
            try {
                $sql = "SELECT * FROM `imagenes` WHERE ruta =:ruta AND idioma !=:idioma ORDER BY cod DESC";
                $stmt2 = parent::conPDO()->prepare($sql);
                $stmt2->execute($row);
                $response = true;
            } catch (Exception $e) {
                $response["error"] = $e->getMessage();
            }
            if (empty($stmt->fetchAll())) {
                $file = explode(".", $row["ruta"]);
                $files = $row["ruta"];
                $filesx1 = $file[0] . "_x1." . $file[1];
                $filesx2 = $file[0] . "_x2." . $file[1];
                @unlink("../" . $files);
                @unlink("../" . $filesx1);
                @unlink("../" . $filesx2);
            }
            try {
                $sqlDelete = "DELETE FROM `imagenes` WHERE id=:id AND idioma=:idioma";
                $stmt = parent::conPDO()->prepare($sqlDelete);
                $stmt->execute($array);
                $response = true;
            } catch (Exception $e) {
                $response["error"] = $e->getMessage();
            }
            return $response;
        }
    }

    public static function deleteAll($array)
    {
        $sql = "SELECT * FROM `imagenes` WHERE cod=:cod AND idioma=:idioma ORDER BY cod DESC";
        $stmt = parent::conPDO()->prepare($sql);
        $stmt->execute($array);
        while ($row = $stmt->fetch()) {
            unset($row["orden"], $row["id"], $row["cod"]);
            try {
                $sql = "SELECT * FROM `imagenes` WHERE ruta =:ruta AND idioma !=:idioma ORDER BY cod DESC";
                $stmt2 = parent::conPDO()->prepare($sql);
                $stmt2->execute($row);
                $response = true;
            } catch (Exception $e) {
                $response["error"] = $e->getMessage();
            }
            if (empty($stmt2->fetchAll())) {
                $file = explode(".", $row["ruta"]);
                $files = $row["ruta"];
                $filesx1 = $file[0] . "_x1." . $file[1];
                $filesx2 = $file[0] . "_x2." . $file[1];
                unlink("../" . $files);
                @unlink("../" . $filesx1);
                @unlink("../" . $filesx2);
            }
        }

        try {
            $sqlDelete = "DELETE FROM `imagenes` WHERE cod=:cod AND idioma=:idioma";
            $stmt = parent::conPDO()->prepare($sqlDelete);
            $stmt->execute($array);
            $response = true;
        } catch (Exception $e) {
            $response['error'] = ($_ENV["DEBUG"]) ? (array)$e : $e->getMessage();
        }
        return $response;
    }

    public function view($cod)
    {
        $idioma = str_replace("''", "'", $this->idioma);
        $sql = ($this->idioma) != '' ?  "SELECT * FROM `imagenes` WHERE `cod` = '$cod' AND `idioma` = $idioma ORDER BY id ASC" :  "SELECT * FROM `imagenes` WHERE `cod` = '$cod' ORDER BY id ASC";
        $imagenes = parent::sqlReturn($sql);
        if (!empty($imagenes)) {
            $row = mysqli_fetch_assoc($imagenes);
        } else {
            $row = false;
        }
        return $row;
    }


    /**
     *
     * Mandamos la ruta de una imagen y nos de vuelve la misma pero con sus tamaños inferiores
     *
     * @param    string  $variable un string con la ruta de la imagen
     * @return    array retorna un array con 2 opciones $variable["x1"] y variable["x2"]
     *
     */


    function     selectImageSize($variable)
    {
        $variable = str_replace(".jpg", "", $variable);
        $urlx1 = dirname(__DIR__) . "/" .  $variable . '_x1.jpg';
        $urlx2 = dirname(__DIR__) . "/" .  $variable . '_x2.jpg';
        $ruta["x1"] =  (@getimagesize($urlx1) ?  $variable . '_x1.jpg' :  'assets/archivos/sin_imagen.jpg');
        $ruta["x2"] =  (@getimagesize($urlx2) ?  $variable . '_x2.jpg' :  'assets/archivos/sin_imagen.jpg');
        return $ruta;
    }

    public static function list($filter, $order = '', $limit = '', $single = false)
    {
        $array = array();
        $array_ = array();
        foreach ($filter as $key => $value) {
            $filters[] = $key . "=:" . $key;
        }
        $filterSql = implode(" AND ", $filters);
        $orderSql = ($order != '') ?  $order  : "`orden` ASC";
        $limitSql = ($limit != '') ? "LIMIT " . $limit : '';
        $sql = "SELECT * FROM `imagenes` WHERE $filterSql ORDER BY $orderSql $limitSql";
        try {
            $stmt = parent::conPDO()->prepare($sql);
            $stmt->execute($filter);
            while ($row = $stmt->fetch()) {
                $array_ = $row;
                $array[] = $array_;
            }
            $response = ($single) ? $array_ : $array;
        } catch (Exception $e) {
            $response["error"] = $e->getMessage();
        }
        return $response;
    }



    public static function checkForProduct($variable)
    {
        $images = [];
        if (strpos($variable, '|')) {
            $variable = explode('|', $variable)[0];
        }
        $matches = !empty($variable) ? glob(dirname(__DIR__) . '/assets/archivos/productos/' . $variable . '[!{_thumb}]*') : '';
        if (!empty($variable)) $matches = array_merge($matches, glob(dirname(__DIR__) . '/assets/archivos/productos/' . $variable . '_*[!{_thumb}].*'));
        if (is_array($matches)) $matches = (array_filter($matches));
        if (is_array($matches)) {
            foreach ($matches as $key => $filename) {
                $img = URL . "/assets/" . explode("/assets/", $filename)[1];
                $thumb = $img;
                $thumbImg = str_replace('.' . $_ENV["TYPE_IMG"], '_thumb.' . $_ENV["TYPE_IMG"], $filename);
                if (file_exists($thumbImg)) {
                    $thumb = URL . "/assets/" . explode("/assets/", $thumbImg)[1];
                }
                $images[] = ["id" => 'byCod', "url" => $img, "thumb" => $thumb];
            }
        }
        return $images;
    }


    public function listValidation($cod)
    {
        $array = array();
        $sql = "SELECT * FROM `imagenes` WHERE cod = '$cod' ORDER BY id ASC";
        $imagenes = parent::sqlReturn($sql);
        if ($imagenes->num_rows == 0) {
            return false;
        } else {
            while ($row = mysqli_fetch_assoc($imagenes)) {
                $array[] = $row;
            }
            return $array;
        }
    }


    public function setOrder()
    {
        $sql = "UPDATE `imagenes` SET orden = {$this->orden} WHERE id = {$this->id}";
        $query = parent::sql($sql);

        return (!empty($query)) ? true : false;
    }

    public function resizeImages($cod, $files, $path, $final_name = "", $idioma, $thumb = false)
    {
        foreach ($files['name'] as $f => $name) {
            $name = (!empty($final_name)) ? $final_name : $cod;
            $file = [
                "name" => $files["name"][$f],
                "type" => $files["type"][$f],
                "tmp_name" => $files["tmp_name"][$f],
                "error" => $files["error"][$f],
                "size" => $files["size"][$f]
            ];
            $final_name_image = PublicFunction::normalizar_titulo_imagenes($name) . "_" . substr(md5(uniqid(rand())), 0, 10);
            $handle = new Upload($file);
            $handle->uploaded;
            foreach ($idioma as $idiomaItem) {
                $lang = isset($idiomaItem['data']['cod']) ? $idiomaItem['data']['cod'] : $idiomaItem;
                $newName = $final_name_image . "_" . $lang;
                $handle->file_new_name_body   = $newName;
                $handle->file_auto_rename     = false;
                $handle->file_overwrite       = true;
                $handle->image_resize         = true;
                $handle->image_x              = 1920;
                $handle->image_ratio_y        = true;
                $handle->image_no_enlarging = true;
                $handle->webp_quality = 70;
                $handle->image_convert = $_ENV["TYPE_IMG"];
                $handle->process(dirname(__DIR__, 1) . '/' . $path);
                if ($handle->processed) {
                    $final_path = $path . '/' . $newName . '.' . $_ENV["TYPE_IMG"];
                    $this->set("cod", $cod);
                    $this->set("ruta",  $final_path);
                    $this->set("idioma",  $lang);
                    $this->add();
                    if ($thumb) {
                        $handle->file_new_name_body   = $newName . '_thumb';
                        $handle->file_auto_rename     = false;
                        $handle->file_overwrite       = true;
                        $handle->image_resize         = true;
                        $handle->image_x              = 400;
                        $handle->image_ratio_y        = true;
                        $handle->image_no_enlarging = true;
                        $handle->webp_quality = 85;
                        $handle->image_convert = $_ENV["TYPE_IMG"];
                        $handle->process(dirname(__DIR__, 1) . '/' . $path);
                        $handle->processed;
                    }
                }
            }
            $handle->clean();
        }
    }


    public function uploadFileInFolder($files, $path, $final_name)
    {
        $file = [
            "name" => $files["name"],
            "type" => $files["type"],
            "tmp_name" => $files["tmp_name"],
            "error" => $files["error"],
            "size" => $files["size"]
        ];
        $handle = new Upload($file);
        $handle->uploaded;
        $size = 1920;
        $name = $name = str_replace([".png", ".jpg", ".jpeg", ".gif"], "", $final_name);
        for ($i = 0; $i <= 1; $i++) {
            if ($i == 1) {
                $name = $name . "_thumb";
                $size = 400;
            }
            $handle->file_new_name_body   = $name;
            $handle->file_auto_rename     = false;
            $handle->file_overwrite       = true;
            $handle->image_x              = $size;
            $handle->image_resize         = true;
            $handle->image_ratio_y        = true;
            $handle->image_no_enlarging   = true;
            $handle->webp_quality = 85;
            $handle->image_convert = $_ENV["TYPE_IMG"];
            $handle->process(dirname(__DIR__, 1) . '/' . $path);
        }
        $handle->clean();
    }


    function     selectSize($file_image, $x = false)
    {
        $file = explode(".", $file_image);
        $name_file = ($x) ? $file[0] . "_x$x." . $file[1] : $file_image;
        return $name_file;
    }



    function     buildEditImagesAdmin($images, $multiple = true)
    {
        if ($multiple) { ?>
            <div class='col-12 my-2'>
                <div class="row" id="img-row">
                    <?php if (!empty($images)) {
                        foreach ($images as $key => $img) {
                            if (isset($img['ruta'])) $img['url'] = URL . "/" . $img['ruta'];
                            if (isset($img['id']) && $img['id'] != 'byCod' && $img['url'] != URL . "/assets/archivos/sin_imagen.jpg") {  ?>
                                <div class='col-md-2 mb-20 mt-20' id="img-<?= $img['id'] ?>">
                                    <div style="height:220px;background:url('<?= $img['url'] ?>') no-repeat center center/contain;"> </div>
                                    <div class="row">
                                        <div class="col-md-6 mt-10">
                                            <a onclick="deleteImg('<?= $img['id'] ?>','<?= $img['idioma'] ?>')" class="btn btn-sm btn-block btn-danger text-white">
                                                <div class="fonticon-wrap">
                                                    <i class="bx bx-trash fs-20"></i>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-md-6 mt-10">
                                            <select onchange="changeOrderImg('<?= $img['id'] ?>',$(this).val(),'<?= URL_ADMIN ?>')">
                                                <?php for ($i = 0; $i < count($images); $i++) { ?>
                                                    <option value='<?= $i ?>' <?= ($img['orden'] == $i) ? 'selected' : '' ?>><?= $i ?></option>
                                                <?php  } ?>
                                            </select>
                                            <i>orden</i>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            <?php } else { ?>
                                <?php if ($img['url'] != URL . "/assets/archivos/sin_imagen.jpg") { ?>
                                    <div class='col-md-2 mb-20 mt-20' id="img-<?= $key ?>">
                                        <div style="height:200px;background:url('<?= $img['url'] ?>') no-repeat center center/contain;"></div>
                                        <div class="row">
                                            <div class="col-md-12 mt-10">
                                                <a onclick="deleteImg('<?= $img['url'] ?>','byCod-<?= $key ?>')" class="btn btn-sm btn-block btn-danger text-white">
                                                    BORRAR IMAGEN
                                                </a>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                    <?php
                                }
                            }
                        }
                    }
                    ?>
                    <div class="col-12">
                        Imágenes:<br />
                        <input type="file" id="file" name="files[]" multiple="multiple" onchange="filePreview(this)" accept="image/*" />
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="row" id="img-row">
                <?php if (isset($images) && !empty($images)) {
                    if (isset($images['ruta'])) $images['url'] = URL . "/" . $images['ruta'];
                ?>
                    <div class='col-md-2 mb-20 mt-20' id="img-<?= $images['id'] ?>">
                        <div style="height:200px;background:url('<?= $images['url'] ?>') no-repeat center center/contain;"></div>
                        <div class="mt-6">
                            <a onclick="deleteImg('<?= $images['id'] ?>','<?= $images['idioma'] ?>',true)" class="btn btn-sm btn-block btn-danger text-white">
                                <div class="fonticon-wrap">
                                    <i class="bx bx-trash fs-20"></i>
                                </div>
                            </a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                <?php } else { ?>
                    <div class="col-md-7">Imágen:<br />
                        <input type="file" id="file" name="files[]" accept="image/*" />
                    </div>
                <?php } ?>
            </div>
        <?php
        }
        ?>
        <div id="preview-images" class="mb-2">
        </div>
<?php
    }
}
