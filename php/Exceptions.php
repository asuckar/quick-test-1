<?php

    define('MYSQL_INVALIDMYSQLCOMMAND_EXCEPTION', 0xF0000001);
    define('FILE_INVALIDFILECONTENT_EXCEPTION', 0xF0000002);
    define('FILE_INVALIDFILEFORMAT_EXCEPTION', 0xF0000003);
    define('FILE_INVALIDINPUT_EXCEPTION', 0xF0000004);
    define('FILE_INVALIDCSV_EXCEPTION', 0xF0000005);
    define('FILE_INVALIDXML_EXCEPTION', 0xF0000006);
    define('FILE_INTERNALSERVER_EXCEPTION', 0xF0000007);

    class Nu3Exception extends Exception {
        protected $errorCode;
        protected $errorMessage;

        public function __construct($message) {
            parent::__construct($message);
            $errorMessage=$message;
        }
        public function getErrorCode() {
            return $errorCode;
        }

        public function getErrorMessage() {
            return $errorMessage;
        }
    }

    final class InvalidMySQLCommandException extends Nu3Exception {

        public function __construct($message) {
            parent::__construct($message);
            $errorCode = MYSQL_INVALIDMYSQLCOMMAND_EXCEPTION;
        }
    };

    final class InvalidFileContentException extends Nu3Exception {

        public function __construct($message) {
            parent::__construct($message);
            $errorCode = FILE_INVALIDFILECONTENT_EXCEPTION;
          }
    };

    final class InvalidFileFormatException extends Nu3Exception {

        public function __construct($message) {
            parent::__construct($message);
            $errorCode = FILE_INVALIDFILEFORMAT_EXCEPTION;
          }
    };

    final class InvalidInputException extends Nu3Exception {

        public function __construct($message) {
            parent::__construct($message);
            $errorCode = FILE_INVALIDINPUT_EXCEPTION;
          }
    };

    final class InvalidCSVException extends Nu3Exception {

        public function __construct($message) {
            parent::__construct($message);
            $errorCode = FILE_INVALIDCSV_EXCEPTION;
          }
    };

    final class InvalidXMLException extends Nu3Exception {

        public function __construct($message) {
            parent::__construct($message);
            $errorCode = FILE_INVALIDXML_EXCEPTION;
          }
    };

    final class InternalServerException extends Nu3Exception {

        public function __construct($message) {
            parent::__construct($message);
            $errorCode = FILE_INTERNALSERVER_EXCEPTION;
          }
    };


