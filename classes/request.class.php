<?php

/**
 * Class to prototype the request used by the service
 * User: alexandre
 * Date: 27/03/16
 * Time: 10:20
 */
class request
{

    const GET = "GET";

    const POST = "POST";

    /**
     * @var string
     */
    private $method;
    /**
     * @var string
     */
    private $origin;
    /**
     * @var string
     */
    private $destination;
    /**
     * @var float
     */
    private $gasValue;

    /**
     * @var float
     */
    private $autonomy;

    /**
     * @var array
     */
    public $validOrigins;

    /**
     * @var array
     */
    public $validDestinations;

    public static $mandatoryFields = array("origin", "destination", "gas_value", "autonomy");

    /**
     * request constructor.
     * @param $method
     * @param array $validOrigins
     * @param array $validDestinations
     */
    public function __construct($method, array $validOrigins, array $validDestinations)
    {
        $this->validOrigins = $validOrigins;
        $this->validDestinations = $validDestinations;

        if( strcasecmp( $method, 'post' ) ===0 ) {
            return $this->generateFromPost();
        }
        elseif ( strcasecmp( $method, 'get' ) ===0 ) {
            return $this->generateFromGet();
        }
        else{
            throw new Exception("Type of Argument is invalid for this service");
        }
    }

    /**
     * @return $this
     * @throws HttpInvalidParamException
     */
    private function generateFromPost()
    {
        $this->setMethod(self::POST);
       if(!$this->validateRequestInfo(self::POST)){
           throw new Exception("Required Fields not sent");
       }
        $this->generateAttributes($_POST);
        return $this;

    }

    /**
     * @return $this
     * @throws HttpQueryStringException
     */
    private function generateFromGet()
    {
        $this->setMethod(self::GET);
        if(!$this->validateRequestInfo(self::GET)){
            throw new Exception("Required params not sent");
        }
        $this->generateAttributes($_GET);
        return $this;
    }

    private function generateAttributes( array $superGlobalArray) {
        $this->setOrigin($superGlobalArray['origin'])
            ->setDestination($superGlobalArray['destination'])
            ->setGasValue($superGlobalArray['gas_value'])
            ->setAutonomy($superGlobalArray['autonomy']);
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return request
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @param string $origin
     * @return request
     */
    public function setOrigin($origin)
    {
        if(!in_array($origin, $this->validOrigins)) {
            throw new Exception("The Origin is not Valid");
        }
        $this->origin = $origin;
        return $this;
    }

    /**
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param string $destination
     * @return request
     */
    public function setDestination($destination)
    {
        if(!in_array($destination, $this->validDestinations)) {
            throw new Exception("The Destination is not Valid");
        }
        $this->destination = $destination;
        return $this;
    }

    /**
     * @return float
     */
    public function getGasValue()
    {
        return $this->gasValue;
    }

    /**
     * @param float $gasValue
     * @return request
     */
    public function setGasValue($gasValue)
    {
        if(!is_numeric($gasValue)) {
            throw new Exception("The combustible price must be a number");
        }
        $this->gasValue = (float) $gasValue;
        return $this;
    }

    /**
     * @param string $requestMethod
     * @return bool
     */
    private function validateRequestInfo( $requestMethod = self::GET )
    {
        $arrayFields =$_GET ;
        if( strcasecmp( $requestMethod, self::POST ) === 0 ) {
            $arrayFields = $_POST ;
        }
        if( !$this->assureMandatoryFields( $arrayFields ) ) {
            return false;
        }
        return $this->mandatoryFieldsFilled( $arrayFields );

    }

    /**
     * @param array $arrayFields
     * @return bool
     */
    private function assureMandatoryFields( array $arrayFields)
    {

        $fields = array_keys($arrayFields);
        if( !empty( array_diff(self::$mandatoryFields, $fields) ) ) {
            return false;
        }
        return true;
    }

    /**
     * @param array $arrayFields
     * @return bool
     */
    private function mandatoryFieldsFilled( array $arrayFields ) {
        foreach( $arrayFields as $field) {
            if( empty($field) ) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return float
     */
    public function getAutonomy()
    {
        return $this->autonomy;
    }

    /**
     * @param float $autonomy
     */
    public function setAutonomy($autonomy)
    {
        if(!is_numeric($autonomy)) {
            throw new Exception("The Vehicle autonomy must be a numeric Value");
        }
        $this->autonomy = (float) $autonomy;
        return $this;
    }



}