<?php 

interface CZapSign{

	function newContract (Array $data, String $route, String $method):Array;

	function set_post($curl,String $post,String $route, String $method);
	function set_get($curl,String $data,String $route, String $method);

}

/**
  * ZapSign API
  *
  * Essa classe é uma integração feita com a API da ZapSign
  * uma plataforma de assinatura online de documentos
  * 
  * @subpackage libraries
  * @category   library
  * @version    0.1.0 <alpha>
  * @author     Felipe Rico Gazapina <https://github.com/FelipeGazapina>
  * @link       https://docs.zapsign.com.br/
  */
class ZapSign implements CZapSign{

	private $url_production_api = "https://api.zapsign.com.br/api/v1/";
	private $production_token = "";
	private $state = "development"; # development | production

	public function __construct(){  

    }

	/*
	 * Esta função faz a criação de um contrato considerando oo template_id dentro do array de data
	 * @param Array $data ["template_id"=>"ad896ea9-3c69-48d8-893a-3ee10fdb1e8d","signer_name"=>"João dos Santos","external_id"=>"123","send_automatic_email"=>true,"data"=>[ ["de":"{{EMAIL CLIENTE}}","para":"cliente@gmail.com"],["de":"{{NOME COMPLETO}}","para":"João dos Santos"] ] ]
     * @param String $route ["models/create-doc/"]
     * @param String $method ["POST"]
     * @return Array
	 */
	public function newContract(Array $data,String $route, String $method):Array{
		# PASSANDO DE ARRAY PARA JSON
        $post = json_encode($data);
        $curl = curl_init();

        $curl = $this->set_post($curl,$post,$route,$method);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        $resp = (array) $response;
        $resp = json_decode($resp[0]);
        curl_close($curl);

        if(isset($resp->errors)){
            $err = ["status"=>500,"errors"=>$resp];
        }else{
            $resp = [
                "status"=>200,
                "message"=> "Contrato criado com sucesso",
                "dados" => $resp
            ];
        }

        if ($err) {
        	return $err;
        } else {
        	return $resp;
        }
	}


	/**
     * Função que seta as confirgurações do CURL para method POST | PUT
     *
     * @param [Curl] $curl Próprio CURL
     * @param String $post dados a serem passados através do post
     * @param String $route rota que será enviado
     * @param String $method método que será usado
     * @return $curl \CurlHandle
     */
    private function set_post($curl,String $post,String $route, String $method){
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->url_production_api . $route,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,      
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json; charset=utf-8",
                "Authorization: Bearer " . $this->production_token
            ],
        ]);
        return $curl;
    }

    /**
     * Função que seta as confirgurações do CURL para method GET | DELETE
     *
     * @param [Curl] $curl Próprio CURL
     * @param String $data dados a serem passados através do get
     * @param String $route rota que será enviado
     * @param String $method método que será usado
     * @return $curl \CurlHandle
     */
    private function set_get($curl,String $data,String $route, String $method){
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->url_production_api . $route . $data,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,      
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json; charset=utf-8",
                "Authorization: Bearer " . $this->production_token
            ],
        ]);
        return $curl;

    }
}