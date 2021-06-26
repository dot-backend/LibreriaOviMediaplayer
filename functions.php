<?php

$_POST = json_decode(file_get_contents('php://input'), true);

class ReadFolder
{

    private $struct;
    private $dir;
    private $extent;
    private $number_dir = 0;

    function __construct($_dir)
    {
        $this->dir = $_dir;
    }

    /**
     * Metodo que retorna todo el contenido de una carpeta 
     * @return this
     */
    public function build()
    {
        if (is_dir($this->dir)) {
            if ($dh = opendir($this->dir)) {
                while (($namefile = readdir($dh)) !== false) {
                    if ($namefile != "." && $namefile != "..") {
                        $this->struct[] = array("folder" => $namefile);
                        $is_folder = $this->struct[$this->number_dir]["folder"];
                        if (is_dir($this->dir . "/" . $is_folder)) {
                            $this->struct($this->number_dir);
                        }
                        $this->number_dir++;
                    }
                }
                closedir($dh);
            }
        }
        return $this;
    }

    /**
     * Metodo que estructura los archivos por carpetas
     * @param int $carpeta
     * @return array
     */
    public function struct($i = null)
    {
        if (!is_null($i)) {
            if ($dh = opendir($this->dir . "/" . $this->struct[$i]["folder"])) {
                while (($namefile = readdir($dh)) !== false) {
                    if ($namefile !== "." && $namefile !== "..") {
                        $info = new SplFileInfo($namefile);
                        $info->getExtension() === $this->extent ?
                            $this->struct[$this->number_dir][] = array(
                                "path" => $this->dir . "/" . $this->struct[$this->number_dir]["folder"] . "/" . $namefile,
                                "name"=>$namefile)
                            : "";
                    }
                }
                closedir($dh);
            }
        }
    }

    /**
     * Metodo define la extensión de los archivos
     * @param String $extent
     * @return this
     */
    public function type($extent)
    {
        $this->extent = $extent;
        return $this;
    }

    /**
     * Metodo que retorna todos los archivos
     * @return $struct
     */
    public function get()
    {
        return $this->struct;
    }
}

if (isset($_POST["nameFolder"]) && isset($_POST["extent"])) {
    $readFolder = new ReadFolder($_POST["nameFolder"]);
    $response = $readFolder->type($_POST["extent"])
                            ->build()
                            ->get();
    echo json_encode($response);
}
