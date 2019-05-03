<?php

/**
 * @package Sw0rdfish\Models Contains classes that represent data models to
 *  interact with the database.
 */
namespace Sw0rdfish\Models;

/**
* Represents a user token that can be generated to securely exchange
* information.
*
* A user token can be used to exchange information outside the system to interact
* with it, for instance, when requesting an action to an endpoint, resetting a
* password or confirming an email address for a user that has just been created.
*/
class UserToken extends BaseModel
{
    use SecurityHelpersTrait;

    /**
     * Defines the table name where the user tokens are stored.
     */
    const TABLE_NAME = 'user_tokens';

    /**
    * The valid types of user tokens that can be created.
    *
    * A `session` type token is used for users interacting with the API. A
    * `password_reset` is a token generated when a user requests his password
    * reset. And finally an `email_confirmation` is a token generated when a
    * new account is created.
    */
    const TYPES = ['session', 'password_reset', 'email_confirmation'];

    /**
     * Strength in bits that is used to generate the random token.
     */
    const TOKEN_SIZE = 32;


    /**
     * Strength in bits that is used to generate a security code.
     */
    const SECURITY_CODE_SIZE = 16;


    /**
     * List of validations for this model.
     */
    const VALIDATIONS = [
        'userId' => [
            'presence',
            'numeric' => [
                'greaterThan' => 0
            ]
        ],
        'type' => [
            'presence',
            'inclusion' => self::TYPES
        ],
        'expiration' => [
            'presence'
        ]
    ];

    /**
     * @var int The ID of the user that this token belongs to.
     */
    public $userId;

    /**
     * @var String Type of this token that defines what its purpose is.
     * @see TYPES
     */
    public $type;

    /**
     * @var String A hash containing a securely-random generated token string.
     */
    public $token;

    /**
     * @var \DateTime Date that this token will expire.
     */
    public $expiration;

    /**
     * @var String A string containing a securely-random generated security
     * code. Only used for `password_reset` and `email_confirmation` type of
     * tokens.
     */
    public $securityCode;

    /**
     * Default constructor for UserToken.
     *
     * Please note that some of the arguments given while public will be
     * overriden such as the token itself and the expiration based on the
     * creation date.
     *
     * @param Array args The list of parameters to be set to this instance.
     * @return UserToken A UserToken with the given parameters.
     */
    public function __construct(Array $args = null)
    {
        // initialize if we did not get anything on the ctor
        if (empty($args)) {
            $args = [];
        }

        // override the token, we should set this here ourselves
        if($this->isNew()) {
            $args['token'] = $this->generateSecureToken(self::TOKEN_SIZE);

            if ($this->type != 'session') {
                $args['securityCode'] = $this->generateSecurityCode(
                    self::SECURITY_CODE_SIZE
                );
            } else {
                $args['securityCode'] = null;
            }
        }

        parent::__construct($args);
    }
}
