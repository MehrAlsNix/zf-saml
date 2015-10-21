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

use InvalidArgumentException;
use RuntimeException;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\Controller\AbstractActionController;
use OneLogin_Saml2_Auth as SamlAuth;
use Zend\Session\Container;
use ZF\ContentNegotiation\ViewModel;

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
     * @param SamlAuth $serverFactory
     * @param UserIdProviderInterface $userIdProvider
     *
     * @throws InvalidArgumentException
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

    public function indexAction()
    {
        $session = new Container();
        if (!isset($session['samlUserdata'])) {
            $settings    = new \OneLogin_Saml2_Settings();
            $authRequest = new \OneLogin_Saml2_AuthnRequest($settings);
            $samlRequest = $authRequest->getRequest();

            $parameters  = ['SAMLRequest' => $samlRequest];
            $parameters['RelayState'] = \OneLogin_Saml2_Utils::getSelfURLNoQuery();

            $idpData = $settings->getIdPData();
            $ssoUrl  = $idpData['singleSignOnService']['url'];

            $this->redirect()->toUrl(
                \OneLogin_Saml2_Utils::redirect($ssoUrl, $parameters, true)
            );
        }

        return (new ViewModel())->setTemplate('saml/attributes');
    }

    public function ssoAction()
    {
        $session = new Container();
        $auth = new \OneLogin_Saml2_Auth();
        if (!isset($session['samlUserdata'])) {
            $auth->login();
        } else {
            $indexUrl = str_replace('/sso.php', '/index.php', \OneLogin_Saml2_Utils::getSelfURLNoQuery());
            \OneLogin_Saml2_Utils::redirect($indexUrl);
        }
    }

    public function sloAction()
    {
        $samlSettings = new \OneLogin_Saml2_Settings();
        $idpData = $samlSettings->getIdPData();
        if (isset($idpData['singleLogoutService'], $idpData['singleLogoutService']['url'])) {
            $sloUrl = $idpData['singleLogoutService']['url'];
        } else {
            throw new RuntimeException(
                'The IdP does not support Single Log Out'
            );
        }
        $session = new Container();
        if (isset($session['IdPSessionIndex']) && !empty($session['IdPSessionIndex'])) {
            $logoutRequest = new \OneLogin_Saml2_LogoutRequest($samlSettings, null, $session['IdPSessionIndex']);
        } else {
            $logoutRequest = new \OneLogin_Saml2_LogoutRequest($samlSettings);
        }
        $samlRequest = $logoutRequest->getRequest();
        $parameters = array('SAMLRequest' => $samlRequest);
        $url = \OneLogin_Saml2_Utils::redirect($sloUrl, $parameters, true);
        $this->redirect()->toUrl($url);
    }

    /**
     * Your IdP will usually want your metadata, you can use this code to
     * generate it once, or expose it on a URL so your IdP can check it
     * periodically.
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function metadataAction()
    {
        $samlSettings = new \OneLogin_Saml2_Settings();
        $sp = $samlSettings->getSPData();
        $samlMetadata = \OneLogin_Saml2_Metadata::builder($sp);

        /** @var Response $httpResponse */
        $httpResponse = $this->getResponse();
        $httpResponse->setStatusCode(200);
        $httpResponse->getHeaders()->addHeaders(['Content-type' => 'application/xml']);
        $httpResponse->setContent($samlMetadata);

        return $httpResponse;
    }

    public function consumeAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        try {
            if ($request->getPost('SAMLResponse') !== null) {
                $samlSettings = new \OneLogin_Saml2_Settings();
                $samlResponse = new \OneLogin_Saml2_Response($samlSettings, $request->getPost('SAMLResponse'));
                if ($samlResponse->isValid()) {
                    echo 'You are: ' . $samlResponse->getNameId() . '<br>';
                    $attributes = $samlResponse->getAttributes();
                    if (!empty($attributes)) {
                        echo 'You have the following attributes:<br>';
                        echo '<table><thead><th>Name</th><th>Values</th></thead><tbody>';
                        foreach ($attributes as $attributeName => $attributeValues) {
                            echo '<tr><td>' . htmlentities($attributeName) . '</td><td><ul>';
                            foreach ($attributeValues as $attributeValue) {
                                echo '<li>' . htmlentities($attributeValue) . '</li>';
                            }
                            echo '</ul></td></tr>';
                        }
                        echo '</tbody></table>';
                    }
                } else {
                    echo 'Invalid SAML Response';
                }
            } else {
                echo 'No SAML Response found in POST.';
            }
        } catch (\Exception $e) {
            echo 'Invalid SAML Response: ' . $e->getMessage();
        }
    }
}
