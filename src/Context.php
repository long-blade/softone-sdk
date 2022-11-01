<?php

namespace SoftOne;

use SoftOne\Exception\MissingArgumentException;
use SoftOne\Exception\UninitializedContextException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Context
{
    /**
     * @var string
     */
    public static string $URL;
    /**
     * @var string
     */
    public static string $USERNAME;
    /**
     * @var string
     */
    public static string $PASSWORD;
    /**
     * @var string
     */
    public static string $APP_ID;
    /**
     * @var string|null
     */
    public static ?string $COMPANY = null;
    /**
     * @var string|null
     */
    public static ?string $BRANCH = null;
    /**
     * @var string|null
     */
    public static ?string $MODULE = null;
    /**
     * @var string|null
     */
    public static ?string $REFID = null;
    /**
     * @var bool
     */
    private static bool $IS_INITIALIZED = false;
    /**
     * @var LoggerInterface|null
     */
    public static ?LoggerInterface $LOGGER = null;
    /**
     * @var string
     */
    public static string $CLIENT_ID = '';

    /**
     * @param string $url
     * @param string $username
     * @param string $password
     * @param string $appId
     * @param string|null $company
     * @param string|null $branch
     * @param string|null $module
     * @param string|null $refId
     * @param LoggerInterface|null $logger
     * @return void
     * @throws MissingArgumentException
     */
    public static function initialize(
        string $url,
        string $username,
        string $password,
        string $appId,
        string $company = null,
        string $branch = null,
        string $module = null,
        string $refId = null,
        LoggerInterface $logger = null
    )
    {
        // ensure required values given
        $requiredValues = [
            'url' => $url,
            'username' => $username,
            'password' => $password,
            'appId' => $appId,
        ];
        $missing = [];
        foreach ($requiredValues as $key => $value) {
            if (!strlen($value)) {
                $missing[] = $key;
            }
        }

        if (!empty($missing)) {
            $missing = implode(', ', $missing);
            throw new MissingArgumentException(
                "Cannot initialize Soft1 API Library. Missing values for: $missing"
            );
        }

        self::$URL = $url;
        self::$USERNAME = $username;
        self::$PASSWORD = $password;
        self::$APP_ID = $appId;
        self::$COMPANY = $company;
        self::$BRANCH = $branch;
        self::$MODULE = $module;
        self::$REFID = $refId;
        self::$LOGGER = $logger;

        self::$IS_INITIALIZED = true;
    }

    /**
     * Throws exception if initialize() has not been called
     *
     * @throws UninitializedContextException
     */
    public static function throwIfUninitialized(): void
    {
        if (!self::$IS_INITIALIZED) {
            throw new UninitializedContextException(
                'Context has not been properly initialized. ' .
                'Please call the .initialize() method to set up your app context object.'
            );
        }
    }

    /**
     * Logs a message using the defined callback. If none is set, the message is ignored.
     *
     * @param string $message The message to log
     * @param string $level One of the \Psr\Log\LogLevel::* consts, defaults to INFO
     *
     */
    public static function log(string $message, string $level = LogLevel::INFO): void
    {
//        self::throwIfUninitialized();

        if (!self::$LOGGER) {
            return;
        }

        self::$LOGGER->log($level, $message);
    }
}