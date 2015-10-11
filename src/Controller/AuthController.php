<?php
/**
 * ZF-SAML
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @copyright 2015 MehrAlsNix (http://www.mehralsnix.de)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://github.com/MehrAlsNix/zf-saml
 */

namespace MehrAlsNix\ZF\SAML\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use OneLogin_Saml2_Auth as SamlAuth;
use OneLogin_Saml2_Response as SamlResponse;
use OneLogin_Saml_AuthRequest as SamlRequest;

class AuthController extends AbstractActionController
{
    /**
     * @var boolean
     */
    protected $apiProblemErrorResponse = true;

    /**
     * @var SamlAuth
     */
    protected $server;

    /**
     * @var callable Factory for generating an OAuth2Server instance.
     */
    protected $serverFactory;

    /**
     * @var UserIdProviderInterface
     */
    protected $userIdProvider;

    /**
     * Constructor
     *
     * @param SamlAuth $server
     * @param UserIdProviderInterface $userIdProvider
     */
    public function __construct($serverFactory, UserIdProviderInterface $userIdProvider)
    {
        if (! is_callable($serverFactory)) {
            throw new InvalidArgumentException(sprintf(
                'OAuth2 Server factory must be a PHP callable; received %s',
                (is_object($serverFactory) ? get_class($serverFactory) : gettype($serverFactory))
            ));
        }
        $this->serverFactory  = $serverFactory;
        $this->userIdProvider = $userIdProvider;
    }
    /**
     * Should the controller return ApiProblemResponse?
     *
     * @return bool
     */
    public function isApiProblemErrorResponse()
    {
        return $this->apiProblemErrorResponse;
    }

    /**
     * Indicate whether ApiProblemResponse or oauth2 errors should be returned.
     *
     * Boolean true indicates ApiProblemResponse should be returned (the
     * default), while false indicates oauth2 errors (per the oauth2 spec)
     * should be returned.
     *
     * @param bool $apiProblemErrorResponse
     */
    public function setApiProblemErrorResponse($apiProblemErrorResponse)
    {
        $this->apiProblemErrorResponse = (bool) $apiProblemErrorResponse;
    }
}
