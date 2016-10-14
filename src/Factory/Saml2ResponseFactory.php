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

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use MehrAlsNix\ZF\SAML\Controller\Exception;

/**
 * Class Saml2ResponseFactory
 * @package MehrAlsNix\ZF\SAML\Factory
 */
class Saml2ResponseFactory implements FactoryInterface
{
    /**
     * @var \OneLogin_Saml2_Response
     */
    private $response;

    /**
     * Create an \OneLogin_Saml2_AuthnRequest instance.
     *
     * @param ServiceLocatorInterface $services
     *
     * @return \OneLogin_Saml2_Response
     *
     * @throws Exception\RuntimeException
     */
    public function createService(ServiceLocatorInterface $services)
    {
        if ($this->response) {
            return $this->response;
        }

        try {
            /* @var \OneLogin_Saml2_Settings $settings */
            $settings = $services->get('MehrAlsNix\ZF\SAML\Service\SAML2Settings');
            $request = $services->get('Request');
            return $this->response = new \OneLogin_Saml2_Response(
                $settings,
                $request->getPost('SAMLResponse')
            );
        } catch (\Exception $e) {
            throw new Exception\RuntimeException($e);
        }
    }
}
