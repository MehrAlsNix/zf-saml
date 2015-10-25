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

namespace MehrAlsNix\ZF\SAML\Factory;

use MehrAlsNix\ZF\SAML\Controller\AuthController;
use OneLogin_Saml2_Auth as SamlServer;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthControllerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $controllers
     * @return AuthController
     */
    public function createService(ServiceLocatorInterface $controllers)
    {
        $services = $controllers->getServiceLocator()->get('ServiceManager');
        $authController = new AuthController();
        $authController->setSettings($services->get('MehrAlsNix\ZF\SAML\Service\SAML2Settings'));
        $authController->setAuthnRequest($services->get('MehrAlsNix\ZF\SAML\Service\SAML2AuthnRequest'));
        $authController->setMetadata($services->get('MehrAlsNix\ZF\SAML\Service\SAML2Metadata'));

        $config = $services->get('Config');
        $authController->setApiProblemErrorResponse((isset($config['zf-saml']['api_problem_error_response'])
            && $config['zf-saml']['api_problem_error_response'] === true));
        return $authController;
    }
}
