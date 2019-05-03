<?php

namespace Sw0rdfish\Services;

use \Sw0rdfish\Models\User;
use \Sw0rdfish\Models\UserToken;
use \Sw0rdfish\Errors\BadRequestError;

/**
 * A service object that allows the management of user tokens.
 */
class CreateTokenService
{
    /**
     * @var ServerRequestInterface The incoming request with the token creation
     *  data.
     */
    public $request;

    /**
     * @var Array An array of error details if the operation fails.
     */
    public $errors;

    /**
     * @var Array An array of generic informational messages to be sent to the
     *  user.
     */
    public $messages;

    /**
     * @var mixed Any specific output object that we may want to expose to the
     *  user.
     */
    public $output;

    /**
     * Creates a new instance of the token manager service.
     *
     * @param ServerRequestInterface $request The incoming request with the token
     *  data
     */
    function __construct($request)
    {
        $this->request = $request;
        $this->errors = [];
        $this->messages = [];
    }

    /**
     * Executes the main action of this service which is creating a token. If it
     * fails will return FALSE or TRUE otherwise.
     *
     * @return Boolean Will return true if the operation succeeds, false
     *  otherwise.
     */
    public function perform()
    {
        $this->reload();
        $request = $this->request;
        $type = $this->validTokenType($request);

        switch ($type) {
            case 'session':
                $this->output = $this->createSessionToken($request);
                break;
            case 'password_reset':
                $this->output = $this->createPasswordResetToken($request);
                break;
            case 'email_confirmation':
                $this->output = $this->createEmailConfirmationToken($request);
                break;
            default:
                break;
        }

        return empty($this->errors);
    }

    /**
     * Resets the service properties.
     */
    private function reload()
    {
        $this->errors = [];
        $this->messages = [];
        $this->output = null;
    }

    /**
     * Checks whether the incoming request token data type is valid or not and
     * will add to the errors details if it is not.
     *
     * @param ServerRequestInterface The incoming request.
     * @return String The token type that came in the incoming request.
     */
    private function validTokenType($request)
    {
        $params = $request->getParsedBody();

        if (in_array($params['type'], UserToken::TYPES) == false) {
            array_push(
                $this->errors,
                "Invalid token type '$params[type]': you must specify a valid token type"
            );
        }

        return $params['type'];
    }

    /**
     * Creates a session type token from the incoming request data.
     *
     * @param ServerRequestInterface The incoming request.
     * @return \Sw0rdfish\Models\UserToken The newly-created session token.
     */
    private function createSessionToken($request)
    {
        $params = $request->getParsedBody();
        $email = $params['email'];
        $password = $params['password'];

        return $this->createToken(
            'session',
            [
                'where' => [
                    'email' => $email,
                    'password' => $password
                ]
            ],
            true
        );
    }


    /**
     * Creates a password-reset type token from the incoming request data.
     *
     * @param ServerRequestInterface The incoming request.
     * @return \Sw0rdfish\Models\UserToken The newly-created session token.
     */
    private function createPasswordResetToken($request)
    {
        $params = $request->getParsedBody();
        $email = $params['email'];

        $token = $this->createToken(
            'password_reset',
            [
                'where' => [
                    'email' => $email
                ]
            ],
            false
        );

        if ($token->isNew() == false) {
            // TODO:
            // 1- send email with security code
        }

        return $token;
    }

    /**
     * Creates an email-configmration type token from the incoming request data.
     *
     * @param ServerRequestInterface The incoming request.
     * @return \Sw0rdfish\Models\UserToken The newly-created session token.
     */
    private function createEmailConfirmationToken($request)
    {
        $params = $request->getParsedBody();
        $email = $params['email'];

        $token = $this->createToken(
            'email_confirmation',
            [
                'where' => [
                    'email' => $email
                ]
            ],
            false
        );

        if ($token->isNew() == false) {
            // TODO:
            // 1- send email with security code
        }

        return $token;
    }

    /**
     * Creates a new UserToken based on the given parameters. Since some type of
     * tokens require that we do not hint the user wether credentials were wrong
     * or not this function supports a parameter to set if we want to cause such
     * an error.
     *
     * @param String $type The type of UserToken to create.
     * @param Array $userArgs An array of user conditions or filter to find the
     *  user that this token will belong to.
     * @param Boolean $causeError If set to true then we will raise an error if
     *  the user filter args don't match any existing user.
     * @return \Sw0rdfish\Models\UserToken The newly-created token for the given
     *  type.
     */
    private function createToken($type, Array $userArgs, $causeError = false)
    {
        $users = User::all($userArgs);
        $userId = null;

        $now = new \DateTime();
        $expiration = getenv('EXPIRATION_IN_HOURS');
        $now->add(new \DateInterval("PT{$expiration}H"));
        $expirationDate = $now->format('Y-m-d H:i:s');

        if (empty($users) && $causeError) {
            array_push($this->errors, 'Wrong credentials');
            return;
        } elseif (!empty($users)) {
            $userId = $users[0]->id;
        }

        $token = new UserToken([
            'userId' => $userId,
            'type' => $type,
            'expiration' => $expirationDate
        ]);

        if (empty($users) == false) {
            if($token->save() == false) {
                array_push($this->errors, $token->getValidationErrors());
            }
        }

        return $token;
    }
}
