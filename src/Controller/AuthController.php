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

use RuntimeException;
use Zend\Http\Exception\InvalidArgumentException as HttpInvalidArgumentException;
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
     * @var \OneLogin_Saml2_Settings $settings
     */
    protected $settings;

    /**
     * @var \OneLogin_Saml2_AuthnRequest
     */
    protected $authnRequest;

    /**
     * @var string
     */
    protected $metadata;

    /**
     * @var \OneLogin_Saml2_Response
     */
    protected $samlResponse;

    /**
     * @var \OneLogin_Saml2_Auth
     */
    protected $samlAuth;


    /**
     * @return \OneLogin_Saml2_Settings
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param \OneLogin_Saml2_Settings $settings
     */
    public function setSettings($settings)
    {
        if (!$settings instanceof \OneLogin_Saml2_Settings) {
            $this->settings = $settings;
        }
    }

    /**
     * @return \OneLogin_Saml2_AuthnRequest
     */
    public function getAuthnRequest()
    {
        return $this->authnRequest;
    }

    /**
     * @param \OneLogin_Saml2_AuthnRequest $authnRequest
     */
    public function setAuthnRequest($authnRequest)
    {
        $this->authnRequest = $authnRequest;
    }

    /**
     * @return string
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return \OneLogin_Saml2_Response
     */
    public function getSamlResponse()
    {
        return $this->samlResponse;
    }

    /**
     * @return \OneLogin_Saml2_Auth
     */
    public function getSamlAuth()
    {
        return $this->samlAuth;
    }

    /**
     * @param string $metadata
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
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
            $settings    = $this->settings;
            $authRequest = $this->authnRequest;
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
        $auth = $this->getSamlAuth();
        if (!isset($session['samlUserdata'])) {
            $auth->login();
        } else {
            $indexUrl = str_replace('/sso.php', '/index.php', \OneLogin_Saml2_Utils::getSelfURLNoQuery());
            \OneLogin_Saml2_Utils::redirect($indexUrl);
        }
    }

    public function sloAction()
    {
        $samlSettings = $this->settings;
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
        $parameters = ['SAMLRequest' => $samlRequest];
        $url = \OneLogin_Saml2_Utils::redirect($sloUrl, $parameters, true);
        $this->redirect()->toUrl($url);
    }

    /**
     * Your IdP will usually want your metadata, you can use this code to
     * generate it once, or expose it on a URL so your IdP can check it
     * periodically.
     *
     * @return \Zend\Stdlib\ResponseInterface
     *
     * @throws Exception\RuntimeException
     */
    public function metadataAction()
    {
        /** @var Response $httpResponse */
        $httpResponse = $this->getResponse();
        try {
            $httpResponse->setStatusCode(200);
            $httpResponse->getHeaders()->addHeaders(['Content-type' => 'application/xml']);
        } catch (HttpInvalidArgumentException $e) {
            throw new Exception\RuntimeException($e);
        }
        $httpResponse->setContent($this->metadata);

        return $httpResponse;
    }

    public function consumeAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        try {
            if ($request->getPost('SAMLResponse') !== null) {
                $samlResponse = $this->getSamlResponse();
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
