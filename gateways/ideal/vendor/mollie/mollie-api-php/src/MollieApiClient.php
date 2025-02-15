<?php
    
    namespace Mollie\Api;
    
    use Mollie\Api\Endpoints\BalanceEndpoint;
    use Mollie\Api\Endpoints\BalanceReportEndpoint;
    use Mollie\Api\Endpoints\BalanceTransactionEndpoint;
    use Mollie\Api\Endpoints\ChargebackEndpoint;
    use Mollie\Api\Endpoints\ClientEndpoint;
    use Mollie\Api\Endpoints\CustomerEndpoint;
    use Mollie\Api\Endpoints\CustomerPaymentsEndpoint;
    use Mollie\Api\Endpoints\InvoiceEndpoint;
    use Mollie\Api\Endpoints\MandateEndpoint;
    use Mollie\Api\Endpoints\MethodEndpoint;
    use Mollie\Api\Endpoints\OnboardingEndpoint;
    use Mollie\Api\Endpoints\OrderEndpoint;
    use Mollie\Api\Endpoints\OrderLineEndpoint;
    use Mollie\Api\Endpoints\OrderPaymentEndpoint;
    use Mollie\Api\Endpoints\OrderRefundEndpoint;
    use Mollie\Api\Endpoints\OrganizationEndpoint;
    use Mollie\Api\Endpoints\OrganizationPartnerEndpoint;
    use Mollie\Api\Endpoints\PaymentCaptureEndpoint;
    use Mollie\Api\Endpoints\PaymentChargebackEndpoint;
    use Mollie\Api\Endpoints\PaymentEndpoint;
    use Mollie\Api\Endpoints\PaymentLinkEndpoint;
    use Mollie\Api\Endpoints\PaymentRefundEndpoint;
    use Mollie\Api\Endpoints\PaymentRouteEndpoint;
    use Mollie\Api\Endpoints\PermissionEndpoint;
    use Mollie\Api\Endpoints\ProfileEndpoint;
    use Mollie\Api\Endpoints\ProfileMethodEndpoint;
    use Mollie\Api\Endpoints\RefundEndpoint;
    use Mollie\Api\Endpoints\SettlementPaymentEndpoint;
    use Mollie\Api\Endpoints\SettlementsEndpoint;
    use Mollie\Api\Endpoints\ShipmentEndpoint;
    use Mollie\Api\Endpoints\SubscriptionEndpoint;
    use Mollie\Api\Endpoints\TerminalEndpoint;
    use Mollie\Api\Endpoints\WalletEndpoint;
    use Mollie\Api\Exceptions\ApiException;
    use Mollie\Api\Exceptions\HttpAdapterDoesNotSupportDebuggingException;
    use Mollie\Api\Exceptions\IncompatiblePlatform;
    use Mollie\Api\HttpAdapter\MollieHttpAdapterPicker;
    use Mollie\Api\Idempotency\DefaultIdempotencyKeyGenerator;
    use stdClass;
    
    class MollieApiClient
    {
        /**
         * Version of our client.
         */
        public const CLIENT_VERSION = '2.56.1';
        
        /**
         * Endpoint of the remote API.
         */
        public const API_ENDPOINT = 'https://api.mollie.com';
        
        /**
         * Version of the remote API.
         */
        public const API_VERSION = 'v2';
        
        /**
         * HTTP Methods
         */
        public const HTTP_GET = 'GET';
        public const HTTP_POST = 'POST';
        public const HTTP_DELETE = 'DELETE';
        public const HTTP_PATCH = 'PATCH';
        
        /**
         * @var HttpAdapter\CurlMollieHttpAdapter|HttpAdapter\Guzzle6And7MollieHttpAdapter|HttpAdapter\MollieHttpAdapterInterface
         */
        protected $httpClient;
        
        /**
         * @var string
         */
        protected $apiEndpoint = self::API_ENDPOINT;
        
        /**
         * RESTful Payments resource.
         *
         * @var PaymentEndpoint
         */
        public $payments;
        
        /**
         * RESTful Methods resource.
         *
         * @var MethodEndpoint
         */
        public $methods;
        
        /**
         * @var ProfileMethodEndpoint
         */
        public $profileMethods;
        
        /**
         * RESTful Customers resource.
         *
         * @var CustomerEndpoint
         */
        public $customers;
        
        /**
         * RESTful Customer payments resource.
         *
         * @var CustomerPaymentsEndpoint
         */
        public $customerPayments;
        
        /**
         * RESTful Settlement resource.
         *
         * @var SettlementsEndpoint
         */
        public $settlements;
        
        /**
         * @var
         */
        public $settlementPayments;
        
        /**
         * @var
         */
        public $subscriptions;
        
        /**
         * RESTful Mandate resource.
         *
         * @var MandateEndpoint
         */
        public $mandates;
        
        /**
         * RESTful Profile resource.
         *
         * @var ProfileEndpoint
         */
        public $profiles;
        
        /**
         * RESTful Organization resource.
         *
         * @var OrganizationEndpoint
         */
        public $organizations;
        
        /**
         * RESTful Permission resource.
         *
         * @var PermissionEndpoint
         */
        public $permissions;
        
        /**
         * RESTful Invoice resource.
         *
         * @var InvoiceEndpoint
         */
        public $invoices;
        
        /**
         * RESTful Balance resource.
         *
         * @var BalanceEndpoint
         */
        public $balances;
        
        /**
         * @var BalanceTransactionEndpoint
         */
        public $balanceTransactions;
        
        /**
         * @var BalanceReportEndpoint
         */
        public $balanceReports;
        
        /**
         * RESTful Onboarding resource.
         *
         * @var OnboardingEndpoint
         */
        public $onboarding;
        
        /**
         * RESTful Order resource.
         *
         * @var OrderEndpoint
         */
        public $orders;
        
        /**
         * RESTful OrderLine resource.
         *
         * @var OrderLineEndpoint
         */
        public $orderLines;
        
        /**
         * RESTful OrderPayment resource.
         *
         * @var OrderPaymentEndpoint
         */
        public $orderPayments;
        
        /**
         * RESTful Shipment resource.
         *
         * @var ShipmentEndpoint
         */
        public $shipments;
        
        /**
         * RESTful Refunds resource.
         *
         * @var RefundEndpoint
         */
        public $refunds;
        
        /**
         * RESTful Payment Refunds resource.
         *
         * @var PaymentRefundEndpoint
         */
        public $paymentRefunds;
        
        /**
         * RESTful Payment Route resource.
         *
         * @var PaymentRouteEndpoint
         */
        public $paymentRoutes;
        
        /**
         * RESTful Payment Captures resource.
         *
         * @var PaymentCaptureEndpoint
         */
        public $paymentCaptures;
        
        /**
         * RESTful Chargebacks resource.
         *
         * @var ChargebackEndpoint
         */
        public $chargebacks;
        
        /**
         * RESTful Payment Chargebacks resource.
         *
         * @var PaymentChargebackEndpoint
         */
        public $paymentChargebacks;
        
        /**
         * RESTful Order Refunds resource.
         *
         * @var OrderRefundEndpoint
         */
        public $orderRefunds;
        
        /**
         * Manages Payment Links requests
         *
         * @var PaymentLinkEndpoint
         */
        public $paymentLinks;
        
        /**
         * RESTful Terminal resource.
         *
         * @var TerminalEndpoint
         */
        public $terminals;
        
        /**
         * RESTful Onboarding resource.
         *
         * @var OrganizationPartnerEndpoint
         */
        public $organizationPartners;
        
        /**
         * Manages Wallet requests
         *
         * @var WalletEndpoint
         */
        public $wallets;
        
        /**
         * @var string
         */
        protected $apiKey;
        
        /**
         * True if an OAuth access token is set as API key.
         *
         * @var bool
         */
        protected $oauthAccess;
        
        /**
         * A unique string ensuring a request to a mutating Mollie endpoint is processed only once.
         * This key resets to null after each request.
         *
         * @var string|null
         */
        protected $idempotencyKey = null;
        
        /**
         * @var
         */
        protected $idempotencyKeyGenerator;
        
        /**
         * @var array
         */
        protected $versionStrings = [];
        
        /**
         * RESTful Client resource.
         *
         * @var ClientEndpoint
         */
        public $clients;
        
        /**
         * @param $httpClient
         * @param $httpAdapterPicker
         * @param $idempotencyKeyGenerator
         * @throws Exceptions\UnrecognizedClientException
         * @throws IncompatiblePlatform
         */
        public function __construct($httpClient = null, $httpAdapterPicker = null, $idempotencyKeyGenerator = null)
        {
            $httpAdapterPicker = $httpAdapterPicker ? : new MollieHttpAdapterPicker;
            $this->httpClient = $httpAdapterPicker->pickHttpAdapter($httpClient);
            
            $compatibilityChecker = new CompatibilityChecker;
            $compatibilityChecker->checkCompatibility();
            
            $this->initializeEndpoints();
            $this->initializeVersionStrings();
            $this->initializeIdempotencyKeyGenerator($idempotencyKeyGenerator);
        }
        
        public function initializeEndpoints()
        {
            $this->payments = new PaymentEndpoint($this);
            $this->methods = new MethodEndpoint($this);
            $this->profileMethods = new ProfileMethodEndpoint($this);
            $this->customers = new CustomerEndpoint($this);
            $this->settlements = new SettlementsEndpoint($this);
            $this->settlementPayments = new SettlementPaymentEndpoint($this);
            $this->subscriptions = new SubscriptionEndpoint($this);
            $this->customerPayments = new CustomerPaymentsEndpoint($this);
            $this->mandates = new MandateEndpoint($this);
            $this->balances = new BalanceEndpoint($this);
            $this->balanceTransactions = new BalanceTransactionEndpoint($this);
            $this->balanceReports = new BalanceReportEndpoint($this);
            $this->invoices = new InvoiceEndpoint($this);
            $this->permissions = new PermissionEndpoint($this);
            $this->profiles = new ProfileEndpoint($this);
            $this->onboarding = new OnboardingEndpoint($this);
            $this->organizations = new OrganizationEndpoint($this);
            $this->orders = new OrderEndpoint($this);
            $this->orderLines = new OrderLineEndpoint($this);
            $this->orderPayments = new OrderPaymentEndpoint($this);
            $this->orderRefunds = new OrderRefundEndpoint($this);
            $this->shipments = new ShipmentEndpoint($this);
            $this->refunds = new RefundEndpoint($this);
            $this->paymentRefunds = new PaymentRefundEndpoint($this);
            $this->paymentCaptures = new PaymentCaptureEndpoint($this);
            $this->paymentRoutes = new PaymentRouteEndpoint($this);
            $this->chargebacks = new ChargebackEndpoint($this);
            $this->paymentChargebacks = new PaymentChargebackEndpoint($this);
            $this->wallets = new WalletEndpoint($this);
            $this->paymentLinks = new PaymentLinkEndpoint($this);
            $this->terminals = new TerminalEndpoint($this);
            $this->organizationPartners = new OrganizationPartnerEndpoint($this);
            $this->clients = new ClientEndpoint($this);
        }
        
        protected function initializeVersionStrings()
        {
            $this->addVersionString('Mollie/' . self::CLIENT_VERSION);
            $this->addVersionString('PHP/' . phpversion());
            
            $httpClientVersionString = $this->httpClient->versionString();
            if ($httpClientVersionString) {
                $this->addVersionString($httpClientVersionString);
            }
        }
        
        /**
         * initializeIdempotencyKeyGenerator
         *
         * @param $generator
         * @return void
         */
        protected function initializeIdempotencyKeyGenerator($generator)
        {
            $this->idempotencyKeyGenerator = $generator ? : new DefaultIdempotencyKeyGenerator;
        }
        
        /**
         * @param string $url
         *
         * @return MollieApiClient
         */
        public function setApiEndpoint($url)
        {
            $this->apiEndpoint = rtrim(trim($url), '/');
            
            return $this;
        }
        
        /**
         * @return string
         */
        public function getApiEndpoint()
        {
            return $this->apiEndpoint;
        }
        
        /**
         * @return array
         */
        public function getVersionStrings()
        {
            return $this->versionStrings;
        }
        
        /**
         * @param string $apiKey The Mollie API key, starting with 'test_' or 'live_'
         *
         * @return MollieApiClient
         * @throws ApiException
         */
        public function setApiKey($apiKey)
        {
            $apiKey = trim($apiKey);
            
            if (!preg_match('/^(live|test)_\w{30,}$/', $apiKey)) {
                throw new ApiException("Invalid API key: '{$apiKey}'. An API key must start with 'test_' or 'live_' and must be at least 30 characters long.");
            }
            
            $this->apiKey = $apiKey;
            $this->oauthAccess = false;
            
            return $this;
        }
        
        /**
         * @param string $accessToken OAuth access token, starting with 'access_'
         *
         * @return MollieApiClient
         * @throws ApiException
         */
        public function setAccessToken($accessToken)
        {
            $accessToken = trim($accessToken);
            
            if (!preg_match('/^access_\w+$/', $accessToken)) {
                throw new ApiException("Invalid OAuth access token: '{$accessToken}'. An access token must start with 'access_'.");
            }
            
            $this->apiKey = $accessToken;
            $this->oauthAccess = true;
            
            return $this;
        }
        
        /**
         * Returns null if no API key has been set yet.
         *
         * @return bool|null
         */
        public function usesOAuth()
        {
            return $this->oauthAccess;
        }
        
        /**
         * @param string $versionString
         *
         * @return MollieApiClient
         */
        public function addVersionString($versionString)
        {
            $this->versionStrings[] = str_replace([' ', "\t", "\n", "\r"], '-', $versionString);
            
            return $this;
        }
        
        /**
         * enableDebugging
         *
         * @return void
         * @throws HttpAdapterDoesNotSupportDebuggingException
         */
        public function enableDebugging()
        {
            if (
                !method_exists($this->httpClient, 'supportsDebugging')
                || !$this->httpClient->supportsDebugging()
            ) {
                throw new HttpAdapterDoesNotSupportDebuggingException(
                    'Debugging is not supported by ' . get_class($this->httpClient) . '.'
                );
            }
            
            $this->httpClient->enableDebugging();
        }
        
        /**
         * disableDebugging
         *
         * @return void
         * @throws HttpAdapterDoesNotSupportDebuggingException
         */
        public function disableDebugging()
        {
            if (
                !method_exists($this->httpClient, 'supportsDebugging')
                || !$this->httpClient->supportsDebugging()
            ) {
                throw new HttpAdapterDoesNotSupportDebuggingException(
                    'Debugging is not supported by ' . get_class($this->httpClient) . '.'
                );
            }
            
            $this->httpClient->disableDebugging();
        }
        
        /**
         * Set the idempotency key used on the next request. The idempotency key is a unique string ensuring a request to a
         * mutating Mollie endpoint is processed only once. The idempotency key resets to null after each request. Using
         * the setIdempotencyKey method supersedes the IdempotencyKeyGenerator.
         *
         * @param $key
         * @return $this
         */
        public function setIdempotencyKey($key)
        {
            $this->idempotencyKey = $key;
            
            return $this;
        }
        
        /**
         * Retrieve the idempotency key. The idempotency key is a unique string ensuring a request to a
         * mutating Mollie endpoint is processed only once. Note that the idempotency key gets reset to null after each
         * request.
         *
         * @return string|null
         */
        public function getIdempotencyKey()
        {
            return $this->idempotencyKey;
        }
        
        /**
         * Reset the idempotency key. Note that the idempotency key automatically resets to null after each request.
         * @return $this
         */
        public function resetIdempotencyKey()
        {
            $this->idempotencyKey = null;
            
            return $this;
        }
        
        /**
         * setIdempotencyKeyGenerator
         *
         * @param $generator
         * @return $this
         */
        public function setIdempotencyKeyGenerator($generator)
        {
            $this->idempotencyKeyGenerator = $generator;
            
            return $this;
        }
        
        /**
         * clearIdempotencyKeyGenerator
         *
         * @param $generator
         * @return $this
         */
        public function clearIdempotencyKeyGenerator($generator)
        {
            $this->idempotencyKeyGenerator = null;
            
            return $this;
        }
        
        /**
         * performHttpCall
         *
         * @param $httpMethod
         * @param $apiMethod
         * @param $httpBody
         * @return stdClass|null
         * @throws ApiException
         */
        public function performHttpCall($httpMethod, $apiMethod, $httpBody = null)
        {
            $url = $this->apiEndpoint . '/' . self::API_VERSION . '/' . $apiMethod;
            
            return $this->performHttpCallToFullUrl($httpMethod, $url, $httpBody);
        }
        
        /**
         * Perform a http call to a full url. This method is used by the resource specific classes.
         *
         * @param string $httpMethod
         * @param string $url
         * @param string|null $httpBody
         *
         * @return stdClass|null
         * @throws ApiException
         *
         * @codeCoverageIgnore
         * @see $isuers
         *
         * @see $payments
         */
        public function performHttpCallToFullUrl($httpMethod, $url, $httpBody = null)
        {
            if (empty($this->apiKey)) {
                throw new ApiException('You have not set an API key or OAuth access token. Please use setApiKey() to set the API key.');
            }
            
            $userAgent = implode(' ', $this->versionStrings);
            
            if ($this->usesOAuth()) {
                $userAgent .= ' OAuth/2.0';
            }
            
            $headers = [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$this->apiKey}",
                'User-Agent' => $userAgent,
            ];
            
            if ($httpBody !== null) {
                $headers['Content-Type'] = 'application/json';
            }
            
            if (function_exists('php_uname')) {
                $headers['X-Mollie-Client-Info'] = php_uname();
            }
            
            if (in_array($httpMethod, [self::HTTP_POST, self::HTTP_PATCH, self::HTTP_DELETE])) {
                if (!$this->idempotencyKey && $this->idempotencyKeyGenerator) {
                    $headers['Idempotency-Key'] = $this->idempotencyKeyGenerator->generate();
                }
                
                if ($this->idempotencyKey) {
                    $headers['Idempotency-Key'] = $this->idempotencyKey;
                } elseif ($this->idempotencyKeyGenerator) {
                    $headers['Idempotency-Key'] = $this->idempotencyKeyGenerator->generate();
                }
            }
            
            $response = $this->httpClient->send($httpMethod, $url, $headers, $httpBody);
            
            $this->resetIdempotencyKey();
            
            return $response;
        }
        
        /**
         * Serialization can be used for caching. Of course doing so can be dangerous but some like to live dangerously.
         *
         * \serialize() should be called on the collections or object you want to cache.
         *
         * We don't need any property that can be set by the constructor, only properties that are set by setters.
         *
         * Note that the API key is not serialized, so you need to set the key again after unserializing if you want to do
         * more API calls.
         *
         * @return string[]
         * @deprecated
         */
        public function __sleep()
        {
            return ['apiEndpoint'];
        }
        
        /**
         * __wakeup
         *
         * @return void
         * @throws Exceptions\UnrecognizedClientException
         * @throws IncompatiblePlatform
         */
        public function __wakeup()
        {
            $this->__construct();
        }
    }
