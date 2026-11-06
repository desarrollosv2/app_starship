<?php
class OpcionController extends CI_Controller
{
    public function __construct()
    {

    }
    public function listado_lmd()
    {
        $request = new HttpRequest();
        $request->setUrl('https://apis-sandbox.fedex.com/address/v1/addresses/resolve');
        $request->setMethod(HTTP_METH_POST);

        $request->setHeaders(array(
            'Authorization' => 'Bearer ',
            'X-locale' => 'en_US',
            'Content-Type' => 'application/json'
        ));

        $request->setBody(input); // 'input' refers to JSON Payload

        try {
            $response = $request->send();

            echo $response->getBody();
        } catch (HttpException $ex) {
            echo $ex;
        }
    }
}
