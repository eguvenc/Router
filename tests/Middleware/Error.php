<?php

namespace Tests\Middleware;

use Throwable;
use Exception;
use RuntimeException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Zend\Diactoros\Response\HtmlResponse;

class Error implements MiddlewareInterface
{
    protected $err;
    protected $errResponse;

    /**
     * Constructor
     * 
     * @param string $err error
     */
    public function __construct($err = null)
    {
        if (is_string($err)) {
            switch ($err) {
                case '404':
                $this->errResponse = new HtmlResponse($this->render404Error(), 404);
                    break;
                default:
                $this->errResponse = new HtmlResponse($this->renderHtmlErrorMessage($err), 404);
                    break;
            }
        }
    }

    /**
     * Process request
     *
     * @param ServerRequestInterface  $request  request
     * @param RequestHandlerInterface $handler
     *
     * @return object ResponseInterface
     */
    public function process(Request $request, RequestHandler $handler) : ResponseInterface
    {
        if ($this->errResponse != null) {
            return $this->errResponse;
        }
        try {
            $response = $handler->handle($request);
        } catch (Throwable $throwable) { 
            $response = $this->handleError($throwable);
        } catch (Exception $exception) {
            $response = $this->handleError($throwable);
        }
        return $response;
    }

    /**
     * Handle application errors
     *
     * @param mixed $error mostly exception object
     *
     * @return object response
     */
    protected function handleError($error)
    {
        $html = $this->renderHtmlErrorMessage($error);
        // $json = $this->renderJsonErrorMessage($error);
        
        if (is_object($error)) {
            switch ($error) {
                case ($error instanceof Throwable):
                case ($error instanceof Exception):
                case ($error instanceof RuntimeException):
                    // error log
                    break;
            }
        }
        // return new JsonResponse($json, 500, [], JSON_PRETTY_PRINT);

        return new HtmlResponse($html, 500);
    }

    /**
     * Page not found
     * 
     * @return string
     */
    protected function render404Error()
    {
        $html = '<html>
        <head>
        <title>404 Page Not Found</title>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <style type="text/css">
        body{ color: #777575 !important; margin:0 !important; padding:20px !important; font-family:Arial,Verdana,sans-serif !important;font-weight:normal;  }
        h1, h2, h3, h4 {
            margin: 0;
            padding: 0;
            font-weight: normal;
            line-height:48px;
        }
        </style>
        </head>
        <body>

        <h1>404 Not Found</h1>
        <p>The page you are looking for could not be found.</p>

        </body>
        </html>';
        return $html;
    }

    /**
     * Render HTML error page
     *
     * @param error $error error | exception
     *
     * @return string
     */
    protected function renderHtmlErrorMessage($error)
    {
        $html = null;
        if (is_string($error)) {
            $html  = '<h3>'.$error.'</h3>';
            $title = 'Error';
        } elseif (is_object($error)) {
            $title = 'Server Error';
            $html = $this->renderHtmlException($error);

            // Don't use $exception->getPrevious() if exception object large you can not display it !
        }
        $header = '<style>
        body{ color: #777575 !important; margin:0 !important; padding:20px !important; font-family:Arial,Verdana,sans-serif !important;font-weight:normal;  }
        h1, h2, h3, h4 {
            margin: 0;
            padding: 0;
            font-weight: normal;
            line-height:48px;
        }
        </style>';

        $output = sprintf(
            "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
            "<title>%s</title>%s</head><body><h1>%s</h1>%s</body></html>",
            $title,
            $header,
            $title,
            $html
        );

        return $output;
    }

    /**
     * Render exception as HTML.
     *
     * @param Exception $exception exception
     *
     * @return string
     */
    protected function renderHtmlException(Throwable $exception)
    {
        $html = sprintf('<tr><td style="width:%s">Type</td><td>%s</td></tr>', '15%', get_class($exception));

        if (($message = $exception->getMessage())) {
            $html .= sprintf('<tr><td>Message</td><td>%s</td></tr>', $message);
        }

        if (($code = $exception->getCode())) {
            $html .= sprintf('<tr><td>Code</td><td>%s</td></tr>', $code);
        }

        if (($file = $exception->getFile())) {
            $html .= sprintf('<tr><td>File</td><td>%s</td></tr>', $file);
        }

        if (($line = $exception->getLine())) {
            $html .= sprintf('<tr><td>Line</td><td>%s</td></tr>', $line);
        }
        $html = "<table>".$html."</table>";

        if (($trace = $exception->getTraceAsString())) {
            $html .= '<h2>Trace</h2>';
            $html .= sprintf('<pre>%s</pre>', htmlentities($trace));
        }
        
        return $html;
    }

    /**
     * Render JSON error
     *
     * @param Exception $exception exception
     *
     * @return string
     */
    protected function renderJsonErrorMessage(Throwable $exception)
    {
        $error = [
            "success" => 0,
            'message' => 'Rest Api Error',
        ];
        $error['exception'] = [
            'type' => get_class($exception),
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => explode("\n", $exception->getTraceAsString()),
        ];

        // Don't use $exception->getPrevious() if exception object large you can not display it !
    
        return $error;
    }
}
