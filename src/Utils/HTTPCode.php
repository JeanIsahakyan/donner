<?php
namespace Donner\Utils;


/**
 * Class HTTPCode
 * @package Donner\Utils
 *
 * @author Zhan Isaakian <jeanisahakyan@gmail.com>
 */
enum HTTPCode: int {
  case CONTINUE = 100;
  case OK = 200;
  case CREATED = 201;
  case ACCEPTED = 202;
  case NON_AUTHORITATIVE = 203;
  case NO_CONTENT = 204;
  case RESET_CONTENT = 205;
  case PARTIAL_CONTENT = 206;
  case MULTIPLE_CHOICES = 300;
  case MOVED_PERMANENTLY = 301;
  case FOUND = 302;
  case SEE_OTHER = 303;
  case NOT_MODIFIED = 304;
  case USE_PROXY = 305;
  case TEMPORARY_REDIRECT = 307;
  case BAD_REQUEST = 400;
  case UNAUTHORIZED = 401;
  case PAYMENT_REQUIRED = 402;
  case FORBIDDEN = 403;
  case NOT_FOUND = 404;
  case METHOD_NOT_ALLOWED = 405;
  case NOT_ACCEPTABLE = 406;
  case CONFLICT = 409;
  case GONE = 410;
  case LENGTH_REQUIRED = 411;
  case PRECONDITION_FAILED = 412;
  case REQUEST_ENTITY_TOO_LARGE = 413;
  case REQUEST_URI_TOO_LONG = 414;
  case UNSUPPORTED_MEDIA_TYPE = 415;
  case REQUEST_RANGE_NOT_SATISFIABLE = 416;
  case EXPECTATION_FAILED = 417;
  case INTERNAL_SERVER_ERROR = 500;
  case NOT_IMPLEMENTED = 501;
  case SERVICE_UNAVAILABLE = 503;

  public static function set(HTTPCode $code): void {
    http_response_code($code->value);
  }
}
