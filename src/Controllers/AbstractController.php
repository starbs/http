<?php

/*
 * This file is part of Starbs Http by Graham Campbell.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Starbs\Http\Controllers;

use Orno\Di\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController
{
    /**
     * The container instance.
     *
     * @var \Orno\Di\ContainerInterface
     */
    protected $container;

    /**
     * The request instance.
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * The response instance.
     *
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $response;

    /**
     * The arguments.
     *
     * @var string[]
     */
    protected $args;

    /**
     * Create a new http controller instance.
     *
     * @param \Orno\Di\ContainerInterface $container
     *
     * @return void
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Setup the controller, then run the fire method.
     *
     * @param \Symfony\Component\HttpFoundation\Request  $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param string[]                                   $args
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, Response $response, array $args)
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;

        return $this->fire();
    }

    /**
     * Do some clever things, then return a response.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    abstract protected function fire();

    /**
     * Get a success response json.
     *
     * @param string[] $data
     * @param int      $code
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function success(array $data, $code = 200)
    {
        $this->response->setStatusCode($code);
        $this->response->headers->add(['Content-Type' => 'application/json']);
        $this->response->setContent(json_encode(['success' => $data], JSON_PRETTY_PRINT));

        return $this->response;
    }

    /**
     * Get a redirection response
     *
     * @param string $url
     * @param int    $code
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function redirect($url, $code = 302)
    {
        $this->response->setStatusCode($code);
        $this->response->headers->set('Location', $url);

        return $this->response;
    }

    /**
     * Get an error response json.
     *
     * @param string[] $data
     * @param int      $code
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function error(array $data, $code = 500)
    {
        $this->response->setStatusCode($code);
        $this->response->headers->add(['Content-Type' => 'application/json']);
        $this->response->setContent(json_encode(['error' => $data], JSON_PRETTY_PRINT));

        return $this->response;
    }

    /**
     * Get a custom response.
     *
     * @param string $data
     * @param string $mime
     * @param int    $code
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function raw($data, $mime, $code = 200)
    {
        $this->response->setStatusCode($code);
        $this->response->headers->add(['Content-Type' => $mime]);
        $this->response->setContent($data);

        return $this->response;
    }

    /**
     * Get an item from the user input.
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function input($key)
    {
        return $this->request->request->get($key);
    }

    /**
     * Get a file from the user input.
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function file($key)
    {
        return $this->request->files->get($key);
    }
}
