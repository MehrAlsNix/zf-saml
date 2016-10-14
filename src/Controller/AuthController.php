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
use Zend\Session\Container;
use ZF\ContentNegotiation\ViewModel;

/**
 * Class AuthController
 * @package MehrAlsNix\ZF\SAML\Controller
 */
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
     * @return \OneLogin_Saml2_AuthnRequest
     */
    public function getAuthnRequest()
    {
        return $this->authnRequest;
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
     * @param bool $apiProblemErrorResponse `true`  for ApiProblemResponse
     *                                       false` for oauth2 errors
     */
    public function setApiProblemErrorResponse($apiProblemErrorResponse)
    {
        $this->apiProblemErrorResponse = (bool) $apiProblemErrorResponse;
    }

    /**
     * @return \Zend\View\Model\ViewModel
     * @throws \OneLogin_Saml2_Error
     * @throws \Zend\Session\Exception\InvalidArgumentException
     */
    public function indexAction()
    {
        $session = new Container();
        if (!isset($session['samlUserdata'])) {
            $settings    = $this->settings;
            $authRequest = $this->authnRequest;
            $samlRequest = $authRequest->getRequest();

            $parameters  = ['SAMLRequest' => $samlRequest];
            $parameters['RelayState'] = \OneLogin_Saml2_Utils::getSelfURLNoQuery();

            $ssoUrl  = $settings->getIdPData()['singleSignOnService']['url'];

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
     * @return \Zend\Mvc\Controller\Plugin\FlashMessenger|\Zend\View\Model\ViewModel
     * @throws \OneLogin_Saml2_Error
     * @throws \Zend\Session\Exception\InvalidArgumentException
     */
    public function acsAction()
    {
        $auth = $this->getSamlAuth();
        $auth->processResponse();
        $errors = $auth->getErrors();
        if (count($errors) > 0) {
            return $this->flashMessenger()->addErrorMessage(implode(', ', $errors));
        }
        if (!$auth->isAuthenticated()) {
            return $this->flashMessenger()->addWarningMessage('Not authenticated');
        }
        $session = new Container();
        $session['samlUserdata']    = $auth->getAttributes();
        $session['IdPSessionIndex'] = $auth->getSessionIndex();

        $relayState = $this->getRequest()->getPost('RelayState');
        if ($relayState !== null
            && $relayState !== \OneLogin_Saml2_Utils::getSelfURL()
        ) {
            $auth->redirectTo($relayState);
        }

        return (new ViewModel())->setTemplate('saml/attributes');
    }

    /**
     * @return \Zend\Mvc\Controller\Plugin\FlashMessenger
     * @throws \OneLogin_Saml2_Error
     */
    public function slsAction()
    {
        $auth = $this->getSamlAuth();
        $auth->processSLO();
        $errors = $auth->getErrors();
        if (0 === count($errors)) {
            return $this->flashMessenger()
                ->addSuccessMessage('Sucessfully logged out');
        }

        return $this->flashMessenger()->addErrorMessage(implode(', ', $errors));
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
            $httpResponse->getHeaders()
                ->addHeaders(['Content-type' => 'application/xml']);
        } catch (HttpInvalidArgumentException $e) {
            throw new Exception\RuntimeException($e);
        }
        $httpResponse->setContent($this->metadata);

        return $httpResponse;
    }

    /**
     * This action will have been given during the SAML authorization.
     * After a successful authorization, the browser will be directed to this
     * link where it will send a certified response via $_POST.
     *
     * @return \Zend\View\Model\ViewModel
     * @throws \Exception
     */
    public function consumeAction()
    {
        /* @var Request $request */
        $request = $this->getRequest();
        $templateVars = [];
        $templateVars['isSamlResponse'] = (bool) $request
            ->getPost('SAMLResponse');
        $templateVars['isValidSamlResponse'] = $this->getSamlResponse()
            ->isValid();
        $templateVars['nameId'] = $this->getSamlResponse()->getNameId();
        $templateVars['attributes'] = $this->getSamlResponse()
            ->getAttributes();

        return (new ViewModel($templateVars))->setTemplate('saml/consumer');
    }
}
